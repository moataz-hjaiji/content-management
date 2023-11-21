<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Serializer implements \Symfony\Component\Serializer\SerializerInterface
{


    public function __construct(private ContainerInterface $container)
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

    private function normalize(mixed $data, array $context = []): string
    {
        $serializer = $this->getSerializer();

        return $serializer->serialize($data, 'json', $context);
    }

    private function getSerializer(): object
    {
        // Get the Symfony serializer service from the container
        return $this->container->get('serializer');
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
