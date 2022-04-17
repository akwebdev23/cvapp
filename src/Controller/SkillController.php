<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SkillService;
use App\Repository\SkillRepository;

class SkillController extends AbstractController{

    /**
     * @Route("/api/skills/all", name="get_all_skill")
     */
    public function getAll(SkillService $skillService)
    {
        try {
            $all = $skillService->getAllSerialized();
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
     * @Route("/api/skills/create", name="create_skill")
     */
    public function create(
        Request $request, 
        SkillService $skillService, 
        SkillRepository $skillRepo
    ): Response
    {
        $data = json_decode($request->getContent());
        $newSkill = $skillService->createNewSkill($data);
        if(count($newSkill[1])){
            return new Response((string) $newSkill[1], 400);
        }
        $skillRepo->add($newSkill[0], true);

        return $this->json([
            'message' => 'Create success!',
            'status' => 'ok',
        ]);
    }
}