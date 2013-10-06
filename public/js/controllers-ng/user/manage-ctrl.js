function ManageUserCtrl($scope, $http, abtPost) {
	$scope.init = function(data) {
		$scope.user = data.user;
		$scope.roleMap = data.roleMap;
        $scope.resetUrl = data.resetUrl;
        $scope.company = data.company;
	}
	$scope.getRoleDisplayName = function() {
		var role = 'User';
		angular.forEach($scope.roleMap, function(map) {
			if ($scope.user.role == map.role) {
				role = map.name;
			}
		});
		return role;
	};
	$scope.onFormSubmit = function() {
		// clear any existing form errors.
		common.formErrors();
		abtPost.send(window.location.href, $scope.user, function(data, status, headers, config) {
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
    $scope.sendReset = function(user) {
        abtPost.send($scope.resetUrl,{user: user.id}, function(){
            common.alert('User has been sent a password reset email.',{type:'success'});
        },function(){
            common.alert('Unable to reset user password at this time.')
        });
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