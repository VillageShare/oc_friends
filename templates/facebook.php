<div id="app" ng-app="Friends" >
<h1 class="heading">Facebook Sync</h1>
<div class="app-body" ng-controller="FacebookController" >
	<div>
		<p class="spacing">Facebook Sync allows you to import your Facebook friends from Facebook.  When one of your Facebook friends on VillageShare also uses the Facebook Sync, you will automatically become VillageShare friends with that user.  We hope that this will make it easier to share files.</p>

		<p class="spacing">If you add new Facebook friends after doing this sync, they will not automatically be added to VillageShare.  You must do the sync again to add these new Facebook friends to VillageShare.</p>

		<p class="spacing">Using this feature requires that you give the VillageShare Facebook App access to your basic permissions.  Basic permissions include your Facebook identifier, name, gender, Facebook profile link, timezone, and locale as posted on Facebook, as well as the names and Facebook identifiers of all of your Facebook friends.  We will only store the Facebook identifiers of you and your Facebook friends.  </p>
		<p class="spacing">
			More information can be found on the Facebook website: <a class="link" href="https://developers.facebook.com/docs/reference/login/public-profile-and-friend-list/">https://developers.facebook.com/docs/reference/login/public-profile-and-friend-list/</a>
		</p>
	</div>
	<div>	
		{% if facebook_name %}
			<p class="spacing" style="font-weight:bold">Your Facebook user is {{facebook_name}}.
			{% if friends_updated_at %}
				Last sync at {{friends_updated_at}}.
			{% else %}
				Sync has not been performed.
			{% endif %}
			</p>
			<a href="{{fb_dialog_url}}" class="button">Sync Friends</a>
		{% else %}
			<a href="#" ng-click="confirmSetup('{{fb_dialog_url}}')" class="button">Setup Sync</a>
		{% endif %}
	</div>
</div>
</div>
