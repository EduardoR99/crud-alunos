<?php

namespace App\Traits;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Trait para padronizar todas as respostas da API.
 *
 * Garante formato consistente em todos os endpoints:
 * - Sucesso: { status: "success", message?, data?, meta? }
 * - Erro:    { status: "error", message, errors? }
 */
trait ApiResponseTrait
{
    protected function success(
        mixed $data = null,
        string $message = null,
        int $statusCode = ResponseInterface::HTTP_OK,
        array $meta = null,
    ): ResponseInterface {
        $body = ['status' => 'success'];

        if ($message !== null) {
            $body['message'] = $message;
        }

        if ($data !== null) {
            $body['data'] = $data;
        }

        if ($meta !== null) {
            $body['meta'] = $meta;
        }

        return $this->respond($body, $statusCode);
    }

    protected function created(mixed $data = null, string $message = 'Recurso criado com sucesso.'): ResponseInterface
    {
        return $this->success($data, $message, ResponseInterface::HTTP_CREATED);
    }

    protected function error(
        string $message,
        int $statusCode = ResponseInterface::HTTP_BAD_REQUEST,
        array $errors = null,
    ): ResponseInterface {
        $body = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return $this->respond($body, $statusCode);
    }

    protected function validationError(array $errors, string $message = 'Dados inválidos.'): ResponseInterface
    {
        return $this->error($message, ResponseInterface::HTTP_UNPROCESSABLE_ENTITY, $errors);
    }

    protected function notFound(string $message = 'Recurso não encontrado.'): ResponseInterface
    {
        return $this->error($message, ResponseInterface::HTTP_NOT_FOUND);
    }

    protected function unauthorized(string $message = 'Não autorizado.'): ResponseInterface
    {
        return $this->error($message, ResponseInterface::HTTP_UNAUTHORIZED);
    }

    protected function conflict(string $message): ResponseInterface
    {
        return $this->error($message, ResponseInterface::HTTP_CONFLICT);
    }
}
