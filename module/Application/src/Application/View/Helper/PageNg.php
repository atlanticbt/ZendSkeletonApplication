<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * @author John F <john.foushee@atlanticbt.com>
 * @company Atlantic Business Technology
 *
 * View helper for outputting page data as a paginated table.
 *
 */
class PageNg extends AbstractHelper
{

	const OPTION_DATA = 'page';
	const OPTION_ROW_HEADINGS = 'tableHeadings';
	const OPTION_ROW_TMP = 'entryRow';
	const OPTION_ROW_SCRIPT = 'entryRowScript';
	const OPTION_ROW_SCRIPT_PARAMS = 'entryRowScriptParams';
	const OPTION_ROW_EMPTY_MESSAGE = 'emptyTableMessage';

	private $_defaultOptions;
	protected $_options = array();

	public function __construct()
	{
		$this->_defaultOptions = array(
			static::OPTION_DATA => null, // the data to paginate
			static::OPTION_ROW_HEADINGS => array('<th>ID</th>'),
			static::OPTION_ROW_TMP => '<td>{{entry.id}}</td>', // template
			static::OPTION_ROW_EMPTY_MESSAGE => 'No results found.',
			'containerId' => '',
			'heading' => 'Results',
			'entryName' => 'entry', //
		);
	}

	public function __invoke()
	{
		return $this;
	}

	public function setOption($optionName, $optionValue)
	{
		$this->_options[$optionName] = $optionValue;
		return $this;
	}

	public function setOptions(array $options)
	{
		if (empty($options) || !is_array($options)) {
			throw new \InvalidArgumentException('Set options expects an array with at least one value');
		}
		$this->_options = $options;
		return $this;
	}

	public function create()
	{
		$options = array_merge($this->_defaultOptions, $this->_options);
		return $this->_render('pagination/page-content.phtml', $options);
	}

	public function emit()
	{
		echo $this->create();
	}

	protected function _render($scriptName, $data = null)
	{
		try {
			return $this->getView()->render($scriptName, $data);
		} catch (\Zend\View\Exception\RuntimeException $e) {
			// filter view script not found
			return $e->getMessage();
		}
	}

	protected function _processOptions($options)
	{
		if (isset($options[static::OPTION_ROW_SCRIPT])) {
			$params = isset($options[static::OPTION_ROW_SCRIPT_PARAMS]) ? $options[static::OPTION_ROW_SCRIPT_PARAMS] : null;
			$options[static::OPTION_ROW_TMP] = $this->_render($options[static::OPTION_ROW_SCRIPT], $params);
		}
		return $options;
	}

}

