# CRUD Alunos

Sistema completo de gerenciamento de alunos com backend em **CodeIgniter 4** (PHP) e frontend em **React** com **Tailwind CSS**.

## Funcionalidades

- Autenticacao com JWT (registro, login, logout)
- CRUD completo de alunos (criar, listar, editar, excluir)
- Gerenciamento de contatos e enderecos dos alunos
- Soft deletes (exclusao logica)
- Rate limiting nas rotas de autenticacao
- Tema claro/escuro
- Validacao de formularios com Zod
- Interface responsiva com Tailwind CSS

## Tecnologias

### Backend
- PHP 8.2+
- CodeIgniter 4.7
- MySQL/MariaDB
- JWT (firebase/php-jwt)

### Frontend
- React 19
- Vite 7
- Tailwind CSS 3
- React Router 7
- React Hook Form + Zod
- Axios

---

## Pre-requisitos

Antes de comecar, voce precisa ter instalado:

- [PHP 8.2+](https://www.php.net/downloads) com as extensoes: `intl`, `mbstring`, `mysqlnd`, `curl`, `json`
- [Composer](https://getcomposer.org/download/)
- [MySQL](https://dev.mysql.com/downloads/) ou [MariaDB](https://mariadb.org/download/)
- [Node.js 18+](https://nodejs.org/) (inclui o npm)
- [Git](https://git-scm.com/downloads)

---

## Instalacao

### 1. Clonar o repositorio

```bash
git clone https://github.com/seu-usuario/crud-alunos.git
cd crud-alunos
```

### 2. Configurar o Backend

#### 2.1 Instalar dependencias do PHP

```bash
composer install
```

#### 2.2 Configurar variaveis de ambiente

Copie o arquivo `env` para `.env` e edite as configuracoes:

```bash
cp env .env
```

Abra o `.env` e configure:

```env
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = development

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = 'http://localhost:8080/'

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = crud_alunos
database.default.username = root
database.default.password = SUA_SENHA_AQUI
database.default.DBDriver = MySQLi
database.default.port = 3306

#--------------------------------------------------------------------
# JWT
#--------------------------------------------------------------------
JWT_SECRET_KEY = 'GERE_UMA_CHAVE_SECRETA_AQUI'
JWT_EXPIRATION = 3600

#--------------------------------------------------------------------
# CORS
#--------------------------------------------------------------------
CORS_ALLOWED_ORIGINS = 'http://localhost:5173'

#--------------------------------------------------------------------
# SEED (usuario admin padrao)
#--------------------------------------------------------------------
SEED_ADMIN_NAME = 'Administrador'
SEED_ADMIN_EMAIL = 'admin@admin.com'
SEED_ADMIN_PASSWORD = 'SUA_SENHA_ADMIN_AQUI'
```

> **Dica:** Para gerar uma chave JWT segura, voce pode usar:
> ```bash
> php -r "echo bin2hex(random_bytes(32));"
> ```

#### 2.3 Criar o banco de dados

Acesse o MySQL e crie o banco:

```sql
CREATE DATABASE crud_alunos CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

#### 2.4 Executar as migrations

```bash
php spark migrate
```

Isso criara as tabelas: `users`, `students`, `student_contacts` e `student_addresses`.

#### 2.5 Criar usuario administrador (opcional)

```bash
php spark db:seed UserSeeder
```

Isso criara o usuario admin com as credenciais definidas no `.env`.

### 3. Configurar o Frontend

#### 3.1 Instalar dependencias do Node.js

```bash
cd frontend
npm install
```

#### 3.2 Configurar variaveis de ambiente (opcional)

O frontend ja vem configurado para desenvolvimento. Se precisar alterar a URL da API, edite o arquivo `frontend/.env.development`:

```env
VITE_API_BASE_URL=http://localhost:8080/api
```

---

## Executando o Projeto

Voce precisara de **dois terminais** abertos simultaneamente:

### Terminal 1 - Backend

```bash
# Na raiz do projeto
php spark serve
```

O backend estara disponivel em: `http://localhost:8080`

### Terminal 2 - Frontend

```bash
# Na pasta frontend
cd frontend
npm run dev
```

O frontend estara disponivel em: `http://localhost:5173`

---

## Endpoints da API

### Autenticacao (publicos)

| Metodo | Rota                  | Descricao              |
|--------|-----------------------|------------------------|
| POST   | `/api/auth/register`  | Registrar novo usuario |
| POST   | `/api/auth/login`     | Fazer login            |
| POST   | `/api/auth/logout`    | Fazer logout           |
| GET    | `/api/auth/me`        | Dados do usuario logado|

### Alunos (protegidos por JWT)

| Metodo | Rota                  | Descricao              |
|--------|-----------------------|------------------------|
| GET    | `/api/students`       | Listar alunos          |
| GET    | `/api/students/:id`   | Detalhes do aluno      |
| POST   | `/api/students`       | Cadastrar aluno        |
| PUT    | `/api/students/:id`   | Atualizar aluno        |
| DELETE | `/api/students/:id`   | Excluir aluno          |

---

## Estrutura do Projeto

```
crud-alunos/
├── app/
│   ├── Config/              # Configuracoes (rotas, banco, CORS, filtros)
│   ├── Controllers/Api/     # Controllers da API
│   ├── Database/
│   │   ├── Migrations/      # Migrations do banco de dados
│   │   └── Seeds/           # Seeders (dados iniciais)
│   ├── Entities/            # Entidades (User, Student, etc.)
│   ├── Filters/             # Filtros (JWT, Rate Limit)
│   ├── Models/              # Models do banco de dados
│   ├── Rules/               # Regras de validacao
│   ├── Services/            # Camada de servicos
│   └── Traits/              # Traits reutilizaveis
├── frontend/
│   ├── src/
│   │   ├── components/      # Componentes React
│   │   ├── pages/           # Paginas da aplicacao
│   │   ├── services/        # Servicos de API (Axios)
│   │   ├── contexts/        # Contextos React (Auth, Theme)
│   │   ├── hooks/           # Hooks customizados
│   │   ├── models/          # Modelos de dados
│   │   ├── utils/           # Utilitarios
│   │   └── config/          # Configuracoes do frontend
│   ├── .env.development     # Variaveis de ambiente (dev)
│   └── .env.production      # Variaveis de ambiente (prod)
├── tests/                   # Testes automatizados
├── public/                  # Arquivos publicos
├── writable/                # Logs, cache, uploads
├── composer.json            # Dependencias PHP
└── .env                     # Variaveis de ambiente do backend
```

---

## Scripts Disponiveis

### Backend

```bash
php spark serve              # Iniciar servidor de desenvolvimento
php spark migrate            # Executar migrations
php spark migrate:rollback   # Reverter ultima migration
php spark db:seed UserSeeder # Criar usuario admin
```

### Frontend

```bash
npm run dev      # Iniciar servidor de desenvolvimento
npm run build    # Gerar build de producao
npm run preview  # Visualizar build de producao
npm run test     # Executar testes
```

---

## Verificacao das Extensoes PHP

Para verificar se todas as extensoes necessarias estao habilitadas:

```bash
php -m | grep -E "intl|mbstring|mysqlnd|curl|json"
```

Caso alguma extensao esteja faltando, habilite-a no arquivo `php.ini` removendo o `;` da linha correspondente:

```ini
extension=intl
extension=mbstring
extension=curl
extension=mysqlnd
```

---

## Solucao de Problemas

| Problema | Solucao |
|----------|---------|
| Erro de CORS no navegador | Verifique se `CORS_ALLOWED_ORIGINS` no `.env` corresponde a URL do frontend |
| Erro de conexao com banco | Confirme usuario, senha e nome do banco no `.env` |
| `php spark serve` nao funciona | Verifique se o PHP 8.2+ esta no PATH do sistema |
| `npm run dev` falha | Delete `node_modules` e `package-lock.json`, depois rode `npm install` novamente |
| Token JWT expirado | Faca login novamente. O token expira em 1 hora por padrao |

---

## Licenca

Este projeto esta sob a licenca MIT.
