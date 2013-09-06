<?php

namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\JsonModel;

class JsonResponse extends AbstractPlugin
{

	/**
	 * Create a json response in a standardized format.
	 * @param type $success
	 * @param type $message
	 * @param type $data
	 * @return \Zend\View\Model\JsonModel
	 */
	public function __invoke($success = false, $message = '', $data = null)
	{
		if (empty($data)) {
			$data = array();
		}
		if (!is_array($data)) {
			$data = array('data' => $data);
		}
		$params = array_merge(array('msg' => $message), $data, array('success' => $success ? true : false));
		return new JsonModel($params);
	}

}
