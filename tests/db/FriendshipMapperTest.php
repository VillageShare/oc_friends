<?php

/**
 * ownCloud - App Framework
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


namespace OCA\Friends\Db;

require_once(__DIR__ . "/../classloader.php");

use OCA\Friends\Db\AlreadyExistsException as AlreadyExistsException;
use OCA\Friends\Db\Friendship;

class FriendshipMapperTest extends \PHPUnit_Framework_TestCase {

	private $api;

	protected function setUp(){
		$this->api = $this->getMock('OCA\Friends\Core\FriendsApi', array('prepareQuery', 'getTime', 'log', 'multiInstanceEnabled'), array('friends'));
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
			'status' => Friendship::DELETED
		);
	}



 	public function testFindAllFriendsByUser(){
		$userId = 'thisisuser1';
		$expected = 'SELECT friend_uid2 as friend FROM `*PREFIX*friends_friendships` WHERE (friend_uid1 = ? AND status = ?)
			UNION
			SELECT friend_uid1 as friend FROM `*PREFIX*friends_friendships` WHERE (friend_uid2 = ? AND status = ?)';	

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
			->method('fetchRow')
			->will($this->returnValue($this->row1));
		$cursor->expects($this->at(1))
			->method('fetchRow')
			->will($this->returnValue($this->row2));
		$cursor->expects($this->at(2))
			->method('fetchRow')
			->will($this->returnValue(false));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo(array($userId, Friendship::ACCEPTED, $userId, Friendship::ACCEPTED)))
			->will($this->returnValue($cursor));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$friendships = $this->mapper->findAllFriendsByUser($userId);

		$this->assertEquals(array('thisisuser2', 'thisisuser3'), $friendships);

 	}

	public function testFind(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array($userId1, $userId2); //should be in alphanumeric order

		$expected = 'SELECT * FROM `*PREFIX*friends_friendships` WHERE friend_uid1 = ? AND friend_uid2 = ?';

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
			->method('fetchRow')
			->will($this->returnValue($this->row3));
		$cursor->expects($this->at(1))
			->method('fetchRow')
			->will($this->returnValue(false));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue($cursor));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$friendship = $this->mapper->find($userId2, $userId1); //send in not alphanumeric order
		$this->assertEquals($userId1, $friendship->getUid1());
		$this->assertEquals($userId2, $friendship->getUid2());
	}

	/* Friendship does not exist */
	public function testFindNotExist(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2'; 
		$expected = 'SELECT * FROM `*PREFIX*friends_friendships` WHERE friend_uid1 = ? AND friend_uid2 = ?';

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->once())
			->method('fetchRow')
			->will($this->returnValue(false));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo(array($userId1, $userId2))) //must be alphanumeric
			->will($this->returnValue($cursor));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$this->setExpectedException('OCA\AppFramework\Db\DoesNotExistException');

		$this->mapper->find($userId2, $userId1);
	}

	public function testExists(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array($userId2, $userId1);

		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('find'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('find')
			->with($userId1, $userId2)
			->will($this->returnValue(true));
		

		$result = $friendshipMapper->exists($userId1, $userId2);
		$this->assertEquals(true, $result);
	}


	public function testAccept(){

		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array(Friendship::ACCEPTED, 'timestamp', $userId1, $userId2);
		$expected = 'UPDATE `*PREFIX*friends_friendships` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?)';
		
		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('exists'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('exists')
			->with($userId1, $userId2)
			->will($this->returnValue(true));


		$this->api->expects($this->once())
			->method('getTime')
			->with()
			->will($this->returnValue('timestamp'));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue(true));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$this->api->expects($this->once())
			->method('multiInstanceEnabled')
			->with()
			->will($this->returnValue(true));

		$milocation = $this->getMock('OCA\MultiInstance\Lib\MILocation', array('createQueuedFriendship'));
		$milocation->staticExpects($this->once())
			->method('createQueuedFriendship')
			->with($userId1, $userId2, 'timestamp', Friendship::ACCEPTED)
			->will($this->returnValue(true));
	
		$friendship = new Friendship();
		$friendship->setUid1($userId2);
		$friendship->setUid2($userId1);

		$result = $friendshipMapper->accept($friendship, $milocation);
		$this->assertEquals(true, $result);
		

	}

	public function testAcceptFailure(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';

		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('exists'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('exists')
			->with($userId1, $userId2)
			->will($this->returnValue(false));
		
		$friendship = new Friendship();
		$friendship->setUid1($userId1);
		$friendship->setUid2($userId2);

		$this->setExpectedException('OCA\AppFramework\Db\DoesNotExistException');
		$result = $friendshipMapper->accept($friendship);

	}

	public function testCreateForFacebook(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array( Friendship::ACCEPTED, 'timestamp', $userId1, $userId2);
		$expected = 'INSERT INTO `*PREFIX*friends_friendships` (status, updated_at, friend_uid1, friend_uid2) VALUES(?, ?, ?, ?)';

		$this->api->expects($this->once())
			->method('getTime')
			->with()
			->will($this->returnValue('timestamp'));

		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('exists'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('exists')
			->with($userId1, $userId2)
			->will($this->returnValue(false));
		
		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue(true));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$this->api->expects($this->once())
			->method('multiInstanceEnabled')
			->with()
			->will($this->returnValue(true));

		$milocation = $this->getMock('OCA\MultiInstance\Lib\MILocation', array('createQueuedFriendship'));
		$milocation->staticExpects($this->once())
			->method('createQueuedFriendship')
			->with($userId1, $userId2, 'timestamp', Friendship::ACCEPTED)
			->will($this->returnValue(true));
	
		$friendship = new Friendship();
		$friendship->setUid1($userId2);
		$friendship->setUid2($userId1);

		$result = $friendshipMapper->create($friendship, $milocation);
		$this->assertEquals(true, $result);
		$this->assertEquals(Friendship::ACCEPTED, $friendship->getStatus());
		
		
	}

	public function testDelete(){
		
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array(Friendship::DELETED, 'timestamp', $userId1, $userId2, $userId2, $userId1);
		$expected = 'UPDATE `*PREFIX*friends_friendships` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?) OR (friend_uid1 = ? AND friend_uid2 = ?)';

		$this->api->expects($this->once())
			->method('multiInstanceEnabled')
			->with()
			->will($this->returnValue(true));
		
		$this->api->expects($this->once())
			->method('getTime')
			->with()
			->will($this->returnValue('timestamp'));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue(true));

		$this->api->expects($this->any())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));
	
		$milocation = $this->getMock('OCA\MultiInstance\Lib\MILocation', array('createQueuedFriendship'));
		$milocation->staticExpects($this->once())
			->method('createQueuedFriendship')
			->with($userId1, $userId2, 'timestamp', Friendship::DELETED)
			->will($this->returnValue(true));

		$result = $this->mapper->delete($userId1, $userId2, $milocation);

		$this->assertEquals(true, $result);

	}


 	public function testFindAllRecipientFriendshipsByUser(){
		$userId = 'thisisuser1';
		$expected = 'SELECT friend_uid1 as friend FROM `*PREFIX*friends_friendships` WHERE friend_uid2 = ? AND status = ?
			UNION
			SELECT friend_uid2 as friend FROM `*PREFIX*friends_friendships` WHERE friend_uid1 = ? AND status = ?';

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
			->method('fetchRow')
			->will($this->returnValue($this->row1));
		$cursor->expects($this->at(1))
			->method('fetchRow')
			->will($this->returnValue(false));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo(array($userId, Friendship::UID1_REQUESTS_UID2, $userId, Friendship::UID2_REQUESTS_UID1)))
			->will($this->returnValue($cursor));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$friendships = $this->mapper->findAllRecipientFriendshipRequestsByUser($userId);

		$this->assertEquals(array('thisisuser2'), $friendships);
 	}

 	public function testFindAllRequesterFriendshipsByUser(){
		$userId = 'thisisuser1';
		$expected = 'SELECT friend_uid1 as friend FROM `*PREFIX*friends_friendships` WHERE friend_uid2 = ? AND status = ?
			UNION
			SELECT friend_uid2 as friend FROM `*PREFIX*friends_friendships` WHERE friend_uid1 = ? AND status = ?';

		$cursor = $this->getMock('cursor', array('fetchRow'));
		$cursor->expects($this->at(0))
			->method('fetchRow')
			->will($this->returnValue($this->row1));
		$cursor->expects($this->at(1))
			->method('fetchRow')
			->will($this->returnValue(false));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo(array($userId, Friendship::UID2_REQUESTS_UID1, $userId, Friendship::UID1_REQUESTS_UID2)))
			->will($this->returnValue($cursor));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$friendships = $this->mapper->findAllRequesterFriendshipRequestsByUser($userId);

		$this->assertEquals(array('thisisuser2'), $friendships);
 	}


	public function testRequest(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$params = array( Friendship::UID2_REQUESTS_UID1, 'timestamp', $userId1, $userId2);
		$expected = 'INSERT INTO `*PREFIX*friends_friendships` (status, updated_at, friend_uid1, friend_uid2) VALUES(?, ?, ?, ?)';

		$this->api->expects($this->once())
			->method('getTime')
			->with()
			->will($this->returnValue('timestamp'));

		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('exists'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('exists')
			->with($userId1, $userId2)
			->will($this->returnValue(false));
		
		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue(true));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));

		$this->api->expects($this->once())
			->method('multiInstanceEnabled')
			->with()
			->will($this->returnValue(true));

		$milocation = $this->getMock('OCA\MultiInstance\Lib\MILocation', array('createQueuedFriendship'));
		$milocation->staticExpects($this->once())
			->method('createQueuedFriendship')
			->with($userId1, $userId2, 'timestamp', Friendship::UID2_REQUESTS_UID1)
			->will($this->returnValue(true));
	
		$friendship = new Friendship();
		$friendship->setUid1($userId1);
		$friendship->setUid2($userId2);
		$friendship->setStatus(Friendship::UID2_REQUESTS_UID1);

		$result = $friendshipMapper->request($friendship, $milocation);
		$this->assertEquals(true, $result);
		
	}
	public function testRequestShouldWorkIfFriendshipAlreadyExistsAndHasDeletedStatus(){
		$userId1 = 'thisisuser1';
		$userId2 = 'thisisuser2';
		$expected = 'UPDATE `*PREFIX*friends_friendships` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?)';
		$params = array(Friendship::UID1_REQUESTS_UID2, 'timestamp', $userId1, $userId2);

		$this->api->expects($this->once())
			->method('getTime')
			->with()
			->will($this->returnValue('timestamp'));

		$friendshipMapper = $this->getMock('OCA\Friends\Db\FriendshipMapper', array('exists', 'find'), array($this->api));
		$friendshipMapper->expects($this->once())
			->method('exists')
			->with($userId1, $userId2)
			->will($this->returnValue(true));

		$friendshipMapper->expects($this->once())
			->method('find')
			->with($userId1, $userId2)
			->will($this->returnValue(new Friendship($this->row4)));

		$query = $this->getMock('query', array('execute'));
		$query->expects($this->once())
			->method('execute')
			->with($this->equalTo($params))
			->will($this->returnValue(true));

		$this->api->expects($this->once())
			->method('prepareQuery')
			->with($this->equalTo($expected))
			->will($this->returnValue($query));
	
		$friendship = new Friendship();
		$friendship->setUid1($userId1);
		$friendship->setUid2($userId2);
		$friendship->setStatus(Friendship::UID1_REQUESTS_UID2);

		$result = $friendshipMapper->request($friendship);
		$this->assertEquals(true, $result);
		
	}

}
