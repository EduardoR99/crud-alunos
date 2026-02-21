<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class JWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = $request->getCookie('jwt_token');

        if (empty($token)) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Token de autenticação não fornecido.',
                ]);
        }

        $authService = Services::authService();
        $decoded     = $authService->validateToken($token);

        if ($decoded === null) {
            return service('response')
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'Token inválido ou expirado.',
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
