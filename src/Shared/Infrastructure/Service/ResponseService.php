<?php

namespace App\Shared\Infrastructure\Service;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseService
{
    private function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $status);
    }

    public function ok(array $message = []): JsonResponse
    {
        return $this->success($message, Response::HTTP_OK);
    }

    public function okList(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        $data['success'] = true;

        return new JsonResponse($data, $status);
    }

    public function created(): JsonResponse
    {
        return $this->success(status: Response::HTTP_CREATED);
    }

    public function noContent(): JsonResponse
    {
        return $this->success(status: Response::HTTP_NO_CONTENT);
    }

    public function respondImage(string $path): BinaryFileResponse
    {
        return new BinaryFileResponse($path);
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

    public function unauthorized(string $message = null): JsonResponse
    {
        return $this->error([$message], Response::HTTP_UNAUTHORIZED);
    }

    public function notFound(string $message = null): JsonResponse
    {
        return $this->error([$message], Response::HTTP_NOT_FOUND);
    }

    public function genericError(string $message = null): JsonResponse
    {
        return $this->error([$message], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
