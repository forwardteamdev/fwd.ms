<?php

namespace AppBundle\Controller;

use AppBundle\Document\Team;
use AppBundle\Form\Type\TeamType;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamController extends FOSRestController
{
    public function postTeamAction(Request $request)
    {
        $this->isUserGranted();

        $responseCode = Response::HTTP_BAD_REQUEST;
        $postData = json_decode($request->getContent(), true);

        $newTeam = new Team();
        $form = $this->createForm(TeamType::class, $newTeam);
        $form->submit($postData);

        if ($form->isValid()) {
            $this
                ->get('doctrine_mongodb.odm.default_document_manager')
                ->persist($form->getData())
            ;

            $this
                ->get('doctrine_mongodb.odm.default_document_manager')
                ->flush()
            ;

            $responseCode = Response::HTTP_CREATED;
        }

        return new JsonResponse([], $responseCode);
    }

    public function getTeamAction($id)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $responseCode = Response::HTTP_OK;
        $data = $this
            ->get('doctrine_mongodb.odm.default_document_manager')
            ->getRepository(Team::class)
            ->find($id);

        if (!$data instanceof Team) {
            $responseCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse($data, $responseCode);
    }

    public function putTeamAction(Request $request, $id)
    {
        $this->isUserGranted();

        $data = $this
            ->get('doctrine_mongodb.odm.default_document_manager')
            ->getRepository(Team::class)
            ->find($id);

        if (!$data instanceof Team) {
            $this->createNotFoundException();
        }

        $responseCode = Response::HTTP_BAD_REQUEST;
        $postData = json_decode($request->getContent(), true);

        $form = $this->createForm(TeamType::class, $data);
        $form->submit($postData);

        if ($form->isValid()) {
            $this
                ->get('doctrine_mongodb.odm.default_document_manager')
                ->persist($form->getData())
            ;

            $this
                ->get('doctrine_mongodb.odm.default_document_manager')
                ->flush()
            ;

            $responseCode = Response::HTTP_ACCEPTED;
        }

        return new JsonResponse([], $responseCode);
    }

    public function deleteTeamAction($id)
    {
        $this->isUserGranted();

        $data = $this
            ->get('doctrine_mongodb.odm.default_document_manager')
            ->getRepository(Team::class)
            ->find($id);

        if (!$data instanceof Team) {
            $this->createNotFoundException();
        }

        $this
            ->get('doctrine_mongodb.odm.default_document_manager')
            ->remove($data)
        ;

        $this
            ->get('doctrine_mongodb.odm.default_document_manager')
            ->flush()
        ;

        return new JsonResponse([], Response::HTTP_ACCEPTED);
    }

    private function isUserGranted()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
    }
}
