
/*
# ownCloud
#
# @author Bernhard Posselt
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('OC', []);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends', ['OC']).config([
    '$provide', '$interpolateProvider', function($provide, $interpolateProvider) {
      var Config;
      $interpolateProvider.startSymbol('[[');
      $interpolateProvider.endSymbol(']]');
      Config = {
        myParam: 'test'
      };
      Config.routes = {
        saveNameRoute: 'friends_ajax_setsystemvalue',
        createFriendshipRequestRoute: 'friends_ajax_createFriendshipRequest',
        acceptFriendshipRequestRoute: 'friends_ajax_acceptFriendshipRequest',
        removeFriendshipRequestRoute: 'friends_ajax_removeFriendshipRequest',
        getFriendshipRequestsRoute: 'friends_ajax_getFriendshipRequests',
        getFriendshipsRoute: 'friends_ajax_getFriendships',
        removeFriendshipRoute: 'friends_ajax_removeFriendship'
      };
      return $provide.value('Config', Config);
    }
  ]);

  angular.module('Friends').run([
    '$rootScope', function($rootScope) {
      var init;
      init = function() {
        return $rootScope.$broadcast('routesLoaded');
      };
      return OC.Router.registerLoadedCallback(init);
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').filter('leetIt', function() {
    return function(leetThis) {
      return leetThis.replace('e', '3').replace('i', '1');
    };
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  angular.module('Friends').factory('_FriendshipModel', [
    '_Model', function(_Model) {
      var FriendshipModel;
      FriendshipModel = (function(_super) {

        __extends(FriendshipModel, _super);

        function FriendshipModel() {
          FriendshipModel.__super__.constructor.call(this);
        }

        return FriendshipModel;

      })(_Model);
      return FriendshipModel;
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  angular.module('Friends').factory('_FriendsRequest', [
    '_Request', function(_Request) {
      var FriendsRequest;
      FriendsRequest = (function(_super) {

        __extends(FriendsRequest, _super);

        function FriendsRequest($http, $rootScope, Config, Publisher) {
          FriendsRequest.__super__.constructor.call(this, $http, $rootScope, Config, Publisher);
        }

        FriendsRequest.prototype.saveName = function(route, name) {
          var data;
          data = {
            somesetting: name
          };
          return this.post(route, {}, data);
        };

        FriendsRequest.prototype.getFriendshipRequests = function(route, scope) {
          var success;
          success = function(data) {
            scope.receivedFriendshipRequests = data.data.receivedFriendshipRequests;
            return scope.sentFriendshipRequests = data.data.sentFriendshipRequests;
          };
          return this.post(route, {}, {}, success);
        };

        FriendsRequest.prototype.acceptFriendshipRequest = function(route, friendUid) {
          var data;
          data = {
            acceptedFriend: friendUid
          };
          return this.post(route, {}, data);
        };

        FriendsRequest.prototype.createFriendshipRequest = function(route, recipientUid) {
          var data;
          console.log(route);
          data = {
            recipient: recipientUid
          };
          return this.post(route, {}, data);
        };

        FriendsRequest.prototype.removeFriendshipRequest = function(route, userUid, sentOrReceived) {
          var data;
          data = {
            userUid: userUid,
            sentOrReceived: sentOrReceived
          };
          return this.post(route, {}, data);
        };

        FriendsRequest.prototype.getFriendships = function(route, scope) {
          var success;
          success = function(data) {
            return scope.friendships = data.data.friendships;
          };
          return this.post(route, {}, {}, success);
        };

        FriendsRequest.prototype.removeFriendship = function(route, friendship) {
          var data;
          data = {
            friend: friendship
          };
          return this.post(route, {}, data);
        };

        return FriendsRequest;

      })(_Request);
      return FriendsRequest;
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  angular.module('Friends').factory('_FRModel', [
    '_Model', function(_Model) {
      var FRModel;
      FRModel = (function(_super) {

        __extends(FRModel, _super);

        function FRModel() {
          FRModel.__super__.constructor.call(this);
        }

        return FRModel;

      })(_Model);
      return FRModel;
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {
  var __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  angular.module('Friends').factory('_FacebookModel', [
    '_Model', function(_Model) {
      var FacebookModel;
      FacebookModel = (function(_super) {

        __extends(FacebookModel, _super);

        function FacebookModel() {
          FacebookModel.__super__.constructor.call(this);
        }

        return FacebookModel;

      })(_Model);
      return FacebookModel;
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').factory('FriendsRequest', [
    '$http', '$rootScope', 'Config', '_FriendsRequest', 'Publisher', function($http, $rootScope, Config, _FriendsRequest, Publisher) {
      return new _FriendsRequest($http, $rootScope, Config, Publisher);
    }
  ]);

  angular.module('Friends').factory('FRModel', [
    '_FRModel', function(_FRModel) {
      return new _FRModel();
    }
  ]);

  angular.module('Friends').factory('Publisher', [
    '_Publisher', 'FRModel', function(_Publisher, FRModel) {
      var publisher;
      publisher = new _Publisher();
      publisher.subscribeModelTo(FRModel, 'friendshiprequests');
      return publisher;
    }
  ]);

  angular.module('Friends').factory('FriendshipModel', [
    '_FriendshipModel', function(_FriendshipModel) {
      return new _FriendshipModel();
    }
  ]);

  angular.module('Friends').factory('Publisher', [
    '_Publisher', 'FriendshipModel', function(_Publisher, FriendshipModel) {
      var publisher;
      publisher = new _Publisher();
      publisher.subscribeModelTo(FriendshipModel, 'friendships');
      return publisher;
    }
  ]);

  angular.module('Friends').factory('FacebookModel', [
    '_FacebookModel', function(_FacebookModel) {
      return new _FacebookModel();
    }
  ]);

  angular.module('Friends').factory('Publisher', [
    '_Publisher', 'FacebookModel', function(_Publisher, FacebookModel) {
      var publisher;
      publisher = new _Publisher();
      publisher.subscribeModelTo(FacebookModel, 'facebook');
      return publisher;
    }
  ]);

}).call(this);



/*
# ownCloud
#
# @author Bernhard Posselt
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('OC').factory('_Model', function() {
    var Model;
    Model = (function() {

      function Model() {
        this.foreignKeys = {};
        this.data = [];
        this.ids = {};
      }

      Model.prototype.handle = function(data) {
        var item, _i, _j, _k, _len, _len1, _len2, _ref, _ref1, _ref2, _results;
        if (data['create'] !== void 0) {
          _ref = data['create'];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            item = _ref[_i];
            this.create(item);
          }
        }
        if (data['update'] !== void 0) {
          _ref1 = data['update'];
          for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
            item = _ref1[_j];
            this.update(item);
          }
        }
        if (data['delete'] !== void 0) {
          _ref2 = data['delete'];
          _results = [];
          for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
            item = _ref2[_k];
            _results.push(this["delete"](item));
          }
          return _results;
        }
      };

      Model.prototype.hasForeignKey = function(name) {
        return this.foreignKeys[name] = {};
      };

      Model.prototype.create = function(data) {
        var id, ids, name, _base, _ref, _results;
        if (this.ids[data.id] !== void 0) {
          return this.update(data);
        } else {
          this.data.push(data);
          this.ids[data.id] = data;
          _ref = this.foreignKeys;
          _results = [];
          for (name in _ref) {
            ids = _ref[name];
            id = data[name];
            (_base = this.foreignKeys[name])[id] || (_base[id] = []);
            _results.push(this.foreignKeys[name][id].push(data));
          }
          return _results;
        }
      };

      Model.prototype.update = function(item) {
        var currentItem, key, value, _results;
        currentItem = this.ids[item.id];
        _results = [];
        for (key in item) {
          value = item[key];
          if (this.foreignKeys[key] !== void 0) {
            if (value !== currentItem[key]) {
              this._updateForeignKeyCache(key, currentItem, item);
            }
          }
          if (key !== 'id') {
            _results.push(currentItem[key] = value);
          } else {
            _results.push(void 0);
          }
        }
        return _results;
      };

      Model.prototype["delete"] = function(item) {
        if (this.getById(item.id) !== void 0) {
          return this.removeById(item.id);
        }
      };

      Model.prototype._updateForeignKeyCache = function(name, currentItem, toItem) {
        var foreignKeyItems, fromValue, toValue;
        fromValue = currentItem[name];
        toValue = toItem[name];
        foreignKeyItems = this.foreignKeys[name][fromValue];
        this._removeForeignKeyCacheItem(foreignKeyItems, currentItem);
        return this.foreignKeys[name][toValue].push(item);
      };

      Model.prototype._removeForeignKeyCacheItem = function(foreignKeyItems, item) {
        var fkItem, index, _i, _len, _results;
        _results = [];
        for (index = _i = 0, _len = foreignKeyItems.length; _i < _len; index = ++_i) {
          fkItem = foreignKeyItems[index];
          if (fkItem.id === id) {
            _results.push(this.foreignKeys[key][item[key]].splice(index, 1));
          } else {
            _results.push(void 0);
          }
        }
        return _results;
      };

      Model.prototype.removeById = function(id) {
        var foreignKeyItems, ids, index, item, key, _i, _len, _ref, _ref1;
        item = this.getById(id);
        _ref = this.foreignKeys;
        for (key in _ref) {
          ids = _ref[key];
          foreignKeyItems = ids[item[key]];
          this._removeForeignKeyCacheItem(foreignKeyItems, item);
        }
        _ref1 = this.data;
        for (index = _i = 0, _len = _ref1.length; _i < _len; index = ++_i) {
          item = _ref1[index];
          if (item.id === id) {
            this.data.splice(index, 1);
          }
        }
        return delete this.ids[id];
      };

      Model.prototype.getById = function(id) {
        return this.ids[id];
      };

      Model.prototype.getAll = function() {
        return this.data;
      };

      Model.prototype.getAllOfForeignKeyWithId = function(foreignKeyName, foreignKeyId) {
        return this.foreignKeys[foreignKeyName][foreignKeyId];
      };

      return Model;

    })();
    return Model;
  });

}).call(this);



/*
# ownCloud
#
# @author Bernhard Posselt
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('OC').factory('_Request', function() {
    var Request;
    Request = (function() {

      function Request($http, $rootScope, Config, publisher) {
        var _this = this;
        this.$http = $http;
        this.$rootScope = $rootScope;
        this.Config = Config;
        this.publisher = publisher;
        this.initialized = false;
        this.shelvedRequests = [];
        this.$rootScope.$on('routesLoaded', function() {
          var req, _i, _len, _ref;
          _ref = _this.shelvedRequests;
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            req = _ref[_i];
            _this.post(req.route, req.routeParams, req.data, req.onSuccess, req.onFailure);
          }
          _this.initialized = true;
          return _this.shelvedRequests = [];
        });
      }

      Request.prototype.post = function(route, routeParams, data, onSuccess, onFailure) {
        var headers, postData, request, url,
          _this = this;
        if (!this.initialized) {
          request = {
            route: route,
            routeParams: routeParams,
            data: data,
            onSuccess: onSuccess,
            onFailure: onFailure
          };
          this.shelvedRequests.push(request);
          return;
        }
        if (routeParams) {
          url = OC.Router.generate(route, routeParams);
        } else {
          url = OC.Router.generate(route);
        }
        data || (data = {});
        postData = $.param(data);
        headers = {
          headers: {
            'requesttoken': oc_requesttoken,
            'Content-Type': 'application/x-www-form-urlencoded'
          }
        };
        return this.$http.post(url, postData, headers).success(function(data, status, headers, config) {
          var name, value, _ref, _results;
          if (onSuccess) {
            onSuccess(data);
          }
          _ref = data.data;
          _results = [];
          for (name in _ref) {
            value = _ref[name];
            _results.push(_this.publisher.publishDataTo(name, value));
          }
          return _results;
        }).error(function(data, status, headers, config) {
          if (onFailure) {
            return onFailure(data);
          }
        });
      };

      return Request;

    })();
    return Request;
  });

}).call(this);



/*
# ownCloud
#
# @author Bernhard Posselt
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


/*
# Used for properly distributing received model data from the server
*/


