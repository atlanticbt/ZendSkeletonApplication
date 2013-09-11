ABTApp.service('acUsers', function() {
	return {
		addTo: function(scope) {
			var baseFunctions = {
				onSelectValue: function(object) {
					//	return value to set in autocomplete
					return object.email;
				},
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

			}
			scope.usersACDataSet = function() {
				var sets = [
					$.extend({}, baseFunctions, {
						getPostData: function(query) {
							//			return the post data to be sent with request
							return {email: query};
						}
					})
				];
				return sets;
			}
		}
	};
})