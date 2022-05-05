<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProjectService;
use App\Repository\ProjectRepository;
use App\Service\SerializeService;
use App\Service\UploadService;
use App\Entity\Project;

class ProjectController extends AbstractController{
    /**
     * @Route("/api/projects/remove/{id}", name="project_remove_one")
     */
    public function removeProject(Project $project, ProjectRepository $projectRepo):response
    {
        try {
            $projectName = $project->getName();
            $projectRepo->remove($project);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $projectName,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/projects/one/{id}", name="project_get_one")
     */
    public function getOne(Project $project, SerializeService $serializeService):response
    {
        try {
            $project = $serializeService->serialize($project);
        } catch (\Throwable $th) {
            $thMessage = $th->getMessage();
            return $this->json([
                'message' => $thMessage,
                'status' => 'error',
            ]);
        }
        return $this->json([
            'message' => 'Success!',
            'data'=> $project,
            'status' => 'ok',
        ]);
    }
    /**
     * @Route("/api/projects", name="project_get_all")
     */
    public function getAll(ProjectRepository $projectRepo, SerializeService $serializeService)
    {
        try {
            $all = $projectRepo->findAll();
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
     * @Route("/api/projects/create", name="project_create")
     */
    public function create(
        Request $request, 
        ProjectService $projectService, 
        ProjectRepository $projectRepo,
        UploadService $uploadService,
        SerializeService $serializeService
    ): Response
    {
        try {
            $newProject = $projectService->createNewProject($request->request->getIterator());
            if(count($newProject[1])){
                return new Response((string) $newProject[1], 400);
            }
            $uploadPath = $this->getParameter('projects.upload_dir');
            $newProject = $newProject[0];

            $icon = $request->files->get('icon');
            $image = $request->files->get('image');
            $name = $request->get('name');

            $iconName = $uploadService->upload($icon, $name.'_icon', $uploadPath);
            $iconName ? $newProject->setIcon($this->getParameter('projects_public.upload_dir').$iconName) : false;

            $imageName = $uploadService->upload($image, $name.'_image', $uploadPath);
            $imageName ? $newProject->setImage($this->getParameter('projects_public.upload_dir').$imageName) : false;

            $projectRepo->add($newProject, true);
            $newProjectSer = $serializeService->serialize($newProject);
        } catch (\Throwable $error) {
            return $this->json([
                'message' => $error->getMessage(),
                'status' => 'error',
                'error' => $error,
            ]);
        }
        return $this->json([
            'message' => 'success',
            'status' => 'ok',
            'data' => $newProjectSer
        ]);
    }
}