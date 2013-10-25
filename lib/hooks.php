<?php
/* ownCloud - Friends
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

namespace OCA\Friends\Lib;

use OCA\Friends\Core\FriendsAPI;
use OCA\Friends\Db\LocationMapper;
use OCA\Friends\DependencyInjection\DIContainer;
use OCA\Friends\Db\QueuedFriendship;
use OCA\Friends\Db\QueuedUserFacebookId;
use OCA\Friends\Db\FriendshipMapper;
use OCA\Friends\Db\Friendship;

class Hooks {

	static public function deleteUser($parameters, $mockFriendshipMapper=null) {
		$msg = "Error test message.";
		$uid = $parameters['uid'];
		#trigger_error($msg);
		if ($mockFriendshipMapper == null) {
			$c = new DIContainer();
			$fm = $c['FriendshipMapper'];
		} else {
			$fm = $mockFriendshipMapper;
		}
		#trigger_error($msg);
		$friendships = $fm->findAllFriendshipsByUser($uid);
		#trigger_error($msg);
		foreach($friendships as $friendship) {
			$fm->delete($friendship);
		}
	}

}
