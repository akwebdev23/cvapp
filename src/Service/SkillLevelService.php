<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\SkillLevel;
use App\Repository\SkillLevelRepository;
use App\Service\SerializeService;
use App\Service\UpdateService;

class SkillLevelService{
    protected $validatorService;
    protected $serializeService;
    protected $skillLevelRepo;
    public function __construct(
            ValidatorService $validatorService,
            SkillLevelRepository $skillLevelRepo,
            SerializeService $serializeService,
            UpdateService $updateService
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->skillLevelRepo = $skillLevelRepo;
        $this->updateService = $updateService;
    }
    public function createNewSkillLevel($data)
    {
        $newSkillLevel = $this->updateService->checkAndGetEntityForUpdate(
            $this->skillLevelRepo,
            ['name'=>$data['name']],
            SkillLevel::class,
            $data
        );
        // $newSkillLevel = new SkillLevel();
        // $newSkillLevel->setName($data['name'] ?? null);
        // $newSkillLevel->setLevel($data['level'] ?? null);
        // $newSkillLevel->setStyle($data['style'] ?? null);
        $errors = $this->validatorService->validate($newSkillLevel);
        return [$newSkillLevel, $errors];
    }
}