<?php

namespace AppBundle\Controller;

use AppBundle\Document\User;
use AppBundle\Document\UserInvitation;
use AppBundle\Form\Type\UserInvitationType;
use AppBundle\Form\Type\UserRegistrationType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserRegistrationController extends FOSRestController
{
    /**
     * @ApiDoc(
     *  description="User Registration",
     *  requirements={
     *      {
     *          "name"="username",
     *          "dataType"="string",
     *          "description"="User Name"
     *     },
     *     {
     *          "name"="email",
     *          "dataType"="string",
     *          "description"="User Email"
     *     }
     * }
     *)
     * @param Request $request
     * @return JsonResponse
     * @throws \LogicException
     */
    public function postUserRegistrationAction(Request $request)
    {
        $result = [];
        $responseCode = Response::HTTP_OK;

        $postData = json_decode($request->getContent(), true);

        $newUser = new User();

        $form = $this->createForm(UserRegistrationType::class, $newUser);
        $form->submit($postData);

        if ($form->isValid()) {
            $result['status'] = 'success';
            $this
                ->get('doctrine.odm.mongodb.document_manager')
                ->persist($form->getData())
            ;
            $this
                ->get('doctrine.odm.mongodb.document_manager')
                ->flush()
            ;
        } else {
            $result['status'] = 'error';
            $result['message'] = $form->getErrors();
            $result['count'] = $form->count();
        }

        return new JsonResponse($result, $responseCode);
    }
}
