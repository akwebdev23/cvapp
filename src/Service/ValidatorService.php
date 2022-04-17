<?php

namespace App\Service;
use Symfony\Component\Validator\Validator\ValidatorInterface as ValidatorInterface;
use App\Entity\Skill;

class ValidatorService{
    protected $validator;
    public function __construct(ValidatorInterface $validator){
        $this->validator = $validator;
    }
    public function validate(Skill $skill)
    {
        $errors = $this->validator->validate($skill);
        return $errors;
    }

}