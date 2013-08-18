<?php
/**
<<<<<<< HEAD
 * ownCloud - Multi Instance
 *
 * @author Sarah Jones
 * @copyright 2013 Sarah Jones sarah.e.p.jones@gmail.com
=======
 * ownCloud - Friends
 *
 * @author Morgan Vigil
 * @copyright 2013 Morgan Vigil morgan.a.vigil@gmail.com
>>>>>>> dd4ec82837170f6026bb610f864b7eb435585bd0
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

<<<<<<< HEAD
namespace OCA\Friends\Lib;

use OCA\Friends\Core\FriendsAPI;
use OCA\Friends\Db\LocationMapper;
use OCA\Friends\DependencyInjection\DIContainer;
use OCA\Friends\Db\QueuedFriendship;
use OCA\Friends\Db\QueuedUserFacebookId;
use OCA\Friends\Db\FriendshipMapper;
use OCA\Friends\Db\Friendship;

class Hooks {

	static public function deleteUser($uid) {
		$c = new DIContainer();
		$fm = $c['FriendshipMapper'];
		$friendships = $fm->findAllFriendsByUser($uid);
		foreach($friendships as $friendship) {
			$fm->deleteFriendship($friendship);
		}
	}

=======
use OCA\Friends\Core\FriendsAPI;
use OCA\Friends\Db\LocationMapper;
use OCA\Friends\DependencyInjection\DIContainer;
use OCA\Friends\Db\Friendship;
use OCA\Friends\Db\FriendshipMapper;

namespace OCA\Friends\Lib;


/*
 * This class contains all hooks
 */
class Hooks {

	/*
	 * Method called when a User is deleted. This deletes all 
	 * friendships corresponding to the user.
	 */
        static public function deleteUserFriendships($uid) {
                $di = new DIContainer();
                $api = $di['API'];
		$fm = $di['FriendshipMapper'];
                //Delete Friendships
                $userfriendships = $fm->findAllByUser($uid);
                foreach($userfriendships as $friendship) {
			$fm->delete($friendship);
                }
        }
>>>>>>> dd4ec82837170f6026bb610f864b7eb435585bd0
}
