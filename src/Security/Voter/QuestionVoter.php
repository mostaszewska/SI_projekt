<?php
/**
 * QuestionVoter
 */

namespace App\Security\Voter;

use App\Entity\Question;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * QuestionVoter
 */
class QuestionVoter extends Voter
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

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
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        // only vote on `Question` objects
        if (!$subject instanceof Question) {
            return false;
        }

        return true;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
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

        // you know $subject is a Question object, thanks to `supports()`
        /** @var Question $question */
        $question = $subject;

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->hasAccess($question, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    /**
     * @param Question $question
     * @param User $user
     *
     * @return bool
     */
    protected function hasAccess(Question $question, User $user): bool
    {
        return $user === $question->getAuthor();
    }
}
