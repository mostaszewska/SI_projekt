<?php
/**
 * AnswerVoter
 */

namespace App\Security\Voter;

use App\Entity\Answer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * AnswerVoter
 */
class AnswerVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';
    const FAVOURITE = 'FAVOURITE';

    /**
     * Security helper.
     *
     * @var \Symfony\Component\Security\Core\Security
     */
    private Security $security;

    /**
     * OrderVoter constructor.
     *
     * @param \Symfony\Component\Security\Core\Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::FAVOURITE])) {
            return false;
        }

        // only vote on `Answer` objects
        if (!$subject instanceof Answer) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ROLE_ADMIN can do anything!
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // you know $subject is a Answer object, thanks to `supports()`
        /** @var Answer $answer */
        $answer = $subject;

        switch ($attribute) {
            case self::VIEW:
            case self::FAVOURITE:
            case self::EDIT:
            case self::DELETE:
                return $this->hasAccess($answer, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Answer $answer
     * @param User $user
     * @return bool
     */
    protected function hasAccess(Answer $answer, User $user): bool
    {
        return $user === $answer->getAuthor();
    }
}
