<?php

namespace App\Service;

use Doctrine\Inflector\Rules\Word;
use App\Repository\ProjectRepository;
use App\Repository\SkillLevelRepository;
use App\Repository\SkillRepository;
use App\Repository\LinkRepository;
use App\Repository\LinkTypeRepository;
use App\Entity\Skill;
use App\Entity\SkillLevel;
use App\Entity\Project;
use App\Entity\Link;
use App\Entity\LinkType;

class UpdateService{
    protected $reppos;
    public function __construct(
        SkillRepository $skillRepo,
        SkillLevelRepository $skillLevelRepo,
        ProjectRepository $projectRepo,
        LinkRepository $linkRepo,
        LinkTypeRepository $linkTypeRepo
        ){
            $this->reppos['level-id'] = $skillLevelRepo;
            $this->reppos['skill-id'] = $skillRepo;
            $this->reppos['project-id'] = $projectRepo;
            $this->reppos['skill-collection'] = $skillRepo;
            $this->reppos['project-collection'] = $projectRepo;
            $this->reppos['linkType-id'] = $linkTypeRepo;

    }
    public function checkAndGetEntityForUpdate($repo, $filter, $class, $data)
    {
        $existEntity = $repo->findOneBy($filter);
        if(!$existEntity)
            $newObj = new $class();
        else
            $newObj = $existEntity;
        
        foreach ($data as $key => $value) {
            if($value === null){
                continue;
            }
            $property = explode('-', $key);
            $methodKey = '';
            $relationType = '';

            if(count($property) > 1){
                $relationType = $property[1];
            } else {
                $relationType = '';
            }
            $methodKey = $property[0];

            if($relationType == 'id'){
                $relatedEntity = $this->reppos[$key]->find($value);
                $method = 'set'.ucfirst($methodKey);
                $newObj->$method($relatedEntity);
                continue;
            }
            if($relationType == 'collection'){
                $method = 'add'.ucfirst($methodKey);
                foreach ($value as $itemId) {
                    $related = $this->reppos[$key]->find($itemId);
                    $newObj->$method($related);
                }
                continue;
            }
            $valueType = gettype($value);
            if($valueType == 'boolean' || $valueType == 'integer' || $valueType == 'string'){
                $method = 'set'.ucfirst($key);
                $newObj->$method($value);
            } 
        }
        return $newObj;
    }

}