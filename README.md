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
npm run lint     # Executar linting (ESLint)
```

---

## Permissoes do Diretorio `writable/`

O CodeIgniter precisa de permissao de escrita no diretorio `writable/` e seus subdiretorios. Em **Linux/Mac**, execute:

```bash
chmod -R 775 writable/
```

Subdiretorios necessarios (ja incluidos no projeto):

- `writable/cache/` — Cache da aplicacao e rate limiting
- `writable/logs/` — Logs de erro e debug
- `writable/session/` — Dados de sessao
- `writable/uploads/` — Upload de arquivos (fotos de alunos)

> **Nota:** No Windows, normalmente nao e necessario ajustar permissoes.

---

## Usando Apache (alternativa ao `php spark serve`)

Se preferir usar **Apache** em vez do servidor embutido do PHP, certifique-se de que:

1. O `mod_rewrite` esta habilitado:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. O `DocumentRoot` aponta para a pasta `public/` do projeto (nao para a raiz).

3. O `AllowOverride All` esta configurado no VirtualHost para que o `.htaccess` funcione.

> O arquivo `public/.htaccess` ja esta configurado com as regras de rewrite necessarias.

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

## CORS (Cross-Origin Resource Sharing)

O projeto ja vem configurado para lidar com CORS em ambiente de desenvolvimento. O backend permite requisicoes do frontend (`http://localhost:5173`) automaticamente.

### Como funciona

- O filtro CORS roda **antes e depois** de toda requisicao, tratando preflight (`OPTIONS`) e adicionando os headers necessarios na resposta.
- O frontend envia cookies de autenticacao (JWT) via `withCredentials: true` no Axios, e o backend aceita com `supportsCredentials: true`.
- Headers permitidos: `Content-Type`, `Authorization`, `X-Requested-With`
- Metodos permitidos: `GET`, `POST`, `PUT`, `DELETE`, `OPTIONS`

### Porta do frontend diferente

Se o Vite subir em uma porta diferente da `5173` (por exemplo, `5174` quando a porta padrao esta ocupada), voce vera um erro de CORS no navegador. Para corrigir, atualize o `.env` do backend:

```env
# Uma unica origem
CORS_ALLOWED_ORIGINS = 'http://localhost:5174'

# Ou multiplas origens separadas por virgula
CORS_ALLOWED_ORIGINS = 'http://localhost:5173,http://localhost:5174'
```

> **Importante:** Apos alterar o `.env`, reinicie o servidor backend (`php spark serve`).

---

## Solucao de Problemas

| Problema | Solucao |
|----------|---------|
| Erro de CORS no navegador | Verifique se a porta do frontend corresponde ao valor de `CORS_ALLOWED_ORIGINS` no `.env` do backend. Veja a secao [CORS](#cors-cross-origin-resource-sharing) acima |
| Erro de conexao com banco | Confirme usuario, senha e nome do banco no `.env` |
| `php spark serve` nao funciona | Verifique se o PHP 8.2+ esta no PATH do sistema |
| `npm run dev` falha | Delete `node_modules` e `package-lock.json`, depois rode `npm install` novamente |
| Token JWT expirado | Faca login novamente. O token expira em 1 hora por padrao |
| Cookies nao enviados pelo navegador | Certifique-se de que o backend e frontend estao rodando em `localhost` (nao use `127.0.0.1` em um e `localhost` no outro) |
| Busca de CEP nao funciona | O frontend consulta a API do [ViaCEP](https://viacep.com.br). Verifique sua conexao com a internet |
| Erro de permissao no `writable/` | Execute `chmod -R 775 writable/` (Linux/Mac). Veja a secao [Permissoes](#permissoes-do-diretorio-writable) |
| Rotas retornam 404 no Apache | Habilite o `mod_rewrite` e aponte o DocumentRoot para `public/`. Veja a secao [Apache](#usando-apache-alternativa-ao-php-spark-serve) |

---

## Licenca

Este projeto esta sob a licenca MIT.
