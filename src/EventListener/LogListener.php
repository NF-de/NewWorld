<?php

namespace App\EventListener;

use App\Entity\Log;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class LogListener
{
    public function postPersist(Log $log, LifecycleEventArgs $args): void
    {
        // ...
    }

    public function preUpdate(Log $log, PreUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();
    }

    public function postUpdate(Log $log, LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($log);
    }

    public function preRemove(Log $log, LifecycleEventArgs $args): void
    {
        // ...
    }

    public function postRemove(Log $log, LifecycleEventArgs $args): void
    {
        // ...
    }
}
