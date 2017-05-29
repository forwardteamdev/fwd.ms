<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 15/05/2017
 * Time: 16:19
 */

namespace AppBundle\Features\Context;

use AppBundle\Document\User;
use AppBundle\Document\UserInvitation;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;

class UserInvitationSetupContext implements Context, SnippetAcceptingContext
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * UserSetupContext constructor.
     *
     * @param ObjectManager  $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there are User Invitations with the following details:
     * @param TableNode $invitations
     */
    public function thereAreUsersWithTheFollowingDetails(TableNode $invitations)
    {
        foreach ($invitations->getColumnsHash() as $key => $val) {
            $userInv = new UserInvitation();
            $userInv
                ->setEmail($val['email'])
                ->setTeam($val['team'])
                ->setSent(true)
            ;

            $this->objectManager->persist($userInv);
        }

        $this->objectManager->flush();
    }
}
