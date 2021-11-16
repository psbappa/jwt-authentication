<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Event\PreUpdateEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Events;

class HashPasswordListener implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    //    dump("listener");
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        dump($args->getObject());
        $entity = $args->getObject();

        if(!$entity instanceof User){
            return;
        }

        if (!$entity->getPlainPassword()) {
            return;
        }

        $entity->setPassword(
            $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword())
        );

        dump($entity);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }
}