<?php

namespace App\Service;
use App\Service\ValidatorService;
use App\Entity\Link;
use App\Entity\LinkType;

use App\Repository\LinkRepository;
use App\Service\SerializeService;

class LinkService{
    protected $validatorService;
    protected $serializeService;
    protected $linkRepo;
    public function __construct(
            ValidatorService $validatorService,
            LinkRepository $linkRepo,
            SerializeService $serializeService
            ){
        $this->validatorService = $validatorService;
        $this->serializeService = $serializeService;
        $this->linkRepo = $linkRepo;
    }
    public function createNewLink($data)
    {
        $newLink = new Link();
        $newLink->setName($data['name'] ?? null);
        $newLink->setLink($data['link'] ?? null);
        $newLink->setProject($data['project'] ?? null);
        $newLink->setLinkType($data['link_type'] ?? null);
        $errors = $this->validatorService->validate($newLink);
        return [$newLink, $errors];
    }
    public function createNewLinkType($data)
    {
        $newLink = new LinkType();
        $newLink->setName($data['name'] ?? null);
        $newLink->setStyle($data['style'] ?? null);
        $errors = $this->validatorService->validate($newLink);
        return [$newLink, $errors];
    }
}