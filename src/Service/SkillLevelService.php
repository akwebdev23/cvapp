<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\SkillLevel;
use App\Repository\SkillLevelRepository;
use App\Service\SerializeService;

class SkillLevelService{
    protected $validatorService;
    protected $serializeService;
    protected $skillLevelRepo;
    public function __construct(
            ValidatorService $validatorService,
            SkillLevelRepository $skillLevelRepo,
            SerializeService $serializeService
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->skillLevelRepo = $skillLevelRepo;
    }
    public function createNewSkillLevel($data)
    {
        $newSkillLevel = new SkillLevel();
        $newSkillLevel->setName($data['name'] ?? null);
        $newSkillLevel->setLevel($data['level'] ?? null);
        $newSkillLevel->setStyle($data['style'] ?? null);
        $errors = $this->validatorService->validate($newSkillLevel);
        return [$newSkillLevel, $errors];
    }
}