<?php

namespace Application\Form;

use Zend\InputFilter\InputFilter;
use ZfcUser\Options\AuthenticationOptionsInterface;

class ChangePasswordFilter extends \ZfcUser\Form\ChangePasswordFilter
{

	public function __construct(AuthenticationOptionsInterface $options, $requireCredential)
	{
		parent::__construct($options);
		if (!$requireCredential) {
			$this->remove('credential');
		}
	}

}
