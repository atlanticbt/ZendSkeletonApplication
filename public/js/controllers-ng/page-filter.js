function PageFilterCtrl($scope, pageFilterService) {
	$scope.setFilter = function(scopeName) {
		pageFilterService.setFilter(scopeName, $scope[scopeName]);
	};
	$scope.removeFilter = function(filterName) {
		pageFilterService.removeFilter(filterName);
	}
	$scope.setFilterValue = function(filterName, filterValue) {
		pageFilterService.setFilter(filterName, filterValue);
	};
	$scope.clearFilters = function() {

	};
	$scope.$on('autoCompleteSelected', function(event, selection, element, attrs, selectionEvent) {
		if (attrs['filterName']) {
			$scope.setFilterValue(attrs['filterName'], selection.item.value);
		}
	});

	$scope.$on('pageFilterChanged', function(event, filters) {
		$scope.filters = filters;
	});
	$scope.$watch('filters', function() {
	}, true);
}