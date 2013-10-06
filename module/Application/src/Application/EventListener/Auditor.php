<?php

namespace Application\EventListener;

use Application\Entity\BaseInterface;
use Application\Service\BaseService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Application\Entity\Base\Audit;

/**
 * service: listener_auditor
 */
class Auditor extends BaseService implements EventSubscriber {

	public function latch() {
		$this->_entityManager()->getEventManager()->addEventSubscriber($this);
		return $this;
	}

	/**
	 * Detect a flush event.
	 * @param OnFlushEventArgs $eventArgs
	 */
	public function onFlush(OnFlushEventArgs $eventArgs) {
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$auditEntries = array(
			'insert' => $uow->getScheduledEntityInsertions(),
			'update' => $uow->getScheduledEntityUpdates(),
			'delete' => $uow->getScheduledEntityDeletions(),
			'updarr' => $uow->getScheduledCollectionUpdates(),
			'delarr' => $uow->getScheduledCollectionDeletions(),
		);
		$user = $this->_currentUser();
        $classMetaData = $eventArgs->getEntityManager()->getClassMetadata('Application\Entity\Base\Audit');
		foreach ($auditEntries as $type => $scheduledChangeObjects) {
			foreach ($scheduledChangeObjects as $scheduledChangeObject) {
				// audit type, entity user
				$id = null;
				if ($scheduledChangeObject instanceof BaseInterface) {
					$id = $scheduledChangeObject->id();
					$delta = $this->_computeEntityChangeset($scheduledChangeObject, $uow);
				} else {
					$delta = $this->_computeCollectionChangeset($scheduledChangeObject, $uow);
				}
                $audit = new Audit();
                if (!$user->isNull()) {
	                $audit->setUser($user)
	                    ->setType($type)
	                    ->setDelta($delta)
	                    ->setObjectClass(get_class($scheduledChangeObject))
	                    ->setObjectId($id);
	                $em->persist($audit);
	                $uow->computeChangeSet($classMetaData, $audit);
                }
			}
		}



	}

	protected function _computeEntityChangeset(BaseInterface $entity, $uow) {
		return $uow->getEntityChangeSet($entity);
	}

	protected function _computeCollectionChangeset(PersistentCollection $collection, $uow) {
		$diff = array(
			'added' => array(),
			'removed' => array(),
		);
		foreach ($collection->getDeleteDiff() as $entity) {
			$diff['removed'][] = $this->_getCollectionDiff($entity);
		}
		foreach ($collection->getInsertDiff() as $entity) {
			$diff['added'][] = $this->_getCollectionDiff($entity);
		}
		return $diff;
	}

	protected function _getCollectionDiff(BaseInterface $entity) {
		return array('id' => $entity->getEId(), 'object' => get_class($entity));
	}

	/**
	 * Returns an array of events this subscriber wants to listen to.
	 *
	 * @return array
	 */
	function getSubscribedEvents()
	{
		return array(Events::onFlush);
	}
}