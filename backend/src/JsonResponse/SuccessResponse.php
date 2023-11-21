<?php

namespace App\JsonResponse;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class SuccessResponse extends JsonResponse
{

    public function __construct(mixed $data = null, public $headers = [])
    {

        $this->response = [
            'status' => 'success',
            'data'=> $data,
            'length'=>count($data)
        ];
        parent::__construct($data,Response::HTTP_OK,$this->headers,false);
    }

}
//    private ContainerInterface $container;
//    public function __construct(ContainerInterface $container,mixed $data = null, array $headers = [],$context = [], bool $json = false)
//    {
//        $this->container = $container;
//        if(count($context)>0){
//            $data = $this->serializeData($data,$context);
//        }
//        $response = [
//            'status' => 'success',
//            'data'=> $data
//        ];
//        parent::__construct($response, 200, $headers, $json);
//    }
//
//    public function serializeData(mixed $data, $context = []): string
//    {
//        return $this->serialize($data,'json',$context);
//    }
//
//    public function serialize(mixed $data, string $format, array $context = []): string
//    {
//        if ($format !== 'json') {
//            throw new \InvalidArgumentException('Unsupported format');
//        }
//
//
//        return $this->normalize($data, $context);
//    }
//
//    private function normalize(mixed $data, array $context = []): string
//    {
//        $serializer = $this->getSerializer();
//
//        return $serializer->serialize($data, 'json', $context);
//    }
//
//    private function getSerializer(): object
//    {
//        // Get the Symfony serializer service from the container
//        return $this->container->get('serializer');
//    }
//
//    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
//    {
//        if ($format !== 'json') {
//            throw new \InvalidArgumentException('Unsupported format');
//        }
//
//        return $this->denormalize($data, $type, $format, $context);
//    }
//
//    private function denormalize(mixed $data, string $type, string $format, array $context = []): mixed
//    {
//        $serializer = $this->getSerializer();
//
//        return $serializer->deserialize($data, $type, $format, $context);
//    }
//}


//namespace App\Response;
//
//use Symfony\Component\HttpFoundation\JsonResponse;
//
//class SuccessResponse extends JsonResponse
//{
//    public function __construct($data = null, $status = 200, $headers = [], $options = 0)
//    {
//        $formattedData = [
//            'status' => $status,
//            'data' => $data,
//            'message' => 'Your custom message here',
//        ];
//
//        parent::__construct($formattedData, $status, $headers, $options);
//    }
//}
