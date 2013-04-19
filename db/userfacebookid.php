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


class UserFacebookId {

	private $uid;
	private $facebookId;
	private $facebookName;
	private $friendsSyncedAt;

	public function __construct($fromRow=null){
		if($fromRow){
			$this->fromRow($fromRow);
		}
	}

	public function fromRow($row){
		$this->uid = $row['uid'];
		$this->facebookId = $row['facebook_id'];
		$this->facebookName = $row['facebook_name'];
		$this->friendsSyncedAt = $row['friends_synced_at'];
	}


	public function getUid(){
		return $this->uid;
	}

	public function getFacebookId(){
		return $this->facebookId;
	}

	public function getFacebookName(){
		return $this->facebookName;
	}
		
	public function getFriendsSyncedAt(){
		return $this->friendsSyncedAt;
	}

	public function setUid($uid){
		$this->uid = $uid;
	}

	public function setFacebookId($facebookId){
		$this->facebookId = $facebookId;
	}

	public function setFacebookName($facebookName){
		$this->facebookName = $facebookName;
	}

}
