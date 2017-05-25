<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 15/05/2017
 * Time: 16:19
 */

namespace AppBundle\Features\Context;

use AppBundle\Document\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;

class UserSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * UserSetupContext constructor.
     *
     * @param UserManagerInterface   $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Given there are Users with the following details:
     * @param TableNode $users
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $users)
    {
        foreach ($users->getColumnsHash() as $key => $val) {
            $confirmationToken = isset($val['confirmation_token']) && $val['confirmation_token'] !== ''
                ? $val['confirmation_token']
                : null;

            /** @var User $user */
            $user = $this->userManager->createUser();

            $user->setEnabled(true);
            $user->setUsername($val['username']);
            $user->setEmail($val['email']);
            $user->setPlainPassword($val['password']);
            $user->addRole($val['role']);
            $user->setConfirmationToken($confirmationToken);
            $user->setFirstName($val['firstName']);
            $user->setLastName($val['lastName']);
            $user->setGender($val['gender']);
            $user->setPhoto($val['photo']);

            if (! empty($confirmationToken)) {
                $user->setPasswordRequestedAt(new \DateTime('now'));
            }

            $this->userManager->updateUser($user);
        }
    }
}
