<div align="center">

# 🎓 Portal SI

### Conhecimento, tecnologia e comunidade em um só lugar.

Portal acadêmico do curso de **Sistemas de Informação — ESUCRI**, desenvolvido na disciplina de **Projeto de Extensão IV**.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css&logoColor=white)

**Projeto em desenvolvimento · Aplicação acadêmica**

[Sobre](#sobre-o-projeto) · [Funcionalidades](#funcionalidades) · [Instalação](#como-executar-localmente) · [Próximas etapas](#próximas-etapas)

</div>

---

## Sobre o projeto

O **Portal SI** é um espaço para divulgar as iniciativas da comunidade acadêmica: projetos de extensão, ações sociais, notícias, eventos e produção científica. A proposta é aproximar estudantes, professores e comunidade, dando visibilidade ao conhecimento produzido no curso.

O desenvolvimento acontece de forma incremental, aplicando conceitos de programação web, integração com banco de dados, autenticação e interfaces responsivas.

## Funcionalidades

| Área | O que já está disponível |
| :--- | :--- |
| **Página inicial** | Exibição de conteúdos publicados, com categoria, resumo e data de publicação. |
| **Cadastro e login** | Cadastro de contas como aluno, armazenamento de senha com hash e autenticação de usuários ativos. |
| **Perfis de acesso** | Redirecionamento após login conforme o perfil e restrição do dashboard aos administradores. |
| **Cabeçalho da home** | Identificação do usuário autenticado e opção de sair da sessão. |
| **Dashboard** | Boas-vindas personalizadas, dados da conta e apresentação das futuras áreas de gestão. |
| **Usuários** | Listagem administrativa com ID, nome, e-mail, perfil, status e data de cadastro. |
| **Interface** | Identidade visual compartilhada entre home e dashboard, com layouts adaptados para desktop e celular. |

> O módulo Usuários já permite consultar as contas cadastradas. Cadastro administrativo, edição, filtros e alterações de permissões serão desenvolvidos nas próximas etapas. Os demais cards apresentam áreas planejadas do painel.

## Tecnologias e organização

- **PHP 8+** para renderização das páginas, sessões e regras de acesso.
- **PostgreSQL** para persistência dos dados.
- **PDO** e consultas parametrizadas para acesso ao banco.
- **HTML5, CSS3 e SVG** para estrutura, estilos e ícones.
- **Apache / XAMPP** como opção de ambiente local.

O projeto organiza o código em pastas de modelos, visualizações e helpers. Parte do processamento de login e cadastro está nas próprias páginas, conforme a evolução das aulas.

```text
portal-si/
├── app/
│   ├── controllers/       # Estrutura para controladores
│   ├── helpers/           # Autenticação e escape de HTML
│   ├── models/            # Acesso aos dados
│   └── views/
│       ├── admin/         # Login, cadastro, dashboard, usuários e logout
│       ├── home/          # Página inicial
│       └── layout/        # Cabeçalho e rodapé
├── config/                # Configuração de banco e autenticação
├── database/
│   ├── schema.sql         # Tabelas, relacionamentos, índices e triggers
│   └── seed.sql           # Dados de exemplo para desenvolvimento
├── docs/                  # Espaço para documentação
├── public/
│   ├── assets/            # CSS e imagens
│   └── index.php          # Entrada da home
└── README.md
```

## Como executar localmente

### 1. Prepare o ambiente

Tenha **PHP 8 ou superior**, **PostgreSQL** e um servidor web com suporte a PHP. No PHP usado pelo servidor, habilite as extensões `pdo_pgsql` e `mbstring`.

O XAMPP pode fornecer o Apache e o PHP. O banco utilizado pelo projeto é o **PostgreSQL**, que deve estar instalado e em execução separadamente.

### 2. Clone o repositório

No terminal, dentro da pasta `htdocs` do XAMPP:

```bash
git clone https://github.com/ViiniP/portal-si.git
cd portal-si
git switch admin
```

No Windows, um caminho comum para o projeto é `C:\xampp\htdocs\portal-si`.

### 3. Crie e prepare o banco

Crie um banco chamado `portal_si` e execute os scripts **nesta ordem**, pelo pgAdmin ou pelo terminal com `psql` disponível no PATH:

```bash
psql -U postgres -c "CREATE DATABASE portal_si;"
psql -U postgres -d portal_si -f database/schema.sql
psql -U postgres -d portal_si -f database/seed.sql
```

O `schema.sql` deve ser executado em um banco novo. O `seed.sql` é opcional e adiciona dados de exemplo.

### 4. Configure a conexão

Edite [`config/database.php`](config/database.php) e ajuste host, porta, nome do banco, usuário e senha de acordo com seu ambiente local. Não publique credenciais pessoais nas alterações do repositório.

### 5. Abra o portal

Inicie o Apache e acesse:

| Página | Endereço local |
| :--- | :--- |
| Home | `http://localhost/portal-si/public/index.php` |
| Login | `http://localhost/portal-si/app/views/admin/login.php` |
| Cadastro | `http://localhost/portal-si/app/views/admin/cadastro.php` |
| Dashboard | `http://localhost/portal-si/app/views/admin/dashboard.php` |
| Usuários | `http://localhost/portal-si/app/views/admin/usuarios.php` |

A estrutura atual utiliza páginas de `app/views/admin` diretamente nas URLs. Para esse fluxo, disponibilize a pasta completa do projeto pelo servidor, como no exemplo com XAMPP.

As páginas administrativas ficam somente em `app/views/admin`. A antiga pasta `app/admin`, que duplicava essas páginas, foi removida; atualize favoritos antigos para os endereços acima.

## Perfis e primeiro acesso

| Perfil | Destino após login | Acesso ao dashboard |
| :--- | :--- | :---: |
| `admin` | Dashboard administrativo | ✅ |
| `aluno` | Página inicial | — |
| `editor` | Página inicial, por enquanto | — |

Visitantes que tentam abrir o dashboard são encaminhados ao login. Usuários autenticados sem perfil `admin` são encaminhados à home.

### Criar um administrador no ambiente local

1. Cadastre uma conta pelo formulário do portal.
2. No seu banco local, altere o perfil dessa conta, substituindo o e-mail abaixo:

```sql
UPDATE usuarios
SET perfil = 'admin'
WHERE email = 'seu-email@exemplo.com';
```

3. Saia da sessão e faça login novamente com a senha escolhida no cadastro.

> Os hashes de senha do `seed.sql` são exemplos. Eles não definem uma senha válida para entrar nas contas de demonstração.

## Banco de dados

A estrutura já prevê as seguintes entidades:

| Entidade | Finalidade |
| :--- | :--- |
| `usuarios` | Contas, perfis e status de acesso |
| `categorias` | Organização dos conteúdos |
| `conteudos` | Publicações, autores, destaques e fluxo editorial |
| `tags` / `conteudo_tags` | Marcadores e associações com publicações |
| `eventos` | Agenda acadêmica |
| `midias` | Metadados dos arquivos enviados |
| `auditoria` | Histórico de ações administrativas |

A existência das tabelas não significa que todas as telas e operações desses módulos já estejam disponíveis.

## Próximas etapas

- [x] Integrar a home aos conteúdos publicados no banco.
- [x] Implementar cadastro, login e redirecionamento por perfil.
- [x] Proteger o acesso ao dashboard administrativo.
- [x] Unificar a identidade visual da home e do dashboard.
- [x] Listar usuários em uma página exclusiva para administradores.
- [ ] Implementar as páginas de leitura de conteúdo e navegação por categoria.
- [ ] Ampliar a gestão de usuários com busca, cadastro administrativo, edição e permissões.
- [ ] Criar, editar, revisar, publicar e arquivar conteúdos.
- [ ] Gerenciar categorias, tags e eventos.
- [ ] Disponibilizar upload e organização de mídias.
- [ ] Integrar indicadores reais e registros de auditoria ao painel.

## Fluxo de desenvolvimento

| Branch | Uso |
| :--- | :--- |
| `main` | Atividades e evolução realizadas em sala de aula |
| `admin` | Testes em casa e desenvolvimento da área administrativa |

Salve suas alterações em um commit antes de alternar de branch:

```bash
# Trabalhar nas atividades de aula
git switch main

# Continuar os testes e o painel administrativo
git switch admin
```

---

<div align="center">

**Portal SI · Sistemas de Informação · ESUCRI**

Conectando conhecimento e comunidade.

</div>
