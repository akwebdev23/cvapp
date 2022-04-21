<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;
use App\Service\SerializeService;

class ProjectService{
    protected $validatorService;
    protected $serializeService;
    protected $projectRepo;
    protected $skillRepo;

    public function __construct(
            ValidatorService $validatorService,
            ProjectRepository $projectRepo,
            SerializeService $serializeService,
            SkillRepository $skillRepo
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->projectRepo = $projectRepo;
        $this->skillRepo = $skillRepo;
    }
    public function createNewProject($data)
    {
        $newProject = new Project();
        $newProject->setName($data['name']);
        $newProject->setDescription($data['description'] ?? null);
        $newProject->setStart($data['start'] ?? null);
        $newProject->setEnd($data['end'] ?? null);
        $newProject->setProducton($data['production'] ?? null);
        $newProject->setGithub($data['github'] ?? null);
        foreach ($data['item_id'] as $key => $value) {
            $skill = $this->skillRepo->find($value);
            $newProject->addSkill($skill);
        }
        $errors = $this->validatorService->validate($newProject);
        return [$newProject, $errors];
    }
}