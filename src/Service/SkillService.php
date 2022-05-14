<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\Skill;
use App\Entity\SkillLevel;
use App\Repository\ProjectRepository;
use App\Repository\SkillLevelRepository;
use App\Repository\SkillRepository;
use App\Service\SerializeService;
use App\Service\UpdateService;

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
            ProjectRepository $projectRepo,
            UpdateService $updateService
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->skillRepo = $skillRepo;
        $this->skillLevelRepo = $skillLevelRepo;
        $this->projectRepo = $projectRepo;
        $this->updateService = $updateService;
    }
    public function createNewSkill($data)
    {
        $newSkill = $this->updateService->checkAndGetEntityForUpdate(
            $this->skillRepo,
            ['name'=>$data['name']],
            Skill::class,
            $data
        );
        $errors = $this->validatorService->validate($newSkill);
        return [$newSkill, $errors];
    }
    public function getNormalizeArray(SkillRepository $skillRepo)
    {
        $skills = [];
        $all = $skillRepo->findAll();
        foreach ($all as $keySkill => $skill) {
            $skillR['id'] = $skill->getId();
            $skillR['name'] = $skill->getName();
            $skillR['label'] = $skill->getLabel();
            $skillR['description'] = $skill->getDescription();
            $skillR['icon'] = $skill->getIcon();
            $skillR['projects'] = [];
            $prs = $skill->getProjects();
            $lvl = $skill->getLevel();

            foreach ($prs as $keyPr => $pr) {
                $skillR['projects'][] = [
                    'id'=>$pr->getId(),
                    'icon'=>$pr->getIcon(),
                    'name'=>$pr->getName(),
                ];

            }
            $skillR['level'] = ['level' => $lvl->getLevel(), 'style' => $lvl->getStyle()];
            $skills[] = $skillR;
        }
        return $skills;
    }
}