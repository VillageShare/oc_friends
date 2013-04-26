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


namespace OCA\Friends\Core;

use  OCA\AppFramework\Core\API as API;

class FriendsAPI extends API {

        public function getTime() {
                $date = new \DateTime("now");
                $date = date('Y-m-d H:i:s', $date->format('U') - $date->getOffset());
                return (string)$date;
        }

	public function beginTransaction() {
		\OCP\DB::beginTransaction();
	}

	public function commit() {
		\OCP\DB::commit();
	}

	public function userExists($uid) {
		return \OC_User::userExists($uid);
	}

	public function fileGetContents($url) {
		return file_get_contents($url);
	}
}	
