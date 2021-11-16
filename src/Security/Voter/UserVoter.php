<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
//        dump($subject);
//        dd($attribute);
        $supportsAttribute = in_array($attribute, ['EDIT']);
        $supportsSubject = $subject instanceof User;

        return in_array($attribute, ['EDIT', 'VIEW'])
            && $subject instanceof \App\Entity\User;

//        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @param string $attribute
     * @param User $subject
     * @param TokenInterface $token
     * @return bool
     * @throws \Exception
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        dump($attribute);
        dump($subject);
        dd($token->getUser()->name);

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'EDIT':
                if ($subject->getOwner() === $user) {
                    return true;
                } else {
                    return 'You are not the owner of this resource user';
                }

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                } else {
                    return 'Only Admin have permission to edit this resource user';
                }

                return false;
        }

        throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
        return false;
    }
}
