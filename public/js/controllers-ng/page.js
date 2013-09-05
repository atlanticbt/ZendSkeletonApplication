/**
 * Controller to paginate results. Seed with json data in ng-init attribute
 * which calls setPageData() passing json page result
 */

function PageCtrl($scope, $http, $timeout, $rootScope, pageSharedService) {

	$scope.options = function() {
		if (!$scope.pageOptions) {
			$scope.pageOptions = {
				source: window.location.href,
				buttons: {before: 3, after: 3}
			};
		}
		if (arguments.length) {
			$scope.pageOptions = $.extend($scope.pageOptions, arguments[0] || {});
			if ($scope.pageOptions.pageTag) {
				$scope.pageTag = $scope.pageOptions.pageTag;
			}
		}
		return $scope.pageOptions;
	};

	$scope.setPageData = function(page) {
		$scope.options(page.options);
		if (page.shared) {
			pageSharedService.appendAll(page.shared);
		}
		if (!page.data) {
			$scope.loadPage(1);
			return;
		}
		$scope.setEntries(page.data);
		$scope.setMeta(page.meta);
		$rootScope.$broadcast('pageLoaded', $scope.pageOptions.source, page, $scope.pageTag);
	};
	$scope.setEntries = function(entries) {
		$scope.entries = entries;
	};
	$scope.setMeta = function(meta) {
		$scope.meta = meta;
		var current = $scope.currentPage();
		$scope.pagesBack = [];
		if (current > 1) {
			// add previous pages
			for (var i = current - 4; i < current; i++) {
				if (i < 1) {
					i = 0;
					continue;
				}
				$scope.pagesBack.push({number: i});
			}
		}
		var total = $scope.totalPages();
		$scope.pagesForward = [];
		if (current < total) {
			// add next pages
			for (var i = current + 1; i < current + 4; i++) {
				$scope.pagesForward.push({number: i});
				if (i >= total) {
					break;
				}
			}
		}
	};
	$scope.loadPage = function(page) {
		var totalPages = $scope.totalPages();
		if (page <= 0 || (totalPages > 0 && page > totalPages)) {
			return;
		}
		var data = $.extend({}, $scope.filters, {offset: !$scope.meta ? null : ((page - 1) * $scope.meta.limit), limit: !$scope.meta ? null : $scope.meta.limit});
		$scope.loadingPage = true;
		var msg = $scope.options().pageLoadFail;
		if (!msg) {
			msg = 'Unable to load page.';
		}
		$http.post($scope.pageOptions.source, $.param(data), {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).success(function(data, status) {
			$scope.loadingPage = false;
			if (!data || !data.page) {
				common.alert(msg);
				return;
			}
			$scope.setPageData(data.page);
		}).error(function(data, status, headers, config) {
			$scope.loadingPage = false;
			// display error message
			common.alert(msg);
		});
	};
	$scope.currentPage = function() {
		return !$scope.meta || !$scope.meta.limit ? 0 : parseInt(($scope.meta.offset / $scope.meta.limit) + 1);
	};
	$scope.totalPages = function() {
		return !$scope.meta || !$scope.meta.limit ? 0 : Math.ceil($scope.meta.total / $scope.meta.limit);
	};

	if ($scope.pageControllerData) {
		$scope.setPageData($scope.pageControllerData);
		$scope.pageControllerData = null;
	}
	$scope.filters = {};

	$scope.loadingPage = false;

	// watchers
	$scope.$watch('meta', function() {
	}, true);

	$scope.$on('pageFilterChanged', function(event, filters) {
		$scope.filters = filters;
		$scope.loadPage(1);
	});
	$scope.$on('pageInject', function(event, pageData) {
		$scope.setPageData(pageData);
	});

	$scope.$on('pageRefresh', function(event, target, except) {
		// skip if target param is non-null and this does not match
		if ((target && $scope.pageTag != target) || (except && $scope.pageTag == except)) {
			// not targeting us, skip
			return;
		}
		var page = $scope.currentPage();
		$scope.loadPage(page);
	});

	$scope.$on('pageAppendEntry', function(event, entry) {
		// allow targeted adding of entries
		if (typeof arguments[2] != 'undefined' && $scope.pageTag != arguments[2]) {
			// not targeting us, skip
			return;
		}
		// make sure we're about to append to an array
		if (!$.isArray($scope.entries)) {
			$scope.entries = [];
		}
		// is the parameter a single entry, or an array of them
		var entries = $.isArray(entry) ? entry : [entry];
		var limit = !$scope.meta || !$scope.meta.limit ? 10 : $scope.meta.limit;
		var diff = limit - $scope.entries.length;
		if (diff > 0) {
			$scope.entries.push.apply($scope.entries, entries.slice(0, diff));
		}
		// update meta regardless
		$scope.setMeta({
			offset: ($scope.meta && $scope.meta.offset ? $scope.meta.offset : 0),
			limit: ($scope.meta && $scope.meta.limit ? $scope.meta.limit : 10),
			total: ($scope.meta && $scope.meta.total ? $scope.meta.total : 0) + 1
		});
	});
}
