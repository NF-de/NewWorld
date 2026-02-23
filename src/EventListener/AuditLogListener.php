<?php

namespace App\EventListener;

use App\Entity\Log;
use App\Entity\User;
use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::onFlush)]
class AuditLogListener
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $user = $this->security->getUser();

        // 1. Entités créées (INSERT)
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->createLog($em, $uow, $entity, 'CREATE', $user);
        }

        // 2. Entités modifiées (UPDATE)
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->createLog($em, $uow, $entity, 'UPDATE', $user);
        }

        // 3. Entités supprimées (DELETE)
        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->createLog($em, $uow, $entity, 'DELETE', $user);
        }
    }

    private function createLog($em, $uow, $entity, string $operation, User $user): void
    {
        if ($entity instanceof Log) {
            return;
        }

        $log = new Log();
        $log->setOperation($operation);

        // Nom de l'entité (ex: "Entreprise", "User", ...)
        $className = (new \ReflectionClass($entity))->getShortName();
        $log->setTableConcernee($className);

        // Récupération de l'ID pour avoir un message plus précis
        $entityId = method_exists($entity, 'getId') && $entity->getId() !== null
            ? $entity->getId()
            : 'N/A';

        $log->setMessage(sprintf("L'action %s a été effectuée sur %s (ID: %s)", $operation, $className, $entityId));
        $log->setCreatedAt(new \DateTime());
        $log->setUser($user);

        $entreprise = $user->getEntreprise();
        if ($entreprise) {
            $log->setEntreprise($entreprise);
        }

        // Dire à Doctrine d'ajouter ce Log à la transaction SQL en cours
        $em->persist($log);
        $classMetadata = $em->getClassMetadata(Log::class);
        $uow->computeChangeSet($classMetadata, $log);
    }
}