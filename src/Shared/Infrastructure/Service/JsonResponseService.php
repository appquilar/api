<?php

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    public function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['success' => true, 'data' => $data], $status);
    }

    public function created(): JsonResponse
    {
        return $this->success(status: Response::HTTP_CREATED);
    }

    private function error(array $message, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse(['success' => false, 'error' => $message], $status);
    }

    public function badRequest(string|array $message): JsonResponse
    {
        return $this->error(
            is_array($message) ? $message : [$message],
            Response::HTTP_BAD_REQUEST
        );
    }
}
