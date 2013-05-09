<?php

/**
* ownCloud - App Template Example
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

namespace OCA\Friends\Controller;

use OCA\AppFramework\Controller\Controller as Controller;
use OCA\AppFramework\Db\DoesNotExistException as DoesNotExistException;
use OCA\AppFramework\Http\RedirectResponse as RedirectResponse;

use OCA\Friends\Db\Friendship as Friendship;
use OCA\Friends\Db\UserFacebookId as UserFacebookId;
use OCA\Friends\Db\FacebookFriend as FacebookFriend;


class FriendshipController extends Controller {
	
	private $friendshipMapper;
	private $userFacebookIdBusinessLayer;
	private $userFacebookIdMapper;

	public $app_id;
	public $app_secret;
	public $my_url;

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param ItemMapper $friendshipMapper: an itemwrapper instance
	 */
	public function __construct($api, $request, $friendshipMapper, $userFacebookIdMapper, $userFacebookIdBusinessLayer){
		parent::__construct($api, $request);
		$this->friendshipMapper = $friendshipMapper;
		$this->userFacebookIdMapper = $userFacebookIdMapper;
		$this->userFacebookIdBusinessLayer = $userFacebookIdBusinessLayer;
		
		$this->app_id = $this->api->getSystemValue('friends_fb_app_id');
		$this->app_secret = $this->api->getSystemValue('friends_fb_app_secret');
		$this->my_url = $this->api->getSystemValue('friends_fb_app_url');
	}


	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * Redirects to the index page
	 */
	public function redirectToIndex(){
		$url = $this->api->linkToRoute('friends_index');
		return new RedirectResponse($url);
	}


	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @brief renders the index page
	 * @return an instance of a Response implementation
	 */
	public function index(){


		// thirdparty stuff
		$this->api->add3rdPartyScript('angular/angular');

		// your own stuff
		$this->api->addStyle('style');
		$this->api->addStyle('animation');

		$this->api->addScript('app');

		// example database access
		// check if an entry with the current user is in the database, if not
		// create a new entry
		$templateName = 'main';
		/*$params = array(
			'somesetting' => $this->api->getSystemValue('somesetting'),
			'test' => $this->params('test')
		);*/
		$params = array();
		return $this->render($templateName, $params);
	}


	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @brief renders the facebook page
	 * @return an instance of a Response implementation
	 */
	public function facebookSync(){
		//Check for rejected permissions
		//YOUR_REDIRECT_URI?
			//error_reason=user_denied
			//&error=access_denied
			//&error_description=The+user+denied+your+request.
			//&state=YOUR_STATE_VALUE


		/*	Start Facebook Code 	*/
		//Set by Facebook response
		if (array_key_exists('code', $_REQUEST)){
			$code = $_REQUEST["code"];
		}

		// Redirect to Login Dialog
		if(empty($code)) {
			$_SESSION['state'] = md5(uniqid(rand(), TRUE)); // CSRF protection

			$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
			. $this->app_id . "&redirect_uri=" . urlencode($this->my_url) . "&state="
			. $_SESSION['state'];
		}

		$currentUser = $this->api->getUserId();

		$userFacebookId = null; //Used below

		//Have permission
		if(array_key_exists('state', $_SESSION) && array_key_exists('state', $_REQUEST)) {
			
			if ($_SESSION['state'] && ($_SESSION['state'] === $_REQUEST['state'])) {
					//Dialog url needs to be reset after getting response, otherwise it is undefined
				$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
				. $this->app_id . "&redirect_uri=" . urlencode($this->my_url) . "&state="
				. $_SESSION['state'];

				$token_url = "https://graph.facebook.com/oauth/access_token?"
					. "client_id=" . $this->app_id . "&redirect_uri=" . urlencode($this->my_url)
					. "&client_secret=" . $this->app_secret . "&code=" . $code;

				$response = $this->api->fileGetContents($token_url); //Get access token
				$params = null;
				parse_str($response, $params);
				if (!array_key_exists('access_token', $params) || $params['access_token']===null){
					//TODO message to user
					error_log("Access token was empty");
				}
				else{
					$_SESSION['access_token'] = $params['access_token'];

					//Get user
					if (!$this->userFacebookIdMapper->exists($currentUser)){
						$graph_url = "https://graph.facebook.com/me?access_token=" 
								. $params['access_token'];
						$user = json_decode($this->api->fileGetContents($graph_url)); //Get user info

						$userFacebookId = new UserFacebookId();
						$userFacebookId->setUid($currentUser);
						$userFacebookId->setFacebookId($user->id);
						$userFacebookId->setFacebookName($user->name);
						$this->userFacebookIdMapper->save($userFacebookId);
					}
					else { //Get Friends
					
						$graph_url = "https://graph.facebook.com/me/friends?access_token=" 
								. $params['access_token'];
						$friends = json_decode($this->api->fileGetContents($graph_url)); //Get user's friends
						
						$this->userFacebookIdBusinessLayer->createFriendsFromFacebookFriendsList($currentUser, $friends->data);
					}
				}
			}
			else { //State is defined but does not match
				error_log("State does not match for Facebook auth");
			}
		}
		/* 	End Facebook Code	*/
		try{
			if ($userFacebookId === null) {
				$userFacebookId = $this->userFacebookIdMapper->find($currentUser);
			}
			else {
			}
			$facebookName = $userFacebookId->getFacebookName();
			$facebookUpdatedAt = $userFacebookId->getFriendsSyncedAt();
		}
		catch (DoesNotExistException $e){
			$facebookName = null;
			$facebookUpdatedAt = null;
		}


		// thirdparty stuff
		$this->api->add3rdPartyScript('angular/angular');

		// your own stuff
		$this->api->addStyle('style');
		$this->api->addStyle('animation');

		$this->api->addScript('app');

		$templateName = 'facebook';
		$params = array(
			'fb_dialog_url' => $dialog_url,
			'facebook_name' => $facebookName,
			'friends_updated_at' => $facebookUpdatedAt
		);
		return $this->render($templateName, $params);

	}



	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief creates a FriendshipRequest
	 * @param 
	 */
	public function createFriendshipRequest(){
		$recipientId = $this->params('recipient');

		$friendshipRequest = new Friendship();
		$friendshipRequest->setFriendUid1($this->api->getUserId()); 
		$friendshipRequest->setFriendUid2($recipientId);
		$friendshipRequest->setStatus(Friendship::UID1_REQUESTS_UID2);
		

		//TODO: error handling
		if($this->friendshipMapper->request($friendshipRequest)){
			//TODO: return something useful
			return $this->renderJSON(array(true));
		}
		else {
			//TODO: return something useful
			return $this->renderJSON(array(false));
		}	
	}

	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief removes a FriendshipRequest
	 * @param 
	 */
	public function removeFriendshipRequest(){
		$userUid = $this->params('userUid');
		$sentOrReceived = $this->params('sentOrReceived');

		if ($sentOrReceived === 'sent'){
			$recipient = $userUid;
			$requester = $this->api->getUserId();
		}
		else if ($sentOrReceived === 'received'){
			$recipient = $this->api->getUserId();
			$requester = $userUid;
		}
		else {
			//TODO: error handling
		}

		if($this->friendshipMapper->exists($requester, $recipient)){
			$this->friendshipMapper->delete($requester, $recipient);
			//TODO: return something useful
			return $this->renderJSON(array(true));
		}
		else {
			//TODO: error handling
			error_log("cannot find friendshiprequest for removeFriendshipRequest");
		}
		

	}

		
	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief converts FriendshipRequest into a friendship
	 * @param 
	 */
	public function acceptFriendshipRequest(){
		$requester = $this->params('acceptedFriend');	
		$currentUser = $this->api->getUserId();

		if ($this->friendshipMapper->exists($requester, $currentUser)){
			$this->api->beginTransaction();
			$friendship = $this->friendshipMapper->find($requester, $currentUser);
			if (($requester === $friendship->getFriendUid1() && Friendship::UID1_REQUESTS_UID2 == $friendship->getStatus()) ||
				($requester === $friendship->getFriendUid2() && Friendship::UID2_REQUESTS_UID1 == $friendship->getStatus())){
				
				$this->friendshipMapper->accept($friendship);
			} else {
				//TODO: change this to an exception with more detail
				error_log("Error in acceptFriendshipRequest");
			}
			$this->api->commit();
		}

		//TODO: change this to return something useful
		return $this->renderJSON(array(true));
	}


	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief gets all FriendshipRequests for the current user
	 * @param 
	 */
	public function getFriendshipRequests(){
		$receivedfriendrequests = $this->friendshipMapper->findAllRecipientFriendshipRequestsByUser($this->api->getUserId());
		$sentfriendrequests = $this->friendshipMapper->findAllRequesterFriendshipRequestsByUser($this->api->getUserId());

		$params = array(
			'receivedFriendshipRequests' => $receivedfriendrequests,
			'sentFriendshipRequests' => $sentfriendrequests
		);
		return $this->renderJSON($params);
		
	}


	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief gets Friendships for the current user
	 * @param 
	 */
	public function getFriendships(){
		$friends = $this->friendshipMapper->findAllFriendsByUser($this->api->getUserId());
		$params = array(
			'friendships' => $friends
		);
		return $this->renderJSON($params);
	}

	/** 
	 * @Ajax
	 * @IsSubAdminExemption
	 * @IsAdminExemption
	 *
	 * @brief deletes a Friendship for the current user
	 * @param 
	 */
	public function removeFriendship(){
		$userUid = $this->params('friend');
		$currentUser = $this->api->getUserId();
		$this->friendshipMapper->delete($userUid, $currentUser);

		//TODO: return useful info
		return $this->renderJSON(array(true));

	}
}
