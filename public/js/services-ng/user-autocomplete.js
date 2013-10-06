ABTApp.service('acUsers', function() {
	return {
		addTo: function(scope) {
			var baseFunctions = {
				getQueryUrl: function(query) {
					//			return where to get the result data set
					return '/users';
				},
				resultTemplate: function() {
					//			return template for displaying data set
					return '<p>{{email}}</p>';
				},
				usersACResult: function(o) {
					o.value = o.email;
					o.tokens = [o.username, o.email, o.displayName];
					return o;
				}

			};
			var extension = $.extend({}, arguments[1] || {});
			scope.onSelectValue = function(object) {
				//	return value to set in autocomplete
				return object.email;
			};
			scope.usersACDataSet = function() {
				var set = [$.extend({}, baseFunctions, {
						getPostData: function(query) {
							//			return the post data to be sent with request
							return {email: query};
						}
					}, extension)];
				return set;
			}
		}
	};
})