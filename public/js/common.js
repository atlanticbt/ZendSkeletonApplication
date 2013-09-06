var common = {
	init: function() {
	},
	alert: function(msg) {
		var options = $.extend({
			type: 'error',
			expires: 5,
			prependTo: 'body > div.container:first',
		}, arguments[1] || {});
		var cls = '';

		if (options.type == 'error') {
			cls = 'alert';
		} else if (options.type == 'success') {
			cls = 'success';
		}
		// fallback, in case specified prependTo does not exist.
		if ($(options.prependTo).length < 1) {
			options.prependTo = 'body';
		}
		/**
		 * <div class="alert-box radius cls">
		 msg
		 </div>
		 */
		var alert = $('<div data-alert class="alert-box radius ' + cls + '">' +
				msg +
				'<a href="javascript:void(null)" class="close">&times;</a>' +
				'</div>').prependTo(options.prependTo);
		if (options.expires > 0) {
			alert.data('expire-timeout', setTimeout(function() {
				alert.fadeOut('fast', function() {
					$(this).remove()
				});
			}, options.expires * 1000));
		}
	},
	/**
	 * Adds error class/messages to form elements
	 * @param {type} errors
	 * @returns {unresolved}
	 */
	formErrors: function(errors) {
		if (!errors) {
			return;
		}
		var options = $.extend({}, arguments[1]);
		if (errors === true) {
			$('div.error').removeClass('error').find('small:last').remove();
			return;
		}
		if (typeof errors == 'string') {
			common.alert(errors);
			return;
		}
		var err = [];
		$.each(errors, function(section, errorList) {
			var formInput = $(':input[name=' + section + ']');
			var fieldErrors = [];
			$.each(errorList, function(errorType, errorMsg) {
				fieldErrors.push(errorMsg);
			});
			if (formInput.length > 0) {
				// add the error to the field
				formInput.parents('div:first').addClass('error').append('<small>' + fieldErrors.join(', ') + '</small>')
			} else {
				err = err.concat(fieldErrors);
			}
		});
		if (err.length > 0) {
			common.alert('<ul class="no-bullet" style="margin-bottom:0;"><li>' + err.join('</li><li>') + '</li></ul>', options);
		}

	}
};

$(document).ready(common.init);

var ABTApp = angular.module('ABT', []);

ABTApp.service('pageFilterService', function($rootScope) {
	var filters = {};
	return {
		getFilter: function(key) {
			return filters[key];
		},
		setFilter: function(name, value) {
			if (value === null && (typeof filters[name] == 'undefined' || filters[name] === null)) {
				return;
			}
			// ignore setting filters for null values if the value is already null or not defined.
			filters[name] = value;
			$rootScope.$broadcast('pageFilterChanged', filters);
		},
		removeFilter: function(name) {
			if (filters[name]) {
				delete filters[name];
				$rootScope.$broadcast('pageFilterChanged', filters);
			}
		},
		clearFilters: function() {
			filters = {};
			$rootScope.$broadcast('pageFilterChanged', filters);
		}
	};
}).service('pageSharedService', function() {
	var sharedData = {};
	return {
		data: function() {
			if (typeof arguments[0] != 'undefined') {
				return sharedData[arguments[0]];
			}
			return sharedData;
		},
		append: function(key, value) {
			if ($.isArray(sharedData[key])) {
				sharedData[key].concat(value);
			} else {
				sharedData[key] = $.extend(sharedData[key], value);
			}
			return this;
		},
		appendAll: function(data) {
			var thisService = this;
			$.each(data, function(key, value) {
				thisService.append(key, value);
			});
		},
		replace: function(key, value) {
			sharedData[key] = value;
			return this;
		}
	};
});

;

/**
 * Generic autocomplete directive.
 * Define a text element <input type="text" auto-complete />
 * Specify the name of the scope function to call for source, select and focus events
 * by specifying fn-source="scopeFunctionName", fn-select="...", fn-focus="..." attributes
 * in the element respectively. Those callbacks should have the following signatures
 *
 * fnSource = function(term, callback, [dom element], [object containing element attribute key/value pairs]})
 * fnSelect = function([select event],[element selected], [dom element], [object containing element attribute key/value pairs])
 * fnFocus = function([focus event],[element selected], [dom element], [object containing element attribute key/value pairs])
 *
 * @param {type} param1
 * @param {type} param2
 */
ABTApp.directive('autoComplete', function($rootScope) {
	return function(scope, iElement, iAttrs) {
		// prevent enter from submitting the form
		iElement.on('keypress', function(ev) {
			if (ev.which == 13) {
				ev.preventDefault();
				return false;
			}
		});
		// autocomplete plugin
		iElement.autocomplete({
			source: function(query, response) {
				if (scope[iAttrs.fnSource] && typeof scope[iAttrs.fnSource] == 'function') {
					scope[iAttrs.fnSource](query.term, response, iElement, iAttrs);
				}
			},
			select: function(ev, selected) {
				if (scope[iAttrs.fnSelect] && typeof scope[iAttrs.fnSelect] == 'function') {
					ev.preventDefault();
					scope[iAttrs.fnSelect](ev, selected, iElement, iAttrs);
					// broadcast selection
					$rootScope.$broadcast('autoCompleteSelected', selected, iElement, iAttrs, ev);
					return false;
				}
			},
			focus: function(ev, ui) {
				ev.preventDefault();
				if (scope[iAttrs.fnFocus] && typeof scope[iAttrs.fnFocus] == 'function') {
					scope[iAttrs.fnFocus](ev, ui, iElement, iAttrs);
				}
				return false;
			}
		});
	};
}).directive('pageOrder', function($rootScope, pageFilterService) {

	return function(scope, iElement, iAttrs) {
		var name = iAttrs['pageOrder'];
		if (name) {
			iElement.click(function() {
				var currentOrder = pageFilterService.getFilter('order');
				var desc = false;
				if (currentOrder && currentOrder[0] == '-') {
					currentOrder = currentOrder.substr(1);
					var desc = true;
				}
				var nowDescending = (name == currentOrder && !desc);
				var descendingClass = 'tbl-sort-up';
				var ascendingClass = 'tbl-sort-down';
				iElement.addClass(nowDescending ? descendingClass : ascendingClass).removeClass(nowDescending ? ascendingClass : descendingClass);
				iElement.siblings('th').removeClass(descendingClass + ' ' + ascendingClass);
				pageFilterService.setFilter('order', (nowDescending ? '-' : '') + name);
			}).addClass('tbl-sort');
		}
	};
});
