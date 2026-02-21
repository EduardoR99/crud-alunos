<?php

namespace Tests\Unit\Services;

use App\Entities\User;
use App\Models\UserModel;
use App\Services\AuthService;
use CodeIgniter\Test\CIUnitTestCase;
use RuntimeException;

/**
 * @internal
 */
final class AuthServiceTest extends CIUnitTestCase
{
    private AuthService $authService;
    private UserModel $mockUserModel;

    protected function setUp(): void
    {
        parent::setUp();

        // Garante que JWT_SECRET_KEY está definida para testes
        $_SERVER['JWT_SECRET_KEY']  = 'test-secret-key-for-phpunit-32bytes!!';
        $_SERVER['JWT_EXPIRATION']  = '3600';

        $this->mockUserModel = $this->createMock(UserModel::class);
        $this->authService   = new AuthService($this->mockUserModel);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($_SERVER['JWT_SECRET_KEY'], $_SERVER['JWT_EXPIRATION']);
    }

    // ─── Register ──────────────────────────────────────────

    public function testRegisterReturnsTokenAndUserData(): void
    {
        $this->mockUserModel->method('findByEmail')->willReturn(null);
        $this->mockUserModel->method('insert')->willReturn(true);
        $this->mockUserModel->method('getInsertID')->willReturn(1);

        $result = $this->authService->register('João Silva', 'joao@test.com', 'Senha@123');

        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('expires_in', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame('João Silva', $result['user']['nome_completo']);
        $this->assertSame('joao@test.com', $result['user']['email']);
        $this->assertSame(1, $result['user']['id']);
        $this->assertNotEmpty($result['token']);
    }

    public function testRegisterThrowsOnDuplicateEmail(): void
    {
        $existingUser = new User(['email' => 'joao@test.com']);

        $this->mockUserModel->method('findByEmail')->willReturn($existingUser);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('E-mail já cadastrado.');

        $this->authService->register('João Silva', 'joao@test.com', 'Senha@123');
    }

    // ─── Authenticate ──────────────────────────────────────

    public function testAuthenticateReturnsDataOnValidCredentials(): void
    {
        $user = new User([
            'nome_completo' => 'Maria Souza',
            'email'         => 'maria@test.com',
        ]);
        $user->id = 5;
        $user->setPassword('Senha@123');

        $this->mockUserModel->method('findByEmail')->willReturn($user);

        $result = $this->authService->authenticate('maria@test.com', 'Senha@123');

        $this->assertNotNull($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertSame(5, $result['user']['id']);
        $this->assertSame('Maria Souza', $result['user']['nome_completo']);
    }

    public function testAuthenticateReturnsNullOnWrongPassword(): void
    {
        $user = new User([
            'nome_completo' => 'Maria Souza',
            'email'         => 'maria@test.com',
        ]);
        $user->setPassword('Senha@123');

        $this->mockUserModel->method('findByEmail')->willReturn($user);

        $result = $this->authService->authenticate('maria@test.com', 'SenhaErrada@1');

        $this->assertNull($result);
    }

    public function testAuthenticateReturnsNullOnNonexistentUser(): void
    {
        $this->mockUserModel->method('findByEmail')->willReturn(null);

        $result = $this->authService->authenticate('naoexiste@test.com', 'Qualquer@1');

        $this->assertNull($result);
    }

    // ─── Token Validation ──────────────────────────────────

    public function testValidateTokenReturnsDecodedPayload(): void
    {
        $this->mockUserModel->method('findByEmail')->willReturn(null);
        $this->mockUserModel->method('insert')->willReturn(true);
        $this->mockUserModel->method('getInsertID')->willReturn(1);

        $result = $this->authService->register('Test User', 'test@test.com', 'Test@123');
        $decoded = $this->authService->validateToken($result['token']);

        $this->assertNotNull($decoded);
        $this->assertSame(1, $decoded->sub);
        $this->assertSame('test@test.com', $decoded->data->email);
    }

    public function testValidateTokenReturnsNullOnInvalidToken(): void
    {
        $result = $this->authService->validateToken('token.invalido.aqui');

        $this->assertNull($result);
    }

    public function testValidateTokenReturnsNullOnTamperedToken(): void
    {
        $this->mockUserModel->method('findByEmail')->willReturn(null);
        $this->mockUserModel->method('insert')->willReturn(true);
        $this->mockUserModel->method('getInsertID')->willReturn(1);

        $result  = $this->authService->register('Test User', 'test@test.com', 'Test@123');
        $decoded = $this->authService->validateToken($result['token'] . 'tampered');

        $this->assertNull($decoded);
    }

    // ─── Token Blacklist ───────────────────────────────────

    public function testInvalidateTokenBlacklistsIt(): void
    {
        $this->mockUserModel->method('findByEmail')->willReturn(null);
        $this->mockUserModel->method('insert')->willReturn(true);
        $this->mockUserModel->method('getInsertID')->willReturn(1);

        $result = $this->authService->register('Test User', 'test@test.com', 'Test@123');
        $token  = $result['token'];

        // Antes de invalidar, token é válido
        $this->assertNotNull($this->authService->validateToken($token));

        // Invalida
        $this->authService->invalidateToken($token);

        // Depois de invalidar, token não é mais válido
        $this->assertNull($this->authService->validateToken($token));
    }

    // ─── Password Hashing (Entity) ────────────────────────

    public function testUserPasswordHashingWorks(): void
    {
        $user = new User();
        $user->setPassword('MinhaSenh@1');

        $this->assertTrue($user->verifyPassword('MinhaSenh@1'));
        $this->assertFalse($user->verifyPassword('OutraSenha@2'));
    }
}
