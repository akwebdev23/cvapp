<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LinkRepository;
use App\Repository\LinkTypeRepository;
use App\Service\SerializeService;
use App\Service\LinkService;
use App\Entity\Link;
use App\Entity\LinkType;
use App\Repository\ProjectRepository;


class LinkController extends AbstractController
{
    /**
     * @Route("/api/links/{id}", name="link_get_one")
     */
    public function getOneLink(Link $link, SerializeService $serializeService):response
    {
        try {
            $link = $serializeService->serialize($link);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $link,
            'status' => 'ok',
        ]);
    }
        /**
     * @Route("/api/linktypes/{id}", name="link_type_get_one")
     */
    public function getOneLinkType(LinkType $linkType, SerializeService $serializeService):response
    {
        try {
            $linkType = $serializeService->serialize($linkType);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $linkType,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/links", name="link_get_all")
     */
    public function getAllLinks(LinkRepository $linkRepo, SerializeService $serializeService):response
    {
        try {
            $all = $linkRepo->findAll();
            $all = $serializeService->serializeArray($all);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $all,
            'status' => 'ok',
        ]);
    }
        /**
     * @Route("/api/linktypes", name="link_types_get_all")
     */
    public function getAllLinkTypes(LinkTypeRepository $linkTypeRepo, SerializeService $serializeService):response
    {
        try {
            $all = $linkTypeRepo->findAll();
            $all = $serializeService->serializeArray($all);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $all,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/links/create/new", name="links_create")
     */
    public function createLink(
        Request $request, 
        LinkService $linkService, 
        LinkRepository $linkRepo,
        LinkTypeRepository $linkTypeRepo,
        ProjectRepository $projectRepo,
        SerializeService $serializeService
    ): Response
    {
        try {
            $dataArr = $request->request->getIterator();
            // $dataArr['project'] = $projectRepo->find($dataArr['project']);
            // $n =$dataArr['project'];

            // $dataArr['link_type'] = $linkTypeRepo->find($dataArr['link_type']);

            $newLink = $linkService->createNewLink($dataArr);
            if(count($newLink[1])){
                return new Response((string) $newLink[1], 400);
            }
            $newLink = $newLink[0];
            $linkRepo->add($newLink);
            $link = $serializeService->serialize($newLink);
        } catch (\Throwable $th) {
            return $this->json([
                'message' => $th->getMessage(),
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'success',
            'status' => 'ok',
            'data' => $link
        ]);
    }
    /**
     * @Route("/api/linktypes/create/new", name="link_types_create")
     */
    public function createLinkType(
        Request $request, 
        LinkService $linkService, 
        LinkTypeRepository $linkTypeRepo,
        SerializeService $serializeService
    ): Response
    {
        try {
            $dataArr = $request->request->getIterator();
            $newLinkType = $linkService->createNewLinkType($dataArr);
            if(count($newLinkType[1])){
                return new Response((string) $newLinkType[1], 400);
            }
            $newLinkType = $newLinkType[0];
            $linkTypeRepo->add($newLinkType);
            $linkType = $serializeService->serialize($newLinkType);
        } catch (\Throwable $th) {
            return $this->json([
                'message' => $th->getMessage(),
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'success',
            'status' => 'ok',
            'data' => $linkType
        ]);
    }
}
