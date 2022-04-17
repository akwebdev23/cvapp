<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Service\SerializeService;

class SkillService{
    protected $validatorService;
    protected $serializeService;
    protected $skillRepo;
    public function __construct(
            ValidatorService $validatorService,
            SkillRepository $skillRepo,
            SerializeService $serializeService
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->skillRepo = $skillRepo;
    }
    public function createNewSkill($data)
    {
        $newSkill = new Skill();
        $newSkill->setName($data->name);
        $newSkill->setLabel($data->label);
        $newSkill->setDesription($data->desription);
        $newSkill->setIcon($data->icon);
        $newSkill->setImage($data->image);
        $errors = $this->validatorService->validate($newSkill);
        return [$newSkill, $errors];
    }
    public function getAllSerialized()
    {
        $returnArr = [];
        $skills = $this->skillRepo->getAll();
        return $this->serializeService->serializeArray($skills);
    }

}