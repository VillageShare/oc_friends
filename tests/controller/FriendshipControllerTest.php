<?php

/**
* ownCloud - App Template plugin
*
* @author Bernhard Posselt
* @copyright 2012 Bernhard Posselt nukeawhale@gmail.com
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\Friends\Controller;

use OCA\AppFramework\Http\Request;
use OCA\AppFramework\Db\DoesNotExistException;
use OCA\AppFramework\Utility\ControllerTestUtility;

use OCA\Friends\Db\Friendship;
use OCA\Friends\Db\UserFacebookId;

require_once(__DIR__ . "/../classloader.php");


class FriendshipControllerTest extends ControllerTestUtility {


	public function testRedirectToIndexAnnotations(){
		$api = $this->getAPIMock();
		$controller = new FriendshipController($api, new Request(), null, null, null, null);
		$methodName = 'redirectToIndex';
		$annotations = array('CSRFExemption', 'IsAdminExemption', 'IsSubAdminExemption');

		$this->assertAnnotations($controller, $methodName, $annotations);
	}


	public function testIndexAnnotations(){
		$api = $this->getAPIMock();
		$controller = new FriendshipController($api, new Request(), null, null, null, null);
		$methodName = 'index';
		$annotations = array('CSRFExemption', 'IsAdminExemption', 'IsSubAdminExemption');

		$this->assertAnnotations($controller, $methodName, $annotations);
	}


	public function testIndex(){
		$api = $this->getAPIMock();

		$controller = new FriendshipController($api, new Request(), null, null, null, null);

		$response = $controller->index();
		$this->assertEquals('main', $response->getTemplateName());
	}



	public function testFacebookSyncFirstLoad(){
		$api = $this->getAPIMock();
		$userFacebookIdMapperMock = $this->getMock('UserFacebookIdMapper', array('exists', 'save', 'find'));
		$controller = new FriendshipController($api, new Request(), null, $userFacebookIdMapperMock, null);

		$api->expects($this->at(0))
					->method('getUserId')
					->will($this->returnValue('Sarah')); //current user

		$userFacebookIdMapperMock->expects($this->once())
					->method('find')
					->with($this->equalTo('Sarah'))
					->will($this->throwException(new DoesNotExistException("")));

		$response = $controller->facebookSync();
		$params = $response->getParams();
		$this->assertRegExp('/.*facebook\.com\/dialog\//', $params['fb_dialog_url']);
	}

	//Redirected back from Facebook after permissions request
	public function testFacebookSyncResponseRedirectUserSync(){
		$api = $this->getAPIMock('OCA\Friends\Core\FriendsAPI');
		$friendshipMapperMock = $this->getMock('FriendshipMapper', array('exists'));
		$userFacebookIdMapperMock = $this->getMock('UserFacebookIdMapper', array('exists', 'save', 'find'));

		$controller = new FriendshipController($api, new Request(), $friendshipMapperMock, $userFacebookIdMapperMock, null);
		$controller->my_url = "http://myfakeurl.com/index.php";
		$controller->app_id = "myid";
		$controller->app_secret = "mysecret";
		
		$api->expects($this->at(0))
					->method('getUserId')
					->will($this->returnValue('Sarah')); //current user
		
		$userFacebookIdMapperMock->expects($this->once())
					->method('exists')
					->with('Sarah')
					->will($this->returnValue(false));

		$tokenUrl = "https://graph.facebook.com/oauth/access_token?"
                                        . "client_id=myid&redirect_uri=" . urlencode($controller->my_url)
                                        . "&client_secret=mysecret&code=mycode";
		$fetchedAccessToken = 'access_token=AAAFji9Iq0fQBSyTFp8MXJhTWC4axsdp8S5RIdZBRvDndgZDZD&expires=5178096';
		$api->expects($this->at(1))
						->method('fileGetContents')
						->with($this->equalTo($tokenUrl))
						->will($this->returnValue($fetchedAccessToken));

		$graphUrl = "https://graph.facebook.com/me?access_token=AAAFji9Iq0fQBSyTFp8MXJhTWC4axsdp8S5RIdZBRvDndgZDZD";
		$fetchedMeData = '{"id":"1234","name":"Sarah Jones","first_name":"Sarah","last_name":"Jones","link":"http:\\/\\/www.facebook.com\\/profile.php?id=1234","gender":"female","timezone":0,"locale":"","verified":true,"updated_time":"2020-12-05T23:13:36+0000"}';
		$api->expects($this->at(2))
						->method('fileGetContents')
						->with($this->equalTo($graphUrl))
						->will($this->returnValue($fetchedMeData));

		$userFacebookIdMapperMock->expects($this->once())
					->method('save')
					->will($this->returnValue(true));

		$userFacebookIdObj = new UserFacebookId();
		$userFacebookIdObj->setFacebookName('Sarah J');
		$userFacebookIdObj->setFacebookId('1234');

		$_REQUEST = array(
			'code' => 'mycode',
			'state' => 'myfakestate'
		);
		$_SESSION = array(
			'state' => 'myfakestate'
		);


		$response = $controller->facebookSync();
	}


	public function testFacebookSyncResponseRedirectFriendsSync(){
		$api = $this->getAPIMock('OCA\Friends\Core\FriendsAPI');
		$friendshipMapperMock = $this->getMock('\OCA\Friends\Db\FriendshipMapper', array('exists'), array($api));
		$userFacebookIdMapperMock = $this->getMock('\OCA\Friends\Db\UserFacebookIdMapper', array('exists', 'save', 'find', 'findByFacebookId', 'updateSyncTime'), array($api));
		$userFacebookIdBusinessLayerMock = $this->getMock('\OCA\Friends\BusinessLayer\UserFacebookIdBusinessLayer', array('createFriendsFromFacebookFriendsList'), array($api, $friendshipMapperMock, $userFacebookIdMapperMock));

		$controller = new FriendshipController($api, new Request(), $friendshipMapperMock, $userFacebookIdMapperMock, $userFacebookIdBusinessLayerMock);
		$controller->my_url = "http://myfakeurl.com/index.php";
		$controller->app_id = "myid";
		$controller->app_secret = "mysecret";
		
		$api->expects($this->at(0))
					->method('getUserId')
					->will($this->returnValue('Sarah')); //current user

		$userFacebookIdMapperMock->expects($this->once())
					->method('exists')
					->with('Sarah')
					->will($this->returnValue(true));

		$tokenUrl = "https://graph.facebook.com/oauth/access_token?"
                                        . "client_id=myid&redirect_uri=" . urlencode($controller->my_url)
                                        . "&client_secret=mysecret&code=mycode";
		$fetchedAccessToken = 'access_token=AAAFji9Iq0fQBSyTFp8MXJhTWC4axsdp8S5RIdZBRvDndgZDZD&expires=5178096';
		$api->expects($this->at(1))
						->method('fileGetContents')
						->with($this->equalTo($tokenUrl))
						->will($this->returnValue($fetchedAccessToken));

		$graphUrl = "https://graph.facebook.com/me/friends?access_token=AAAFji9Iq0fQBSyTFp8MXJhTWC4axsdp8S5RIdZBRvDndgZDZD";
		$fetchedFriendData = '{"data":[{"name":"Ryan","id":"12345"},{"name":"Melissa","id":"12346"},{"name":"John","id":"12347"},{"name":"Mallory","id":"12348"}]}';
		$api->expects($this->at(2))
						->method('fileGetContents')
						->with($this->equalTo($graphUrl))
						->will($this->returnValue($fetchedFriendData));

		$data = array(
			(object) array("name" => "Ryan", "id" => "12345"),
			(object) array("name" => "Melissa", "id" => "12346"),
			(object) array("name" => "John", "id" => "12347"),
			(object) array("name" => "Mallory", "id" => "12348")
		);
		$userFacebookIdBusinessLayerMock->expects($this->once())
						->method('createFriendsFromFacebookFriendsList')
						->with($this->equalTo('Sarah'), $this->equalTo($data));
					
		
		$userFacebookIdMapperMock->expects($this->at(0))
						->method('exists')
						->with($this->equalTo("Sarah", "1234"))
						->will($this->returnValue(true)); //assuming already saved, not really worth testing, just constructor and save

		$userFacebookIdObj = new UserFacebookId();
		$userFacebookIdObj->setFacebookName('Sarah J');
		$userFacebookIdObj->setFacebookId('1234');
		$userFacebookIdMapperMock->expects($this->once())
					->method('find')
					->with($this->equalTo('Sarah'))
					->will($this->returnValue($userFacebookIdObj));

		$_REQUEST = array(
			'code' => 'mycode',
			'state' => 'myfakestate'
		);
		$_SESSION = array(
			'state' => 'myfakestate'
		);


		$response = $controller->facebookSync();

	}

}
