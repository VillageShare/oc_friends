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
angular.module('Friends').factory '_FRController', ->

	class FRController

		constructor: (@$scope, @config, @request, @frModel) ->

			# bind methods on the scope so that you can access them in the
			# controllers child HTML
			@$scope.saveName = (name) =>
				@saveName(name)

			@$scope.acceptFriendshipRequest = (requestor) =>
				@acceptFriendshipRequest(requestor)

			@$scope.createFriendshipRequest = (recipient) =>
				@createFriendshipRequest(recipient)

			@$scope.removeSentFriendshipRequest = (recipientUid) =>
				@removeSentFriendshipRequest(recipientUid)

			@$scope.removeReceivedFriendshipRequest = (requesterUid) =>
				@removeReceivedFriendshipRequest

			@$scope.$on 'routesLoaded', =>
                                @getFriendshipRequests(@$scope)


		#ajax queries

		saveName: (name) ->
			@request.saveName(@config.routes.saveNameRoute, name)

		acceptFriendshipRequest: (friendUid) ->
			@request.acceptFriendshipRequest(@config.routes.acceptFriendshipRequestRoute, friendUid)

		createFriendshipRequest: (recipient) ->
			@request.createFriendshipRequest(@config.routes.createFriendshipRequestRoute, recipient)

		getFriendshipRequests: (scope) ->
			@request.getFriendshipRequests(@config.routes.getFriendshipRequestsRoute, scope)

		removeSentFriendshipRequest: (recipientUid) ->
			console.log("in removeSentFR")
			@request.removeFriendshipRequest(@config.routes.removeFriendshipRequestRoute, recipientUid, 'sent')

		removeReceivedFriendshipRequest: (requesterUid) ->
			@request.removeFriendshipRequest(@config.routes.removeFriendshipRequestRoute, requesterUid, 'received')
	return FRController
