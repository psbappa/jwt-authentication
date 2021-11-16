<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEntityListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        var_dump('UserEntityListener');
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpdateHandler(LifecycleEventArgs $args): void
    {
        var_dump('UserEntityListener');

        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if(!$entity instanceof User){
            return;
        }

        if (!$entity->getPlainPassword()) {
            return;
        }

        $entity->setPassword(
            $this->passwordHasher->hashPassword($entity, $entity->getPlainPassword())
        );
    }
}