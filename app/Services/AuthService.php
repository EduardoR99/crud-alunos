<?php

namespace App\Services;

use App\DTOs\Request\CreateUserRequest;
use App\DTOs\Request\LoginRequest;
use App\DTOs\Response\UserResponse;
use App\Entities\User;
use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use RuntimeException;

class AuthService
{
    private string $secretKey;
    private int $expiration;

    public function __construct(
        private readonly UserModel $userModel
    ) {
        $this->secretKey  = env('JWT_SECRET_KEY', '');
        $this->expiration = (int) env('JWT_EXPIRATION', 3600);

        if ($this->secretKey === '') {
            throw new RuntimeException('JWT_SECRET_KEY não configurada no .env');
        }
    }

    public function register(CreateUserRequest $dto): array
    {
        $existing = $this->userModel->findByEmail($dto->email);

        if ($existing !== null) {
            throw new RuntimeException('E-mail já cadastrado.');
        }

        $user = new User([
            'nome_completo' => $dto->nome_completo,
            'email'         => $dto->email,
        ]);
        $user->setPassword($dto->password);

        $this->userModel->insert($user);
        $user->id = (int) $this->userModel->getInsertID();

        $token = $this->generateToken($user);

        return [
            'token'      => $token,
            'expires_in' => $this->expiration,
            'user'       => UserResponse::fromEntity($user),
        ];
    }

    public function authenticate(LoginRequest $dto): ?array
    {
        $user = $this->userModel->findByEmail($dto->email);

        if ($user === null || !$user->verifyPassword($dto->password)) {
            return null;
        }

        $token = $this->generateToken($user);

        return [
            'token'      => $token,
            'expires_in' => $this->expiration,
            'user'       => UserResponse::fromEntity($user),
        ];
    }

    public function validateToken(string $token): ?object
    {
        try {
            if ($this->isTokenBlacklisted($token)) {
                return null;
            }

            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (ExpiredException) {
            log_message('info', 'JWT expirado.');

            return null;
        } catch (SignatureInvalidException) {
            log_message('warning', 'JWT com assinatura inválida.');

            return null;
        } catch (\Exception $e) {
            log_message('error', 'Falha ao decodificar JWT: ' . $e->getMessage());

            return null;
        }
    }

    public function invalidateToken(string $token): void
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $ttl     = $decoded->exp - time();

            if ($ttl > 0) {
                $key = 'jwt_blacklist_' . hash('sha256', $token);
                \Config\Services::cache()->save($key, true, $ttl);
            }
        } catch (\Exception) {
        }
    }

    private function isTokenBlacklisted(string $token): bool
    {
        $key = 'jwt_blacklist_' . hash('sha256', $token);

        return (bool) \Config\Services::cache()->get($key);
    }

    private function generateToken(User $user): string
    {
        $now = time();

        $payload = [
            'iss' => base_url(),
            'iat' => $now,
            'exp' => $now + $this->expiration,
            'sub' => $user->id,
            'data' => [
                'email'         => $user->email,
                'nome_completo' => $user->nome_completo,
            ],
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
