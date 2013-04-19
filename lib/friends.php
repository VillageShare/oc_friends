<?php
/**
 * ownCloud - Multi Instance
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

namespace OCA\Friends\Lib;

use OCA\Friends\Core\FriendsAPI;
use OCA\Friends\Db\LocationMapper;
use OCA\Friends\DependencyInjection\DIContainer;
use OCA\Friends\Db\QueuedFriendship;
use OCA\Friends\Db\QueuedUserFacebookId;

/**
 * This is a static library methods for Friends app.
 */
class Friends{



	static public function areFriends($user1, $user2, $friendshipMapper=null) {
		if ($friendshipMapper !== null) { //For testability 
			$fm = $friendshipnMapper;
		} 
		else {  
			$di = new DIContainer();
			$fm = $di['FriendshipMapper']; 
		}
		return $fm->exists($user1, $user2); 
	}

	static public function getDisplayNames($uid, $search, $limit, $offset, $friendshipMapper=null) {
		if ($friendshipMapper !== null) { //For testability 
			$fm = $friendshipnMapper;
		} 
		else {  
			$di = new DIContainer();
			$fm = $di['FriendshipMapper']; 
		}

		return $fm->getDisplayNames($uid, $search, $limit, $offset);
	}

}
