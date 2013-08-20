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

namespace OCA\Friends\Lib;

require_once(__DIR__ . "/../classloader.php");

use OCA\Friends\Db\Friendship;

class HooksTest extends \PHPUnit_Framework_TestCase {
	private $api;
	private $mapper;
	private $row1;
	private $row2;
	private $row3;
	private $row4;

	protected function setUp(){
$this->api = $this->getMock('OCA\Friends\Core\FriendsApi', array('prepareQuery', 'getTime', 'log', 'isAppEnabled', 'userExists', 'emitHook'), array('friends'));
                $this->mapper = new FriendshipMapper($this->api);
                $this->row1 = array(
                        //'friend_uid1' => 'thisisuser1',
                        //'friend_uid2' => 'thisisuser2'
                        'friend' => 'thisisuser2'
                );
                $this->row2 = array(
                        //'friend_uid1' => 'thisisuser3',
                        //'friend_uid2' => 'thisisuser1'
                        'friend' => 'thisisuser3'
                );
                $this->row3 = array(
                        'friend_uid1' => 'thisisuser1',
                        'friend_uid2' => 'thisisuser2',
                        'updated_at' => 'sometime',
                        'status' => Friendship::ACCEPTED
                );
                $this->row4 = array(
                        'friend_uid1' => 'thisisuser1',
                        'friend_uid2' => 'thisisuser2',
                        'updated_at' => 'sometime',
                        'status' => Friendship::ACCEPTED
                );

	}

	public function testDeleteUser($params){

		//Setup
		$friendshipMapper = $this->mapper;
		$uid = $params['uid'];
		$friendship1 = $friendshipMapper->create($this->row3);
		$friendship2 = $friendshipMapper->create($this->row4);
		$friendshiparray = array($friendship1, $friendship2);

		// Assertion
		$friendshipMapper->expects($this->at(0))
					->method('findAllFriendsByUser')
					->with($uid)
					->will($this->returnsValue($friendshiparray));

		$friendshipMapper->expects($this->at(1))
                                        ->method('delete')
                                        ->with($friendship1)
                                        ->will($this->returnsValue(true));
		
		$friendshipMapper->expects($this->at(2))
                                        ->method('delete')
                                        ->with($friendship2)
                                        ->will($this->returnsValue(true));

		//FriendsHooks::postDeleteUserDeletesFriendships($uid);
	}

}

