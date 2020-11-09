<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class LinkVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @var Security
     */
    private $security;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserVoter constructor.
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject)
    {

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [ self::VIEW, self::EDIT, self::DELETE, self::WRITE])) {
            return false;
        }

        // only vote on `Chat` objects
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if ($this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User || $user->getId() != $subject->getUserId()) {
            return false;
        } else {
            return true;
        }


    }

}