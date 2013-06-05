<?php
/**
 * ownCloud - Friends
 *
 * @author Morgan Vigil
 * @copyright 2013 Morgan Vigil morgan.a.vigil@gmail.com
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
}
