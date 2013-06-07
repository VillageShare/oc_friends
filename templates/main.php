<div id="app" ng-app="Friends" 
	ng-init="tab=0;">

	<a href="{{ url('friends_facebook') }}" class="button float-right">Facebook Sync</a>
	<h1 class="heading">Friends</h1>



	<div class="tabnav">	
		<a href="#" ng-click="tab=0" class="selectedTab-[[tab==0]]">Your Friends</a>
		<a href="#" ng-click="tab=1" class="selectedTab-[[tab==1]]">Add Friends</a>
		<a href="#" ng-click="tab=2" class="selectedTab-[[tab==2]]">Accept Friend Requests</a>
	</div>
		
		
	
	<div class="app-body" ng-controller="FriendshipController">
		<!-- friends -->
		<div ng-show="tab==0">
			<div ng-repeat="friendship in friendships">
				<span>[[friendship]]</span>
				<span ng-click="removeFriendship(friendship, 'accepted')" class="float-right bold">Remove</span>
				<br />
			</div>
			<p ng-hide="friendships.length">
				You do not have any friends.
			</p>
			
		</div>


		<!--send request-->
		<div ng-show="tab==1" class="">
			<form class="centered">
				<input type="text" placeholder="Enter username" ng-model="recipient">
				<button ng-click="createFriendshipRequest(recipient)">Request Friendship</button>
			</form>
			<br /><br />

		<!--sent requests -->
			<div ng-repeat="friendshipRequest in sentFriendshipRequests">
				<span>[[friendshipRequest]]</span>
				<span ng-click="removeFriendship(friendshipRequest, 'requested')" class="float-right bold">Remove</span>
				<br />
			</div>
			<p ng-hide="sentFriendshipRequests.length">
				You are not waiting on any friend requests.
			</p>
		</div>

		<!--received requests -->
		<div ng-show="tab==2" class="">
			<span ng-repeat="friendshipRequest in receivedFriendshipRequests">
				[[friendshipRequest]]
				<div class="float-right">
					<span class="bold" ng-click="acceptFriendshipRequest(friendshipRequest)" >Accept</span>
					<span class="bold" ng-click="removeFriendship(friendshipRequest, 'received')">Remove</span>
				</div>
				<br />
			</span>
			<p ng-hide="receivedFriendshipRequests.length">
				You have responded to all your friend requests.
			</p>
		</div>

	</div>
</div>


