<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SkillLevelService;
use App\Repository\SkillLevelRepository;
use App\Service\SerializeService;
use Symfony\Component\Filesystem\Filesystem;

class SkillLevelController extends AbstractController{

    /**
     * @Route("/api/skills/levels/all", name="skill_level_get_all")
     */
    public function getAll(SkillLevelRepository $skillLevelRepo, SerializeService $serializeService):response
    {
        try {
            $all = $skillLevelRepo->findAll();
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
     * @Route("/api/skills/levels/create", name="skill_level_create")
     */
    public function create(
        Request $request, 
        SkillLevelService $skillLevelService, 
        SkillLevelRepository $skillLevelRepo,
        SerializeService $serializeService
    ): Response
    {
        try {
            $dataArr = $request->request->getIterator();
            $newSkillLevel = $skillLevelService->createNewSkillLevel($dataArr);
            if(count($newSkillLevel[1])){
                return new Response((string) $newSkillLevel[1], 400);
            }
            $newSkillLevel = $newSkillLevel[0];
            $skillLevelRepo->add($newSkillLevel);
            $skillLevel = $serializeService->serialize($newSkillLevel);
        } catch (\Throwable $th) {
            return $this->json([
                'message' => $th->getMessage(),
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'success',
            'status' => 'ok',
            'data' => $skillLevel
        ]);
    }
}