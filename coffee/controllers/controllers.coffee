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

#angular.module('Friends').controller 'ExampleController',
#['$scope', 'Config', 'FriendsRequest', '_ExampleController', 'ItemModel',
#($scope, Config, FriendsRequest, _ExampleController, ItemModel) ->
#	return new _ExampleController($scope, Config, FriendsRequest, ItemModel)
#]


#Injection
angular.module('Friends').controller 'FRController',
['$scope', 'Config', 'FriendsRequest', '_FRController', 'FRModel',
($scope, Config, FriendsRequest, _FRController, FRModel) ->
	return new _FRController($scope, Config, FriendsRequest, FRModel)
]

angular.module('Friends').controller 'FacebookController',
['$scope', 'Config', 'FriendsRequest', '_FacebookController', 'FacebookModel',
($scope, Config, FriendsRequest, _FacebookController, FacebookModel) ->
	return new _FacebookController($scope, Config, FriendsRequest, FacebookModel)
]

angular.module('Friends').controller 'FriendshipController',
['$scope', 'Config', 'FriendsRequest', '_FriendshipController', 'FriendshipModel',
($scope, Config, FriendsRequest, _FriendshipController, FriendshipModel) ->
	return new _FriendshipController($scope, Config, FriendsRequest, FriendshipModel)
]

