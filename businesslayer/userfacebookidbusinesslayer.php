<?php
/**
* ownCloud - Friends app
*
* @author Sarah Jones
* @copyright 2013 Sarah Jones sarah.e.p.jones@gmail.com 
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

use \OCA\AppFramework\Core\API;
use OCA\Friends\Db\UserFacebookIdMapper;
use OCA\Friends\Db\FriendshipMapper;
use OCA\Friends\Db\Friendship;


class UserFacebookIdBusinessLayer {

	private $api;
	private $friendshipMapper;
	private $userFacebookIdMapper;
	
	public function __construct(API $api, FriendshipMapper $friendshipMapper, UserFacebookIdMapper $userFacebookIdMapper) {
		$this->api = $api;
		$this->friendshipMapper = $friendshipMapper;
		$this->userFacebookIdMapper = $userFacebookIdMapper;
	}

	public function createFriendsFromFacebookFriendsList($currentUser, $friendsDataList) {

		foreach ($friendsDataList as $facebookFriendObj){
			try {
				$friend = $this->userFacebookIdMapper->findByFacebookId($facebookFriendObj->id);
			}
			catch (DoesNotExistException $e){
				//Not an owncloud user who has done the sync or just not an owncloud user
				continue;
			}
			//Transaction
			$this->api->beginTransaction();
			
			if (!$this->api->userExists($friend->getUid())){
				$this->api->log("User " . $friend->getUid() . " does not exist but is in UserFacebookId table as uid.");
				$this->api->commit();
				continue;
			}
			if (!$this->friendshipMapper->exists($friend->getUid(), $currentUser)){
				$friendship = new Friendship();
				$friendship->setFriendUid1($friend->getUid());
				$friendship->setFriendUid2($currentUser);
				$this->friendshipMapper->create($friendship);
			}
			$this->api->commit();
			//End Transaction
		}
		$userFacebookId = $this->userFacebookIdMapper->find($currentUser);
		$this->userFacebookIdMapper->updateSyncTime($userFacebookId);
	}
}	
