<?php

namespace DocurepAdmin\Service\Invitation;

use DocurepAdmin\Service\Invitation;
use Zend\View\Model\JsonModel;

class Batch extends Invitation
{

	protected $_succeeded;
	protected $_failed;
	private $_fileParamName = 'user-spreadsheet';
	protected $_fileHeadings;
	protected $_columnHeadings = array(
		'firstName',
		'lastName',
		'email',
	);

	public function process()
	{
		if ($this->permitted($this->getRole())) {
			// check for a file being uploaded
			$files = $this->getRequest()->getFiles()->toArray();
			$this->_message = 'File not detected.';
			if (isset($files[$this->_fileParamName])) {
				$file = $files[$this->_fileParamName];
				return $this->processFile($file['tmp_name'])->success();
			}
		}
		$this->_message = 'You do not have permission to perform this action';
		return false;
	}

	protected function processFile($fileName)
	{
		$this->_succeeded = array();
		$this->_failed = array();
		$columnNamesChecked = false;
		$this->_message = 'Unable to open file';
		if (($handle = fopen($fileName, "r")) !== false) {
			$success = true;
			while (($data = fgetcsv($handle)) !== false) {
				if (empty($data)) {
					continue;
				}
				if (!$columnNamesChecked) {
					$this->_fileHeadings = $data;
					foreach ($this->_columnHeadings as $headingIndex => $expectedHeading) {
						if (!$this->_columnNameCompare($data[$headingIndex], $expectedHeading)) {
							$this->_success = false;
							$this->_message = 'The file is improperly formatted.';
							return $this;
						}
					}
					$columnNamesChecked = true;
					continue;
				}
				$inviteData = $this->_addExtraData($data);
				$inviteData['role'] = $this->getRole();
				if ($this->create($inviteData)->send($this->getInvitedUser())->success()) {
					$this->_succeeded[] = $this->message();
				} else {
					$this->_failed[] = 'Unable to create ' . trim("{$inviteData['firstName']} {$inviteData['lastName']} ({$inviteData['email']})") . ': ' . $this->_findErrorInArray(null, $this->message());
					$success = false;
				}
				$this->_reset();
			}
			fclose($handle);
			$this->_message = count($this->_succeeded) . " invitations sent.";
			$this->_success = $success;
			$this->_jsonData = array(
				'succeeded' => $this->_succeeded,
				'failed' => $this->_failed,
			);
		}
		return $this;
	}

	protected function _findErrorInArray($key, $message)
	{
		if (!is_array($message)) {
			return $key . ': ' . $message;
		}
		foreach ($message as $index => $value) {
			return $key . ': ' . $this->_findErrorInArray($index, $value);
		}
		return null;
	}

	protected function _columnNameCompare($columnName, $standard)
	{
		return strtolower(trim(str_replace(' ', '', $columnName))) == strtolower($standard);
	}

	/**
	 * Returns formatted key/value array for the invite service to consume. Detects
	 * if location/system information is present and adds accordingly.
	 * @param type $data
	 * @return type
	 */
	protected function _addExtraData($data)
	{
		$headings = $this->_columnHeadings;
		if (isset($this->_fileHeadings[count($this->_columnHeadings)])) {
			$extraColumnName = $this->_fileHeadings[count($this->_columnHeadings)];
			if ($this->_columnNameCompare($extraColumnName, 'office') || $this->_columnNameCompare($extraColumnName, 'system')) {
				$headings[] = 'name';
			}
		}
		return array_merge($this->getRequest()->getPost()->toArray(), array_combine($headings, array_slice($data, 0, count($headings))));
	}

}

