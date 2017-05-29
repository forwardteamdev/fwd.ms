<?php
/**
 * @author Serghei Luchianenco (s@luchianenco.com)
 * Date: 29/05/2017
 * Time: 11:53
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Document\UserInvitation;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class InvitationToCodeTransformer implements DataTransformerInterface
{
    private $objectManager;

    public function __construct(DocumentManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof UserInvitation) {
            throw new UnexpectedTypeException($value, UserInvitation::class);
        }

        return $value->getCode();
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }
        return $this->objectManager->find(UserInvitation::class, $value);
    }
}
