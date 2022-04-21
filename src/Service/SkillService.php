<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\Skill;
use App\Entity\SkillLevel;
use App\Repository\ProjectRepository;
use App\Repository\SkillLevelRepository;
use App\Repository\SkillRepository;
use App\Service\SerializeService;

class SkillService{
    protected $validatorService;
    protected $serializeService;
    protected $skillRepo;
    protected $projectRepo;

    public function __construct(
            ValidatorService $validatorService,
            SkillRepository $skillRepo,
            SerializeService $serializeService,
            SkillLevelRepository $skillLevelRepo,
            ProjectRepository $projectRepo
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->skillRepo = $skillRepo;
        $this->skillLevelRepo = $skillLevelRepo;
        $this->projectRepo = $projectRepo;

    }
    public function createNewSkill($data)
    {
        $newSkill = new Skill();
        $newSkill->setName($data['name']);
        $newSkill->setLabel($data['label']);
        isset($data['level']) 
            ? $newSkill->setLevel($this->skillLevelRepo->find($data['level']))
            : false;
        $newSkill->setDescription($data['description'] ?? null);
        $newSkill->setIcon($data['icon'] ?? null);
        $newSkill->setImage($data['image'] ?? null);
        foreach ($data['item_id'] ?? [] as $key => $value) {
            $project = $this->projectRepo->find($value);
            $newSkill->addProject($project);
        }
        $errors = $this->validatorService->validate($newSkill);
        return [$newSkill, $errors];
    }

}