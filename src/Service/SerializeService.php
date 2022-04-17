<?php

namespace App\Service;
use Symfony\Component\Serializer\SerializerInterface as SerializerInterface;

class SerializeService{
    protected $serializer;
    public function __construct(SerializerInterface $serializer){
        $this->serializer = $serializer;
    }
    public function serializeArray(array $items): array
    {
        $returnArr = [];
        foreach ($items as $item) {
            $returnArr[] = json_decode($this->serializer->serialize($item, 'json'));
        }
        return $returnArr;
    }

}