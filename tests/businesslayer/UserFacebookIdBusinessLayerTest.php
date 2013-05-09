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

namespace OCA\Friends\BusinessLayer;

use OCA\AppFramework\Http\Request;
use OCA\AppFramework\Db\DoesNotExistException;
use OCA\AppFramework\Utility\ControllerTestUtility;

use OCA\Friends\Db\Friendship;
use OCA\Friends\Db\UserFacebookId;

require_once(__DIR__ . "/../classloader.php");


class UserFacebookIdBusinessLayerTest extends \OCA\AppFramework\Utility\TestUtility {


	public function testCreateFriendsFromFacebookFriendsList(){
		$api = $this->getAPIMock('OCA\Friends\Core\FriendsAPI');
		$friendshipMapperMock = $this->getMock('\OCA\Friends\Db\FriendshipMapper', array('exists'), array($api));
		$userFacebookIdMapperMock = $this->getMock('\OCA\Friends\Db\UserFacebookIdMapper', array('exists', 'save', 'find', 'findByFacebookId', 'updateSyncTime'), array($api));
		$userFacebookIdBusinessLayerMock = $this->getMock('\OCA\Friends\BusinessLayer\UserFacebookIdBusinessLayer', array('createFriendsFromFacebookFriendsList'), array($api, $friendshipMapperMock, $userFacebookIdMapperMock));

		$businessLayer = new UserFacebookIdBusinessLayer($api, $friendshipMapperMock, $userFacebookIdMapperMock);

		$currentUser = 'Sarah';
		$data = array(
			(object) array("name" => "Ryan", "id" => "12345"),
			(object) array("name" => "Melissa", "id" => "12346"),
			(object) array("name" => "John", "id" => "12347"),
			(object) array("name" => "Mallory", "id" => "12348")
		);
		

		$userFacebookIdMapperMock->expects($this->at(0))
						->method('findByFacebookId')
						->with($this->equalTo("12345"))
						->will($this->throwException(new DoesNotExistException('')));  //Test failure
		$userFacebookIdMelissa = new UserFacebookId();
		$userFacebookIdMelissa->setFacebookId("12346");
		$userFacebookIdMelissa->setUid("Melissa");
		$userFacebookIdMapperMock->expects($this->at(1))
						->method('findByFacebookId')
						->with($this->equalTo("12346"))
						->will($this->returnValue($userFacebookIdMelissa));
		$api->expects($this->at(0))
					->method('beginTransaction');
		$api->expects($this->at(1))
					->method('userExists')
					->with($this->equalTo("Melissa"))
					->will($this->returnValue(false));
		$api->expects($this->at(2))
					->method('log');
		$api->expects($this->at(3))
					->method('commit');
		//There should be no more calls for this user

		$userFacebookIdJohn = new UserFacebookId();
		$userFacebookIdJohn->setFacebookId("12347");
		$userFacebookIdJohn->setUid("John");
		$userFacebookIdMapperMock->expects($this->at(2))
						->method('findByFacebookId')
						->with($this->equalTo("12347"))
						->will($this->returnValue($userFacebookIdJohn));
		$api->expects($this->at(4))
					->method('beginTransaction');
		$api->expects($this->at(5))
					->method('userExists')
					->with($this->equalTo("John"))
					->will($this->returnValue(true));
		$friendshipMapperMock->expects($this->any()) //all of the remaining users will exist
					->method('exists')
					->will($this->returnValue(true)); //assuming saved, not really worth testing
		$api->expects($this->at(6))
					->method('commit');
		$userFacebookIdMallory = new UserFacebookId();
		$userFacebookIdMallory->setFacebookId("12348");
		$userFacebookIdMallory->setUid("Mallory");
		$userFacebookIdMapperMock->expects($this->at(3))
						->method('findByFacebookId')
						->with($this->equalTo("12348"))
						->will($this->returnValue($userFacebookIdMallory));
		$api->expects($this->at(7))
					->method('beginTransaction');
		$api->expects($this->at(8))
					->method('userExists')
					->with($this->equalTo("Mallory"))
					->will($this->returnValue(true));
		$api->expects($this->at(9))
					->method('commit');

		$userFacebookIdMapperMock->expects($this->once())
					->method('updateSyncTime')
					->will($this->returnValue(true));

		$businessLayer->createFriendsFromFacebookFriendsList($currentUser, $data);

	}

}
