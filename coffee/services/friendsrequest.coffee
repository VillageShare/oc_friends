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

# Define your local request functions in an object that inherits from the
# Request object
angular.module('Friends').factory '_FriendsRequest',
['_Request',
(_Request) ->

	class FriendsRequest extends _Request


		constructor: ($http, $rootScope, Config, Publisher) ->
			super($http, $rootScope, Config, Publisher)


		saveName: (route, name) ->
			data =
				somesetting: name

			@post(route, {}, data)


		# Create your local request methods in here
		#
		# myReqest: (route, ...) ->

		#get both sent and received friendship requests
		getFriendshipRequests: (route, scope) ->
			success = (data) ->
				scope.receivedFriendshipRequests = data.data.receivedFriendshipRequests
				scope.sentFriendshipRequests = data.data.sentFriendshipRequests

			#this must be the call to get the data, and success must be the callback
			@post(route, {}, {}, success)			


		#accept a friend request
		acceptFriendshipRequest: (route, friendUid) ->
			data =
				acceptedFriend: friendUid

			@post(route, {}, data)

		#create a friend request
		createFriendshipRequest: (route, recipientUid) ->
			console.log(route)
			data =
				recipient: recipientUid
			
			@post(route, {}, data)


		#remove a friend request
		removeFriendshipRequest: (route, userUid, sentOrReceived) ->
			data =
				userUid: userUid
				sentOrReceived: sentOrReceived
			
			@post(route, {}, data)


		#get Friendships
		getFriendships: (route, scope) ->
			success = (data) ->
				scope.friendships = data.data.friendships

			@post(route, {}, {}, success)			
			
		removeFriendship: (route, friendship) ->
			data =
				friend: friendship

			@post(route, {}, data)	


	return FriendsRequest
]
