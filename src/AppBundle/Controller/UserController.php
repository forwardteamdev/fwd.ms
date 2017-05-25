<?php

namespace AppBundle\Controller;

use AppBundle\Document\User;
use AppBundle\Document\UserInvitation;
use AppBundle\Form\Type\UserInvitationType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @throws \LogicException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function getUserAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $data = [];
        $data['email'] = $this->getUser()->getEmail();

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function postUserInvitationAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $postData = json_decode($request->getContent(), true);

        $userInvitation = new UserInvitation();
        $form = $this->createForm(UserInvitationType::class, $userInvitation);
        $form->submit($postData);

        if ($form->isValid()) {
            $userInvitation = $form->getData();
            $this->get('doctrine.odm.mongodb.document_manager')->persist($userInvitation);
            $this->get('doctrine.odm.mongodb.document_manager')->flush();
            $result = [
                'status' => 'success'
            ];
        } else {
            $result = [
                'status' => 'error'
            ];
        }


        return new JsonResponse($result, 200);
    }

    public function validateUserInvitationAction(Request $request)
    {
        $result = [];
        $responseCode = Response::HTTP_NOT_FOUND;

        $code = $request->get('code');
        if (mb_strlen($code) !== UserInvitation::CODE_LENGTH) {
            $responseCode = Response::HTTP_BAD_REQUEST;
        }

        $invitation = $this
            ->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:UserInvitation')
            ->findOneBy([
                'code' => $code
            ]);

        if ($invitation instanceof UserInvitation) {
            $responseCode = Response::HTTP_OK;
            $result['email'] = $invitation->getEmail();
            $result['team'] = $invitation->getTeam();
        }

        return new JsonResponse($result, $responseCode);
    }
}
