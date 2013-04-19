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
angular.module('Friends').factory '_FacebookController', ->

	class FacebookController

		constructor: (@$scope, @config, @request, @facebookModel) ->

			# bind methods on the scope so that you can access them in the
			# controllers child HTML
			@$scope.saveName = (name) =>
				@saveName(name)

			@$scope.confirmSetup = (facebookUrl) =>
				@confirmSetup(facebookUrl)

		saveName: (name) ->
			@request.saveName(@config.routes.saveNameRoute, name)


		confirmSetup: (facebookUrl) =>
			if confirm "The sync will occur with the user currently logged in to Facebook.  If there is no logged in user, you will be prompted to log in.  Please confirm you are the Facebook user logged into Facebook on this computer.  Then press OK to continue."
				#@$scope.$apply( $location.path( facebookUrl ) )
				window.location = facebookUrl

	return FacebookController