(function() {

  angular.module('OC').factory('_Publisher', function() {
    var Publisher;
    Publisher = (function() {

      function Publisher() {
        this.subscriptions = {};
      }

      Publisher.prototype.subscribeModelTo = function(model, name) {
        var _base;
        (_base = this.subscriptions)[name] || (_base[name] = []);
        return this.subscriptions[name].push(model);
      };

      Publisher.prototype.publishDataTo = function(data, name) {
        var subscriber, _i, _len, _ref, _results;
        _ref = this.subscriptions[name] || [];
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          subscriber = _ref[_i];
          _results.push(subscriber.handle(data));
        }
        return _results;
      };

      return Publisher;

    })();
    return Publisher;
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {
  var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  angular.module('Friends').factory('_FacebookController', function() {
    var FacebookController;
    FacebookController = (function() {

      function FacebookController($scope, config, request, facebookModel) {
        var _this = this;
        this.$scope = $scope;
        this.config = config;
        this.request = request;
        this.facebookModel = facebookModel;
        this.confirmSetup = __bind(this.confirmSetup, this);

        this.$scope.saveName = function(name) {
          return _this.saveName(name);
        };
        this.$scope.confirmSetup = function(facebookUrl) {
          return _this.confirmSetup(facebookUrl);
        };
      }

      FacebookController.prototype.saveName = function(name) {
        return this.request.saveName(this.config.routes.saveNameRoute, name);
      };

      FacebookController.prototype.confirmSetup = function(facebookUrl) {
        if (confirm("The sync will occur with the user currently logged in to Facebook.  If there is no logged in user, you will be prompted to log in.  Please confirm that someone else is not logged into Facebook on this computer.  Then press OK to continue.")) {
          window.location = facebookUrl;
          return console.log("confirmed");
        }
      };

      return FacebookController;

    })();
    return FacebookController;
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').factory('_ExampleController', function() {
    var ExampleController;
    ExampleController = (function() {

      function ExampleController($scope, config, request, itemModel) {
        var _this = this;
        this.$scope = $scope;
        this.config = config;
        this.request = request;
        this.itemModel = itemModel;
        this.$scope.saveName = function(name) {
          return _this.saveName(name);
        };
      }

      ExampleController.prototype.saveName = function(name) {
        return this.request.saveName(this.config.routes.saveNameRoute, name);
      };

      return ExampleController;

    })();
    return ExampleController;
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').factory('_FriendshipController', function() {
    var FriendshipController;
    FriendshipController = (function() {

      function FriendshipController($scope, config, request, friendshipModel) {
        var _this = this;
        this.$scope = $scope;
        this.config = config;
        this.request = request;
        this.friendshipModel = friendshipModel;
        this.$scope.$on('routesLoaded', function() {
          return _this.getFriendships(_this.$scope);
        });
        this.$scope.removeFriendship = function(friendship) {
          return _this.removeFriendship(friendship);
        };
      }

      FriendshipController.prototype.getFriendships = function(scope) {
        return this.request.getFriendships(this.config.routes.getFriendshipsRoute, scope);
      };

      FriendshipController.prototype.removeFriendship = function(friendship) {
        return this.request.removeFriendship(this.config.routes.removeFriendshipRoute, friendship);
      };

      return FriendshipController;

    })();
    return FriendshipController;
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').factory('_FRController', function() {
    var FRController;
    FRController = (function() {

      function FRController($scope, config, request, frModel) {
        var _this = this;
        this.$scope = $scope;
        this.config = config;
        this.request = request;
        this.frModel = frModel;
        this.$scope.saveName = function(name) {
          return _this.saveName(name);
        };
        this.$scope.acceptFriendshipRequest = function(requestor) {
          return _this.acceptFriendshipRequest(requestor);
        };
        this.$scope.createFriendshipRequest = function(recipient) {
          return _this.createFriendshipRequest(recipient);
        };
        this.$scope.removeSentFriendshipRequest = function(recipientUid) {
          return _this.removeSentFriendshipRequest(recipientUid);
        };
        this.$scope.removeReceivedFriendshipRequest = function(requesterUid) {
          return _this.removeReceivedFriendshipRequest;
        };
        this.$scope.$on('routesLoaded', function() {
          return _this.getFriendshipRequests(_this.$scope);
        });
      }

      FRController.prototype.saveName = function(name) {
        return this.request.saveName(this.config.routes.saveNameRoute, name);
      };

      FRController.prototype.acceptFriendshipRequest = function(friendUid) {
        return this.request.acceptFriendshipRequest(this.config.routes.acceptFriendshipRequestRoute, friendUid);
      };

      FRController.prototype.createFriendshipRequest = function(recipient) {
        return this.request.createFriendshipRequest(this.config.routes.createFriendshipRequestRoute, recipient);
      };

      FRController.prototype.getFriendshipRequests = function(scope) {
        return this.request.getFriendshipRequests(this.config.routes.getFriendshipRequestsRoute, scope);
      };

      FRController.prototype.removeSentFriendshipRequest = function(recipientUid) {
        console.log("in removeSentFR");
        return this.request.removeFriendshipRequest(this.config.routes.removeFriendshipRequestRoute, recipientUid, 'sent');
      };

      FRController.prototype.removeReceivedFriendshipRequest = function(requesterUid) {
        return this.request.removeFriendshipRequest(this.config.routes.removeFriendshipRequestRoute, requesterUid, 'received');
      };

      return FRController;

    })();
    return FRController;
  });

}).call(this);



/*
# ownCloud
#
# @author Sarah Jones
# Copyright (c) 2012 - Bernhard Posselt <nukeawhale@gmail.com>
#
# This file is licensed under the Affero General Public License version 3 or later.
# See the COPYING-README file
#
*/


(function() {

  angular.module('Friends').controller('FRController', [
    '$scope', 'Config', 'FriendsRequest', '_FRController', 'FRModel', function($scope, Config, FriendsRequest, _FRController, FRModel) {
      return new _FRController($scope, Config, FriendsRequest, FRModel);
    }
  ]);

  angular.module('Friends').controller('FacebookController', [
    '$scope', 'Config', 'FriendsRequest', '_FacebookController', 'FacebookModel', function($scope, Config, FriendsRequest, _FacebookController, FacebookModel) {
      return new _FacebookController($scope, Config, FriendsRequest, FacebookModel);
    }
  ]);

  angular.module('Friends').controller('FriendshipController', [
    '$scope', 'Config', 'FriendsRequest', '_FriendshipController', 'FriendshipModel', function($scope, Config, FriendsRequest, _FriendshipController, FriendshipModel) {
      return new _FriendshipController($scope, Config, FriendsRequest, FriendshipModel);
    }
  ]);

}).call(this);
