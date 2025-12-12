<?php
use App\Entity\Log;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class LoggerListener
{
    public function postPersist(Log $log, LifecycleEventArgs $args): void
    {
        //...
    }

    public function preUpdate(Log $log, PreUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();
    }

    public function postUpdate(Log $log, LifecycleEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($log);
    }

    public function preRemove(Log $log, LifecycleEventArgs $args): void
    {
        //...
    }

    public function postRemove(Log $order, LifecycleEventArgs $args): void
    {
        //...
    }
}
