function ManageUserCtrl($scope, $http, abtPost) {
	$scope.onFormSubmit = function() {
		// clear any existing form errors.
		common.formErrors();
		abtPost.send(window.location.href, $scope.form, function(data, status, headers, config) {
			common.alert('Saved changes', {type: 'success'});
		}, function(msg, data, status, headers, config) {
			common.formErrors(msg);
		});
	}
}