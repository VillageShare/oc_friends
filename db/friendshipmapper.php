<?php
/**
* ownCloud - App Template Example
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

use \OCA\AppFramework\Core\API as API;
use \OCA\AppFramework\Db\Mapper as Mapper;
use \OCA\AppFramework\Db\DoesNotExistException as DoesNotExistException;
use \OCA\AppFramework\Db\MultipleObjectsReturnedException as MultipleObjectsReturnedException;
use OCA\Friends\Db\AlreadyExistsException as AlreadyExistsException;

use OCA\MultiInstance\Lib\MILocation as MILocation;
use \OCA\AppFramework\Db\Entity;

class FriendshipMapper extends Mapper {




	/**
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct($api){
		parent::__construct($api, 'friends_friendships');
	}




	/**
	 * Finds all friends for a user 
	 * @param string $userId: the id of the user that we want to find friends for
	 * @throws DoesNotExistException: if the item does not exist
	 * @return an array of friends
	 */
	public function findAllFriendsByUser($userId){
		$sql = 'SELECT friend_uid2 as friend FROM `' . $this->getTableName() . '` WHERE (friend_uid1 = ? AND status = ?)
			UNION
			SELECT friend_uid1 as friend FROM `' . $this->getTableName() . '` WHERE (friend_uid2 = ? AND status = ?)';
		$params = array($userId, Friendship::ACCEPTED, $userId, Friendship::ACCEPTED);

		$result = array();
		
		$query_result = $this->execute($sql, $params);
		while($row =  $query_result->fetchRow()){
			$friend = $row['friend'];
			array_push($result, $friend);
		}
		return $result;
	}

        /**
         * @brief Get a list of all friends' display names
         * @returns array with  all displayNames (value) and the correspondig uids (key)
         *
         * Get a list of all friends' display names and user ids.
         */
        public function getDisplayNames($uid, $search = '', $limit = null, $offset = null) {
                $displayNames = array();
		$sql = 'SELECT `friends`.`uid`, `*PREFIX*users`.`displayname` 
			FROM
				(SELECT `friend_uid1` as `uid` FROM `' . $this->getTableName() . '` WHERE (`friend_uid2` = ? AND `status` = ?)
				UNION
				SELECT `friend_uid2` as `uid` FROM `' . $this->getTableName() . '` WHERE (`friend_uid1` = ? AND `status` = ?)
				) as `friends`, `*PREFIX*users`
			WHERE `friends`.`uid` = `*PREFIX*users`.`uid`
				AND ((LOWER(`displayname`) LIKE LOWER(?)) OR LOWER(`friends`.`uid`) LIKE LOWER(?))';
		$params = array($uid, Friendship::ACCEPTED, $uid, Friendship::ACCEPTED, $search.'%', $search.'%'); 
		$query_result = $this->execute($sql, $params, $limit, $offset);
		while($row =  $query_result->fetchRow()){
			if (trim( $row['displayname']) === '') {
                        	$displayNames[$row['uid']] = $row['uid'];
			} 
			else {	
                        	$displayNames[$row['uid']] = $row['displayname'];
			}
		}


                return $displayNames;
        }


	public function findAllByUser($userId){
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE (friend_uid1 = ?)
			UNION
			SELECT * FROM `' . $this->getTableName() . '` WHERE (friend_uid2 = ?)';
		$params = array($userId, $userId);

		$result = array();
		
		$query_result = $this->execute($sql, $params);
		while($row = $query_result->fetchRow()){
			$friend = new Friendship($row);
			array_push($result, $friend);
		}
		return $result;
	}


	/**
	 * Finds all users requesting friendship of the user 
	 * @param string $userId: the id of the user that we want to find friendship requests for
	 * @return an array of user uids
	 */
	public function findAllRecipientFriendshipRequestsByUser($userId){
		$sql = 'SELECT friend_uid1 as friend FROM `' . $this->getTableName() . '` WHERE friend_uid2 = ? AND status = ?
			UNION
			SELECT friend_uid2 as friend FROM `' . $this->getTableName() . '` WHERE friend_uid1 = ? AND status = ?';
		$params = array($userId, Friendship::UID1_REQUESTS_UID2, $userId, Friendship::UID2_REQUESTS_UID1);

		$result = array();
		
		$query_result = $this->execute($sql, $params);
		while($row = $query_result->fetchRow()){
			$requester = $row['friend'];
			array_push($result, $requester);
		}

		return $result;
	}


	/**
	 * Finds all users the user is requesting friendships with 
	 * @param string $userId: the id of the user that we want to find friendship-request recipients for
	 * @return an array of user uids
	 */
	public function findAllRequesterFriendshipRequestsByUser($userId){
		$sql = 'SELECT friend_uid1 as friend FROM `' . $this->getTableName() . '` WHERE friend_uid2 = ? AND status = ?
			UNION
			SELECT friend_uid2 as friend FROM `' . $this->getTableName() . '` WHERE friend_uid1 = ? AND status = ?';
		$params = array($userId, Friendship::UID2_REQUESTS_UID1, $userId, Friendship::UID1_REQUESTS_UID2);

		$result = array();
		$query_result = $this->execute($sql, $params);
		while($row =  $query_result->fetchRow()){
			$recipient = $row['friend'];
			array_push($result, $recipient);
		}

		return $result;
	}
	/**
	 * Finds a friendship  
	 * @param string $userId1: the id of one of the users
	 * @param string $userId2: the id  of the other user
	 * @throws DoesNotExistException: if the item does not exist
	 * @throws MultipleObjectsReturnedException: if more than one friendship with those ids exists
	 * @return a friendship object
	 */
	public function find($userId1, $userId2){
		$uids = $this->sortUids($userId1, $userId2);
		$sql = 'SELECT * FROM `' . $this->getTableName() . '` WHERE friend_uid1 = ? AND friend_uid2 = ?';
		$params = array($uids[0], $uids[1]);

		$result = array();
		
		$result = $this->execute($sql, $params);
		$row = $result->fetchRow();


		if ($row === false) {
			throw new DoesNotExistException('Friendship with users ' . $userId1 . ' and ' . $userId2 . ' does not exist!');
		} elseif($result->fetchRow() !== false) {
			throw new MultipleObjectsReturnedException('Friendship with users ' .$userId1 . ' and ' . $userId2 . ' returned more than one result.');
		}
		return new Friendship($row);
	}	


	/** 
	 * Checks to see if a row already exists
	 * @param $userId1 - the first user id
	 * @param $userId2 - the second user id
	 * @return boolean: whether or not it exists (note: will return true if more than one is found)
	 */
	public function exists($userId1, $userId2){
		try{
			//sorted in find
			$f = $this->find($userId1, $userId2);
		}
		catch (DoesNotExistException $e){
			return false;
		}
		catch (MultipleObjectsReturnedException $e){
			return true;
		}
		return true;
	}

	/**
	 * Saves a friendship into the database
	 * @param  $friendship: the friendship to be saved
	 * @return true if successful
	 */
	public function request($friendship){
		//Must save in alphanumeric order (for multiinstance app)
		$uids = $this->sortUids($friendship->getFriendUid1(), $friendship->getFriendUid2());

		if ($friendship->getStatus() !== Friendship::UID1_REQUESTS_UID2 && $friendship->getStatus() !== Friendship::UID2_REQUESTS_UID1){
			$this->api->log("Calling request without a request status.");
			return false;
		}

		if ($this->exists($uids[0], $uids[1])){
			if ($this->find($uids[0], $uids[1])->getStatus() !== Friendship::DELETED) {
				throw new AlreadyExistsException('Cannot save Friendship with friend_uid1 = ' . $friendship->getFriendUid1() . ' and friend_uid2 = ' . $friendship->getFriendUid2());
			}
			$sql = 'UPDATE `' . $this->getTableName() . '` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?)';
		}
		else {
			$sql = 'INSERT INTO `'. $this->getTableName() . '` (status, updated_at, friend_uid1, friend_uid2)'.
				' VALUES(?, ?, ?, ?)';
		}
		

		$friendship->setUpdatedAt($this->api->getTime());
		if ($uids[0] !== $friendship->getFriendUid1()) {
			//switch order of request
			$friendship->setFriendUid1($uids[0]);
			$friendship->setFriendUid2($uids[1]);
			if ($friendship->getStatus() === Friendship::UID1_REQUESTS_UID2){
				$friendship->setStatus(Friendship::UID2_REQUESTS_UID1);
			} else if ($friendship->getStatus() === (Friendship::UID2_REQUESTS_UID1)){
				$friendship->setStatus(Friendship::UID1_REQUESTS_UID2);
			}
		}

		

		$params = array(
			$friendship->getStatus(),
			$friendship->getUpdatedAt(),
			$friendship->getFriendUid1(),
			$friendship->getFriendUid2()
		);

		$result = $this->execute($sql, $params);
		if ($result){
			$this->api->emitHook('FriendshipMapper', 'post_request', array('friendship' => $friendship));
		}
		return $result;
	}

	public function accept($friendship) {
		$uids = $this->sortUids($friendship->getFriendUid1(), $friendship->getFriendUid2());

		if (!$this->exists($uids[0], $uids[1])){
			throw new DoesNotExistException("Cannot accept a friendship that does not exist.  uid1 = {$uids[0]}, uid2 = {$uids[1]}");
		}

		$sql = 'UPDATE `' . $this->getTableName() . '` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?)';
		$friendship->setStatus(Friendship::ACCEPTED);
		$friendship->setUpdatedAt($this->api->getTime());
		if ($uids[0] !== $friendship->getFriendUid1()) {
			//switch order of request
			$friendship->setFriendUid1($uids[0]);
			$friendship->setFriendUid2($uids[1]);
		}

		$params = array(
			$friendship->getStatus(),
			$friendship->getUpdatedAt(),
			$friendship->getFriendUid1(),
			$friendship->getFriendUid2()
		);

		$result = $this->execute($sql, $params);
		if ($result){
			$this->api->emitHook('FriendshipMapper', 'post_accept', array('friendship' => $friendship));
		}
		return $result;
		
	}

	/**
	 * This method bypasses the request and accept for Facebook sync
	 */
	public function create($friendship) {
		//Must save in alphanumeric order (for multiinstance app)
		$uids = $this->sortUids($friendship->getFriendUid1(), $friendship->getFriendUid2());

		if ($this->exists($uids[0], $uids[1])){
			throw new AlreadyExistsException('Cannot save Friendship with friend_uid1 = ' . $friendship->getFriendUid1() . ' and friend_uid2 = ' . $friendship->getFriendUid2());
		}

		$sql = 'INSERT INTO `'. $this->getTableName() . '` (status, updated_at, friend_uid1, friend_uid2)'.
			' VALUES(?, ?, ?, ?)';
		

		if ($uids[0] !== $friendship->getFriendUid1()) {
			//switch order of request
			$friendship->setFriendUid1($uids[0]);
			$friendship->setFriendUid2($uids[1]);
		}
		$friendship->setStatus(Friendship::ACCEPTED);
		$friendship->setUpdatedAt($this->api->getTime());

		$params = array(
			$friendship->getStatus(),
			$friendship->getUpdatedAt(),
			$friendship->getFriendUid1(),
			$friendship->getFriendUid2()
		);

		$result = $this->execute($sql, $params);
		if ($result){
			$this->api->emitHook('FriendshipMapper', 'post_create', array('friendship' => $friendship));
		}
		return $result;

	}

	/**
	 * Deletes a friendship
	 * @param userId1: the first user
	 * @param userId2: the second user
	 * TODO make this so it matches the parent delete format
	 */
	public function delete(Entity $friendship){
		$uids = $this->sortUids($friendship->getFriendUid1(), $friendship->getFriendUid2());
		
		$sql = 'UPDATE `' . $this->getTableName() . '` SET status=?, updated_at=? WHERE (friend_uid1 = ? AND friend_uid2 = ?) OR (friend_uid1 = ? AND friend_uid2 = ?)';

		if ($uids[0] !== $friendship->getFriendUid1()) {
			//switch order of request
			$friendship->setFriendUid1($uids[0]);
			$friendship->setFriendUid2($uids[1]);
		}
		$friendship->setStatus(Friendship::DELETED);
		$friendship->setUpdatedAt($this->api->getTime());
		$params = array(
			$friendship->getStatus(),
			$friendship->getUpdatedAt(),
			$friendship->getFriendUid1(),
			$friendship->getFriendUid2(),
			$friendship->getFriendUid2(),
			$friendship->getFriendUid1()
		);
		
		$result = $this->execute($sql, $params);
		if ($result){
			$this->api->emitHook('FriendshipMapper', 'post_delete', array('friendship' => $friendship));
		}
		return $result;
	}

	/**
	 * Helper function
	 * MultiInstance app requires that friendships be saved in alphanumeric order.
	 * This simplifies lookup and eliminates duplication problem (where uid1 and uid2 are swapped)
	 */
	public function sortUids($userId1, $userId2) {
		$compare = strcmp($userId1, $userId2);
		if ($compare === 0) {
			throw new Exception("Cannot uids that are the same");
		}
		else if ($compare < 0) {
			return array($userId1, $userId2);
		}
		else {
			return array($userId2, $userId1);
		}
	}


}
