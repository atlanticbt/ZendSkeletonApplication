<?php

namespace Application\Controller\Plugin;

use Zend\View\Model\JsonModel;

class JsonResponse extends BasePlugin
{
	/**
	 * Create a json response in a standardized format.
	 * @param type $success
	 * @param type $message
	 * @param type $data
	 * @return \Zend\View\Model\JsonModel
	 */
	public function __invoke($success = false, $message = '', $data = null, $flatten = true)
	{
		if (empty($data)) {
			$data = array();
		} else if ($flatten) {
			$data = $this->flatten($data);
		}
		if (!is_array($data)) {
			$data = array('data' => $data);
		}
		$params = array_merge(array('msg' => $message), $data, array('success' => $success ? true : false));
		return new JsonModel($params);
	}

	public function flatten($data) {
		$flattener = $this->getServiceLocator()->get('rflatten');
		return $flattener($data);
	}

}
