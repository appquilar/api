<?php

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonResponseService
{
    private function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['success' => true, 'data' => $data], $status);
    }

    public function ok(array $message): JsonResponse
    {
        return $this->success($message, Response::HTTP_OK);
    }

    public function created(): JsonResponse
    {
        return $this->success(status: Response::HTTP_CREATED);
    }

    private function error(?array $message = [], int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        $data = null;
        if (!empty($message)) {
            $data = ['success' => false, 'error' => $message];
        }
        return new JsonResponse($data, $status);
    }

    public function badRequest(string|array $message): JsonResponse
    {
        return $this->error(
            is_array($message) ? $message : [$message],
            Response::HTTP_BAD_REQUEST
        );
    }

    public function unauthorized(): JsonResponse
    {
        return $this->error(status: Response::HTTP_UNAUTHORIZED);
    }
}
