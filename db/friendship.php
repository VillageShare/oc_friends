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

use \OCA\AppFramework\Db\Entity;

class Friendship extends Entity {

	public $id;
	public $friendUid1;
	public $friendUid2;
	public $updatedAt;
	public $status;


	const ACCEPTED = 1;
	const DELETED = 2;
	const UID1_REQUESTS_UID2 = 3;
	const UID2_REQUESTS_UID1 = 4;

	public function __construct($fromRow=null){
		$this->addType('id', 'int');
		$this->addType('status', 'int');
		if($fromRow){
			$this->fromRow($fromRow);
		}
	}
}
