<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class Serializer implements SerializerInterface
{


    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function serializeData(mixed $data, $context = []): string
    {
        return $this->serialize($data,'json',$context);
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        if ($format !== 'json') {
            throw new \InvalidArgumentException('Unsupported format');
        }


        return $this->normalize($data, $context);
    }

    protected function normalize(mixed $data, array $context = []): string
    {
        $serializer = $this->getSerializer();

        return $serializer->serialize($data, 'json', $context);
    }

    private function getSerializer(): object
    {
        // Get the Symfony serializer service from the container
        return $this->serializer;
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        if ($format !== 'json') {
            throw new \InvalidArgumentException('Unsupported format');
        }

        return $this->denormalize($data, $type, $format, $context);
    }

    private function denormalize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        $serializer = $this->getSerializer();

        return $serializer->deserialize($data, $type, $format, $context);
    }
}
