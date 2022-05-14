<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SkillService;
use App\Entity\Skill;

use App\Repository\SkillRepository;
use App\Service\SerializeService;
use App\Service\UploadService;

class SkillController extends AbstractController{
    /**
     * @Route("/api/skills/remove/{id}", name="skill_remove_one")
     */
    public function removeSkill(Skill $skill, SkillRepository $skillRepo):response
    {
        try {
            $skillName = $skill->getName();
            $skillRepo->remove($skill);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $skillName,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/skills/one/{id}", name="skill_get_one")
     */
    public function getOne(Skill $skill, SerializeService $serializeService):response
    {
        try {
            $skill = $serializeService->serialize($skill);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $skill,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/skills", name="skill_get_all")
     */
    public function getAll(SkillRepository $skillRepo, SkillService $skillService):response
    {
        try {
            $skills = $skillService->getNormalizeArray($skillRepo);
            
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $skills,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/skills/create", name="skill_create")
     */
    public function create(
        Request $request, 
        SkillService $skillService, 
        SkillRepository $skillRepo,
        UploadService $uploadService, 
        SerializeService $serializeService
    ): Response
    {
        try {
            $dataArr = $request->request->getIterator();
            $newSkill = $skillService->createNewSkill($dataArr);
            if(count($newSkill[1])){
                return new Response((string) $newSkill[1], 400);
            }
            $uploadPath = $this->getParameter('upload_dir').'/skills/';
            $newSkill = $newSkill[0];

            $icon = $request->files->get('icon');
            $image = $request->files->get('image');
            $name = $request->get('name');

            $iconName = $uploadService->upload($icon, $name.'_icon', $uploadPath);
            $iconName ? $newSkill->setIcon($this->getParameter('skills_public.upload_dir').$iconName) : false;

            $imageName = $uploadService->upload($image, $request->get('name').'_image', $uploadPath);
            $imageName ? $newSkill->setImage($this->getParameter('skills_public.upload_dir').$iconName) : false;

            $skillRepo->add($newSkill, true);
            $skill = $serializeService->serialize($newSkill);
            return $this->json([
                'message' => 'success',
                'status' => 'ok',
                'data' => $skill 
            ]);
        } catch (\Throwable $th) {
            return $this->json([
                'message' => $th->getMessage(),
                'status' => 'error',
                'error' => $th,
            ]);
        }
    }
}