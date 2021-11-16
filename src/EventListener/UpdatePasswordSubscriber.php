<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Events;
use App\Entity\User;

class UpdatePasswordSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();
        dump($entity);
        $entityManager = $eventArgs->getObjectManager();

        if ($entity instanceof User) {
            if ($eventArgs->hasChangedField('password') && $eventArgs->getNewValue('password')) {
                //Hashed only in User login
                $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());

                $eventArgs->setNewValue('password', $hashedPassword);

                dump($eventArgs);
            }

            if ($eventArgs->hasChangedField('password') || $eventArgs->getNewValue('password')) {
                dump('plainPassword');
                
                // $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword());

                // $eventArgs->setNewValue('password', $hashedPassword);
            }
        }

        dump($entity);
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate
        ];
    }
}