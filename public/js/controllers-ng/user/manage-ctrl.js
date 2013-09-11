function ManageUserCtrl($scope, $http, abtPost) {
	$scope.setUser = function(data) {
		$scope.form = data;

	}
	$scope.onFormSubmit = function() {
		console.log($scope.form);
		// clear any existing form errors.
		common.formErrors();
		abtPost.send(window.location.href, $scope.form, function(data, status, headers, config) {
			common.alert('Saved changes', {type: 'success'});
		}, function(msg, data, status, headers, config) {
			common.formErrors(msg);
		});
	};
	$scope.findErrorMessage = function(errors) {
		if (!errors) {
			return '';
		}
		if (typeof errors == 'string') {
			return '<li>' + errors + '</li>';
		}
		var list = '';
		angular.forEach(errors, function(error) {
			list += $scope.findErrorMessage(error)
		});
		return list;
	};
	$scope.form = {};
	$scope.uploading = false;

	$scope.$on('uploadStarted', function() {
		$scope.uploading = true;
	});
	$scope.$on('uploadStopped', function(event, upload) {
		$scope.uploading = false;
		if (upload && upload.result) {
			var data = upload.result;
			if (data.failures && data.failures.length > 0) {
				common.alert('Encountered ' + data.failures.length + ' errors<ul>' + $scope.findErrorMessage(data.failures) + '</ul>', {expires: 0})
			}
			if (data.successes && data.successes.length) {
				common.alert('<ul><li>' + data.successes.join('</li><li>') + '</li></ul>', {type: 'success'});
			}
		}
	});
}