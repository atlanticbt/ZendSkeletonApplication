var common = {
	init: function() {
	},
	alert: function(msg) {
		var options = $.extend({
			type: 'error',
			expires: 5,
			prependTo: 'body > div.container:first',
		}, arguments[1] || {});
		var cls = 'info';

		if (options.type == 'error') {
			cls = 'danger';
		} else if (options.type == 'success') {
			cls = 'success';
		}
		// fallback, in case specified prependTo does not exist.
		if ($(options.prependTo).length < 1) {
			options.prependTo = 'body';
		}
		var alert = $('<div data-alert class="alert alert-dismissable alert-' + cls + '">' +
				'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
				msg +
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
			$('div.has-error').removeClass('has-error').find('span.help-block').remove();
			return;
		}
		var options = $.extend({}, arguments[1]);
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
				formInput.parents('div:first').parents('div:first').addClass('has-error').end().append('<span class="help-block">' + fieldErrors.join(', ') + '</span>')
			} else {
				err = err.concat(fieldErrors);
			}
		});
		if (err.length > 0) {
			common.alert('<ul><li>' + err.join('</li><li>') + '</li></ul>', options);
		}

	},
	getScopeFunction: function(key, scope, fallback) {
		try {
			if (key && typeof scope[key] == 'function') {
				return scope[key];
			}
		} catch (e) {
		}
		return fallback;
	}
};

$(document).ready(common.init);

var ABTApp = angular.module('ABT', []);

ABTApp.service('abtPost', function($http) {
	return {
		headers: function() {
			return {
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			};
		},
		params: function(data) {
			if (!data) {
				data = {};
			}
			return $.param(data);
		},
		send: function(url, data, success, fail) {
			return $http.post(url, this.params(data), this.headers())
					.success(function(data, status, headers, config) {
				if (!data || !data.success) {
					fail(data && data.msg ? data.msg : 'The request failed', data, status, headers, config);
					return;
				}
				success(data, status, headers, config);
			}).error(function(data, status, headers, config) {
				fail('The request failed.', data, status, headers, config);
			});
		}
	}
}).service('pageFilterService', function($rootScope) {
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
}).service('uploadService', function($rootScope) {
	var upload = {
		inProgress: false
	};

	return {
		info: function() {
			return upload;
		},
		notifyUploadStarted: function(event, data) {
			upload.inProgress = true;
			$rootScope.$broadcast('uploadStarted', data);
		},
		notifyUploadStopped: function(event, data) {
			upload.inProgress = false;

			$rootScope.$broadcast('uploadStopped', data);
		}
	};
});

/**
 * Generic autocomplete directive.
 * Define a text element <input type="text" auto-complete="[[data cache name]]" />
 * Specify the name of the scope function to call for various configurations
 * by specifying fn-source="scopeFunctionName" ... attributes
 * in the element respectively. Those callbacks should have the following signatures:
 *
 *
 * value-on-select: onSelectValue({selected dataset object}): return value to set in autocomplete
 * url: getQueryUrl(query): return where to get the result data set
 * post-data: getPostData(query): return the post data to be sent with request
 * result-template: resultTemplate(): return template for displaying data set
 * filter-results: filterResults(response): return formatted data set to display
 * transform-result: transformResult(result): return result formatted as you wish
 */
