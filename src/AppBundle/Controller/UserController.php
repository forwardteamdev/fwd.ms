<?php

namespace AppBundle\Controller;

use AppBundle\Document\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function getUserAction()
    {
        /** @var User $user */
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $data = [];
        $data['email'] = $this->getUser()->getEmail();

        return new JsonResponse($data, Response::HTTP_OK);
    }
}
