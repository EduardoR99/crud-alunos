<?php

namespace App\Controllers\Api;

use App\DTOs\Request\CreateUserRequest;
use App\DTOs\Request\LoginRequest;
use App\DTOs\Response\UserResponse;
use App\Traits\ApiResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use RuntimeException;

class AuthController extends ResourceController
{
    use ApiResponseTrait;

    public function register(): ResponseInterface
    {
        $rules = [
            'nome_completo' => 'required|min_length[3]|max_length[255]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/]',
        ];

        $messages = [
            'password' => [
                'min_length'  => 'A senha deve ter no mínimo 8 caracteres.',
                'regex_match' => 'A senha deve conter maiúscula, minúscula, número e caractere especial.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->validationError($this->validator->getErrors());
        }

        $dto = new CreateUserRequest(
            nome_completo: $this->request->getJsonVar('nome_completo'),
            email: $this->request->getJsonVar('email'),
            password: $this->request->getJsonVar('password')
        );

        try {
            $result = Services::authService()->register($dto);
        } catch (RuntimeException $e) {
            return $this->conflict($e->getMessage());
        }

        $response = $this->created(
            ['user' => $result['user']->toArray()],
            'Usuário registrado com sucesso.',
        );

        return $this->setTokenCookie($response, $result['token'], $result['expires_in']);
    }

    public function login(): ResponseInterface
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->validationError($this->validator->getErrors());
        }

        $dto = new LoginRequest(
            email: $this->request->getJsonVar('email'),
            password: $this->request->getJsonVar('password')
        );

        $result = Services::authService()->authenticate($dto);

        if ($result === null) {
            return $this->unauthorized('Credenciais inválidas.');
        }

        $response = $this->success(['user' => $result['user']->toArray()]);

        return $this->setTokenCookie($response, $result['token'], $result['expires_in']);
    }

    public function me(): ResponseInterface
    {
        $token   = $this->request->getCookie('jwt_token');
        $decoded = Services::authService()->validateToken($token);

        if ($decoded === null) {
            return $this->unauthorized('Token inválido ou expirado.');
        }

        $userDto = new UserResponse(
            id: $decoded->sub,
            nome_completo: $decoded->data->nome_completo,
            email: $decoded->data->email
        );

        return $this->success(['user' => $userDto->toArray()]);
    }

    public function logout(): ResponseInterface
    {
        $token = $this->request->getCookie('jwt_token');

        if (!empty($token)) {
            Services::authService()->invalidateToken($token);
        }

        $response = $this->success(message: 'Logout realizado com sucesso.');

        return $this->clearTokenCookie($response);
    }

    private function setTokenCookie(ResponseInterface $response, string $token, int $expiresIn): ResponseInterface
    {
        $secure = env('CI_ENVIRONMENT') === 'production';

        return $response->setCookie(
            'jwt_token',
            $token,
            $expiresIn,
            '',
            '/',
            '',
            $secure,
            true,
            'Lax'
        );
    }

    private function clearTokenCookie(ResponseInterface $response): ResponseInterface
    {
        $secure = env('CI_ENVIRONMENT') === 'production';

        return $response->setCookie(
            'jwt_token',
            '',
            0,
            '',
            '/',
            '',
            $secure,
            true,
            'Lax'
        );
    }
}
