###
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
###


# This is an example of a controller. We pass in the Config via Dependency
# Injection. A factory creates a shared instance. You can also share objects
# across controllers this way

#FriendRequest Controller (named FR so not to be confused with FriendsRequest as in AppRequest)
angular.module('Friends').factory '_FriendshipController', ->

	class FriendshipController

		constructor: (@$scope, @config, @request, @friendshipModel) ->



			#ajax queries


			@$scope.$on 'routesLoaded', =>
				@request.getFriendships(@config.routes.getFriendshipsRoute, @$scope)

			@$scope.removeFriendship = (friend, state) =>
				@request.removeFriendship(@config.routes.removeFriendshipRoute, @$scope, friend, state)

			@$scope.acceptFriendshipRequest = (requestor) =>
				@request.acceptFriendshipRequest(@config.routes.acceptFriendshipRequestRoute, @$scope, requestor)

			@$scope.createFriendshipRequest = (recipient) =>
				@request.createFriendshipRequest(@config.routes.createFriendshipRequestRoute, @$scope, recipient)

			@$scope.$on 'routesLoaded', =>
				console.log('requesting friendship requests')
				@request.getFriendshipRequests(@config.routes.getFriendshipRequestsRoute, @$scope)


	return FriendshipController
