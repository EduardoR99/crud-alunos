<?php

namespace App\Database\Seeds;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('SEED_ADMIN_EMAIL', 'admin@admin.com');
        $password = env('SEED_ADMIN_PASSWORD');

        if (empty($password)) {
            CLI::write('SEED_ADMIN_PASSWORD não definida no .env. Seeder ignorado.', 'red');

            return;
        }

        $userModel = new UserModel();

        $existing = $userModel->findByEmail($email);

        if ($existing !== null) {
            CLI::write('Usuário admin já existe. Seeder ignorado.', 'yellow');

            return;
        }

        $user = new User([
            'nome_completo' => env('SEED_ADMIN_NAME', 'Administrador'),
            'email'         => $email,
        ]);
        $user->setPassword($password);

        $userModel->insert($user);

        CLI::write('Usuário admin criado com sucesso.', 'green');
    }
}
