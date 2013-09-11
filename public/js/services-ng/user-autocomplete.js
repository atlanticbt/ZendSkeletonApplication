ABTApp.service('acUsers', function() {
	return {
		addTo: function(scope) {
			$.extend(scope, {
				onSelectValue: function(object) {
					//	return value to set in autocomplete
					return object.email;
				},
				getQueryUrl: function(query) {
					//			return where to get the result data set
					return '/users';
				},
				getPostData: function(query) {
					//			return the post data to be sent with request
					return {name: query};
				},
				resultTemplate: function() {
					//			return template for displaying data set
					return '<p>{{username}} ({{email}})</p>';
				}
			});
		}
	};
})