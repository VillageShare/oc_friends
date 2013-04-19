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



angular.module('Friends').factory 'FriendsRequest',
['$http', '$rootScope', 'Config', '_FriendsRequest', 'Publisher',
($http, $rootScope, Config, _FriendsRequest, Publisher) ->
	return new _FriendsRequest($http, $rootScope, Config, Publisher)
]

#FriendshipRequest


angular.module('Friends').factory 'FRModel',
['_FRModel',
(_FRModel) ->
	return new _FRModel()
]


angular.module('Friends').factory 'Publisher',
['_Publisher', 'FRModel',
(_Publisher, FRModel) ->
	publisher = new _Publisher()
	publisher.subscribeModelTo(FRModel, 'friendshiprequests')
	return publisher
]


#Friendship

angular.module('Friends').factory 'FriendshipModel',
['_FriendshipModel',
(_FriendshipModel) ->

	return new _FriendshipModel()
]

angular.module('Friends').factory 'Publisher',
['_Publisher', 'FriendshipModel',
(_Publisher, FriendshipModel) ->
	publisher = new _Publisher()
	publisher.subscribeModelTo(FriendshipModel, 'friendships')
	return publisher
]

#Facebook

angular.module('Friends').factory 'FacebookModel',
['_FacebookModel',
(_FacebookModel) ->

	return new _FacebookModel()
]


angular.module('Friends').factory 'Publisher',
['_Publisher', 'FacebookModel',
(_Publisher, FacebookModel) ->
	publisher = new _Publisher()
	publisher.subscribeModelTo(FacebookModel, 'facebook')
	return publisher
]
