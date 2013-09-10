<?php

namespace Application\Service;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\EventManager\ResponseCollection;

/**
 * service: event_hook
 */
class EventHook implements EventManagerAwareInterface
{

	/**
	 *
	 * @var ResponseCollection
	 */
	private $_results = null;

	/**
	 *
	 * @var \Zend\EventManager\EventManagerInterface
	 */
	private $_em = null;

	/**
	 *
	 * @param string $event
	 * @param EventsCapableInterface|null $context
	 * @param array $data
	 * @return EventHook
	 */
	public function __invoke($event, EventsCapableInterface $context = null, array $data = null)
	{
		return $this->trigger($event, $context, $data);
	}

	/**
	 *
	 * @param string $event
	 * @param \Zend\EventManager\EventsCapableInterface $context
	 * @param array $data
	 * @return EventHook
	 */
	public function trigger($event, EventsCapableInterface $context = null, array $data = null)
	{
		$this->_results = null;

		$em = empty($context) ? $this->_em : $context->getEventManager();
		$this->_results = $em->trigger(
				$event, $context, $data, function($v) {
					return ($v instanceof Response);
				}
		);
		return $this;
	}

	/**
	 *
	 * @return boolean
	 */
	public function wasStopped()
	{
		return $this->_results->stopped();
	}

	/**
	 *
	 * @return type
	 */
	public function getInterrupt()
	{
		return $this->_results->last();
	}

	/**
	 *
	 * @return EventManagerInterface
	 */
	public function getEventManager()
	{
		return $this->_em;
	}

	/**
	 *
	 * @param \Zend\EventManager\EventManagerInterface $eventManager
	 * @return EventHook
	 */
	public function setEventManager(EventManagerInterface $eventManager)
	{
		$this->_em = $eventManager;
		return $this;
	}

}
