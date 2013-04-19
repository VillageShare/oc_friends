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


class Friendship {

	private $uid1;
	private $uid2;
	private $updatedAt;
	private $status;


	const ACCEPTED = 1;
	const DELETED = 2;
	const UID1_REQUESTS_UID2 = 3;
	const UID2_REQUESTS_UID1 = 4;

	public function __construct($fromRow=null){
		if($fromRow){
			$this->fromRow($fromRow);
		}
	}

	public function fromRow($row){
		$this->uid1 = $row['friend_uid1'];
		$this->uid2 = $row['friend_uid2'];
		$this->updatedAt = $row['updated_at'];
		$this->status = $row['status'];
	}


	public function getUid1(){
		return $this->uid1;
	}

	public function getUid2(){
		return $this->uid2;
	}

	public function getUpdatedAt(){
		return $this->updatedAt;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setUid1($uid){
		$this->uid1 = $uid;
	}

	public function setUid2($uid){
		$this->uid2 = $uid;
	}

	public function setStatus($status){
		$this->status = $status;
	}

}
