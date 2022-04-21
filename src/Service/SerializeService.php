<?php

namespace App\Service;

use stdClass;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


class SerializeService{
    protected $serializer;
    public function __construct(){
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getName();
            },
        ];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);
        
        $this->serializer = new Serializer([$normalizer], [$encoder]);
    }
    public function serializeArray(array $items): array
    {
        $returnArr = [];
        foreach ($items as $item) {
            $returnArr[] = json_decode($this->serializer->serialize($item, 'json'));
        }
        return $returnArr;
    }
    public function serialize($item): stdClass
    {
        return json_decode($this->serializer->serialize($item, 'json'));
    }

}