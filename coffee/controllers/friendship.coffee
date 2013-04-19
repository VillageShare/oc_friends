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

			# bind methods on the scope so that you can access them in the
			# controllers child HTML

			@$scope.$on 'routesLoaded', =>
                                @getFriendships(@$scope)

			@$scope.removeFriendship = (friendship) =>
				@removeFriendship(friendship)


		#ajax queries


		getFriendships: (scope) ->
			@request.getFriendships(@config.routes.getFriendshipsRoute, scope)

		removeFriendship: (friendship) ->
			@request.removeFriendship(@config.routes.removeFriendshipRoute, friendship)

	return FriendshipController