ABTApp.directive('autoComplete', function($rootScope, $timeout) {
	return function(scope, iElement, iAttrs) {
		// prevent enter from submitting the form
		iElement.on('keypress', function(ev) {
			if (ev.which == 13) {
				ev.preventDefault();
				return false;
			}
		});
		var cacheName = iAttrs.autoComplete;
		var onSelectValue = common.getScopeFunction(iAttrs.valueOnSelect, scope, function(object) {
			return object.id;
		});
		var resultTemplate = common.getScopeFunction(iAttrs.resultTemplate, scope, function() {
			return '<p>{{name}}</p>';
		});
		var queryUrl = common.getScopeFunction(iAttrs.url, scope, function(query) {
			return window.location.href;
		});
		var queryData = common.getScopeFunction(iAttrs.postData, scope, function(query) {
			return {};
		});

		var transformResult = common.getScopeFunction(iAttrs.transformResult, scope, common.getScopeFunction(cacheName + 'ACResult', scope, function(o) {
			return o;
		}));

		var filterResults = common.getScopeFunction(iAttrs.filterResults, scope, function(response) {
			var data = [];
			$.each(response.page.data, function(i, o) {
				data.push(transformResult(o));
			});
			return data;
		});
		iElement.typeahead({
			// name of the dataset so the plugin can cache intelligently
			name: cacheName,
			remote: {
				url: '%QUERY',
				valueKey: 'email',
				beforeSend: function(jqXHR, settings) {
					settings.type = "POST";
					var query = settings.url;
					settings.url = queryUrl(query);
					settings.data = queryData(query);
				},
				filter: filterResults
			},
			template: resultTemplate(),
			engine: Hogan
		}).on('typeahead:selected', function(event, object) {
			$timeout(function() {
				// global event
				$rootScope.$broadcast('autoCompleteSelected', object, iElement, iAttrs, event);
				// targeted event
				$rootScope.$broadcast('autocomplete-' + cacheName, object, iElement, iAttrs, event);
			}, 0);
			iElement.typeahead('setQuery', onSelectValue(object));
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
})
		/**
		 * This directive activates the file upload plugin functionality for a file form element.
		 *
		 * Adding 'angular-upload' as the element attribute will cause that element to be
		 * targeted by this directive.
		 * example: <input type="file" angular-upload />
		 *
		 * You can supply a value to this attribute which will be used as post data
		 * to be sent along with the file upload as such:
		 * example: <input type="file" angular-upload="{'paramOne':'paramOneValue'}" />
		 *
		 * Additionally, you can supply another attribute to point to a scope function
		 *
		 * <input type="file" angular-upload="{'paramOne':'paramOneValue'}" data-on-upload="someFunctionName" />
		 *
		 * This will cause $scope.someFunctionName() to be invoked at the time of upload.
		 * The function gets the following parameters: the calculated data from the angular-upload attribute (if any)
		 * in the form of [{name: 'paramOne', value: 'paramOneValue'}], the file upload element, and the object containing
		 * all the element's attributes/values.
		 *
		 */
		.directive('angularUpload', function($timeout, uploadService) {
	return function(scope, element, attrs) {
		var data = [];
		try {
			if (attrs.angularUpload) {
				var uploadData = $.parseJSON(attrs.angularUpload);
				$.each(uploadData, function(key, value) {
					data.push({name: key, value: value});
				});
			}
		} catch (e) {
			console.error('Unable to parse file upload data', e);
		}
		// allow scope function use too
		var onUpload = null;
		if (attrs.onUpload) {
			onUpload = scope[attrs.onUpload];
		}
		var url = attrs.destination ? attrs.destination : window.location.href;
		var config = {};
		try {
			if (attrs.uploadConfig) {
				$.extend(config, $.parseJSON(attrs.uploadConfig));
			}
		} catch (e) {
			console.error('Unable to parse file upload config', e);
		}
		var doneFn = (attrs.onComplete && scope[attrs.onComplete] && typeof scope[attrs.onComplete] == 'function') ? scope[attrs.onComplete] : function() {
		};
		$(element).fileupload($.extend(config, {
			url: url,
			send: function(e, data) {
				$timeout(function() {
					uploadService.notifyUploadStarted(e, data);
				}, 0);
			},
			formData: function() {
				var runtimeData = [];
				try {
					var onUploadData = (typeof onUpload == 'function') ? onUpload(data, element, attrs) : {};
					$.each(onUploadData, function(k, v) {
						runtimeData.push({name: k, value: v});
					});
				} catch (e) {
					console.errro('Unable to calculate runtime data');
				}
				return data.concat(runtimeData);
			},
			done: function(ev, data) {
				try {
					$timeout(function() {
						doneFn(ev, data.result, data);
					}, 0);
				} catch (e) {
				}
			},
			always: function(e, data) {
				$timeout(function() {
					uploadService.notifyUploadStopped(e, data);
				}, 0);
			}
		}));
	};
});
