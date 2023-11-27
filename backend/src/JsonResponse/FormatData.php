<?php

namespace App\JsonResponse;

use App\Service\Serializer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class FormatData
{
    private mixed $data;
    public function __construct(mixed $data)
    {
        $this->data = $data;
    }
    public function getFormatData( $status = 'success'): array
    {
        return [
            'status'=>$status,
            'data'=>$this->data,
            'amount'=>count($this->data)
        ];
    }
}
