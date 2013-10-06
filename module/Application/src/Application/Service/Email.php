<?php

namespace Application\Service;

use Application\Service\BaseService;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\Sendmail as SendmailTransport;

/**
 * service: email
 */
class Email extends BaseService
{

	/**
	 *
	 * @var \Zend\Mail\Message
	 */
	private $_message = null;

	/**
	 *
	 * @var array
	 */
	private $_mimeParts = array();

	/**
	 *
	 * @var string
	 */
	private $_error = null;

	const EMAIL_NO_REPLY = 'no-reply@workforcechallenge.com';

	public function __construct()
	{
		$this->reset();
	}

	public function reset()
	{
		$this->_message = new Message();
		$this->_message->addFrom(static::EMAIL_NO_REPLY)
				->addReplyTo(static::EMAIL_NO_REPLY);
		$this->resetBody();
		return $this;
	}
	public function setBody($content, $html = true)
	{
		if ($html) {
			$this->setHtml($content);
		} else {
			$this->setText($content);
		}
		return $this;
	}

	public function resetBody()
	{
		$this->_mimeParts = array();
		return $this;
	}

	public function setHtml($content)
	{
		/* @var $mimePart \Zend\Mime\Part */
		$mimePart = new MimePart($content);
		$mimePart->type = 'text/html';
		$this->_mimeParts[] = $mimePart;
		return $this;
	}

	public function setText($content)
	{
		/* @var $mimePart \Zend\Mime\Part */
		$mimePart = new MimePart($content);
		$mimePart->type = 'text/plain';
		$this->_mimeParts[] = $mimePart;
		return $this;
	}

	public function addTo($email)
	{
		$this->_message->addTo($email);
		return $this;
	}

	public function setTo($email) {
		$this->_message->setTo($email);
		return $this;
	}

	public function setSubject($subject)
	{
		$this->_message->setSubject($subject);
		return $this;
	}

	public function getError()
	{
		return $this->_error;
	}

	public function send()
	{
		/* @var $body \Zend\Mime\Message */
		$body = new MimeMessage();
		$body->setParts($this->_mimeParts);

		$this->_message->setBody($body);
		try {
			$transport = new SendmailTransport();
			$transport->send($this->_message);
		} catch (\Exception $e) {
			$this->_error = $e->getMessage();
			return false;
		}
		return true;
	}

}

