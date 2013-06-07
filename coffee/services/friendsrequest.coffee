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


		constructor: ($http, Config, Publisher) ->
			super($http, Config, Publisher)




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
		acceptFriendshipRequest: (route, scope, friendUid) ->
			data =
				acceptedFriend: friendUid
			success = (data) -> 
				if data.data.success
					scope.receivedFriendshipRequests.remove(friendUid)				
					scope.friendships.push friendUid
				else
					alert('Sorry. Accepting the friendship didn\'t work.  Please refresh the page and try again.')
					
			failure = (data) ->
				alert('Sorry. Cannot connect to server')

			@post(route, {}, data, success, failure)

		#create a friend request
		createFriendshipRequest: (route, scope, recipientUid) ->
			data =
				recipient: recipientUid
			
			success = (data) ->
				if data.data.success
					scope.sentFriendshipRequests.push recipientUid
				else
					alert('Sorry.  Creating a friendship with that user didn\'t work.  Are you sure you aren\'t already friends with that user?')
				scope.recipient = ""
			failure = (data) -> 
				alert('Sorry. Cannot connect to server.')
			
			@post(route, {}, data, success, failure)



		#get Friendships
		getFriendships: (route, scope) ->
			success = (data) ->
				scope.friendships = data.data.friendships

			@post(route, {}, {}, success)			
			
		#remove Friendship (request or full)
		removeFriendship: (route, scope, friendUid, state) ->
			data =
				friend: friendUid

			success = (data) ->
				if data.data.success
					if state == 'requested'	
						scope.sentFriendshipRequests.remove(friendUid)
					else if state == 'received'
						scope.receivedFriendshipRequests.remove(friendUid)
					else if state == 'accepted'
						scope.friendships.remove(friendUid)
				else
					alert('Sorry.  Deleting that friendship didn\'t work.  Try refreshing the page.')

			failure = (data) -> 
				alert('Sorry. Cannot connect to server.')

			@post(route, {}, data, success, failure)	

		#http://stackoverflow.com/questions/4825812/clean-way-to-remove-element-from-javascript-array-with-jquery-coffeescript
		Array::remove = (e) -> @[t..t] = [] if (t = @indexOf(e)) > -1

	return FriendsRequest
]
