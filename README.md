# Portal SI · ESUCRI

Portal acadêmico em PHP e PostgreSQL para publicar projetos, notícias e iniciativas do curso de Sistemas de Informação.

## Abrir no ambiente atual

Com Apache e PostgreSQL ligados, acesse:

- Portal: http://localhost/portal-si/public/index.php
- Login: http://localhost/portal-si/app/views/admin/login.php
- Cadastro: http://localhost/portal-si/app/views/admin/cadastro_usuario.php

Não é necessário compilar o frontend nem acessar serviços externos. Fontes e estilos funcionam sem CDN.

O projeto mantém as URLs PHP existentes em `app/views/admin`. Sirva a raiz do projeto (como o XAMPP já faz), não somente a pasta `public`.

## Requisitos

PHP 8.1 ou superior com `pdo_pgsql`, `mbstring`, `fileinfo` e `iconv`. Os testes HTTP também usam `curl`, `dom` e `proc_open`. PostgreSQL deve estar disponível.

Neste computador, use **C:\xampp\php\php.exe**. O `php` encontrado primeiro no PATH é outra instalação, sem as extensões necessárias habilitadas.

A configuração aceita `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASSWORD` por variáveis de ambiente. Os valores locais padrão foram mantidos em `config/database.php`.

## Instalação em um banco novo

1. Crie o banco `portal_si`.
2. Execute `database/schema.sql` nesse banco vazio.
3. Execute `database/seed.sql`.

**Não execute o schema novamente sobre seu banco existente.** A revisão não exige recriar tabelas ou reimportar seus dados.

O seed oferece contas exclusivamente para demonstração local:

| Perfil | E-mail | Senha de demonstração |
|---|---|---|
| Administrador | admin@portal-si.local | PortalSI@2026 |
| Editor | jucemar@portal-si.local | PortalSI@2026 |
| Aluno | academico@portal-si.local | PortalSI@2026 |

O seed usa `ON CONFLICT DO NOTHING`: ele **não troca senhas de contas já existentes**. As credenciais acima servem para uma instalação nova. No banco atual, use sua conta existente. Alguns registros antigos ainda contêm hashes de exemplo inválidos; eles foram preservados.

## Fluxo de publicação

- Administradores entram no dashboard e têm acesso completo às publicações, incluindo publicar e arquivar.
- Novos cadastros recebem o perfil aluno.
- Alunos entram na Home pública e não acessam o painel ou as operações de gestão, mesmo por URL direta.
- Editores entram no dashboard, criam seus próprios rascunhos e enviam para revisão. Um administrador publica.
- Editores não publicam nem arquivam conteúdos, e não editam nem excluem conteúdos de outros autores ou materiais já publicados/arquivados.
- A Home oferece saída da conta por POST com CSRF e acesso ao painel somente para editores e administradores.
- Somente conteúdos publicados, com data não futura e categoria ativa, aparecem no portal.
- Uploads aceitam imagens JPG, PNG e GIF de até 2 MB.
- Exclusão exige uma tela de confirmação e envio POST.
- O corpo da publicação é texto simples, com preservação das quebras de linha.
- Vídeos são links HTTPS para o YouTube.

## Testes

No PowerShell, a partir da raiz do projeto:

```powershell
& C:\xampp\php\php.exe tests/lint.php
& C:\xampp\php\php.exe tests/integration.php
npm run check:css
```

A integração cria um schema PostgreSQL com nome aleatório `portal_si_test_...`, carrega o schema/seed, inicia PHP em `127.0.0.1:8765` e testa requisições HTTP reais. Ao terminar, remove seu schema, servidor e arquivos temporários. Precisa de permissão para criar schemas no banco e da porta 8765 livre. **Não usa nem altera as tabelas do schema original.**

`--visual` mantém o servidor isolado aberto após os testes para conferência no navegador. Para encerrar:

```powershell
New-Item tests/.runtime/stop -ItemType File -Force
```

Os comandos npm de PHP usam a instalação configurada no PATH. Prefira os comandos explícitos acima neste computador.

## Organização

| Caminho | Responsabilidade |
|---|---|
| `public` | Entrada pública, categorias, página de leitura e estilos |
| `app/views/admin` | Login, cadastro, painel e gestão de publicações |
| `app/views/admin/includes` | Layout administrativo e editor compartilhado |
| `app/helpers` | Escape HTML, URLs, sessão, CSRF, permissões e uploads |
| `app/controllers/Authcontroller.php` | Autenticação e renovação da sessão |
| `app/models` | Consultas de usuários, categorias e conteúdos |
| `config/database.php` | Conexão PDO centralizada |
| `database` | Estrutura e dados de demonstração |
| `tests` | Validação de sintaxe e testes HTTP |
| `docs/revisao-apresentacao.md` | Panorama da revisão e roteiro de apresentação |

Os estilos antigos `login.css`, `cadastro.css` e `dashboard.css` foram preservados, mas as telas atualizadas usam `auth.css` e `app/views/admin/css/admin.css`.

O Apache usa os novos arquivos `.htaccess` para impedir listagem de pastas e acesso direto a recursos internos. O antigo `public/.htacess` estava vazio e tinha o nome incorreto; não é usado.

## Escopo atual

O banco já contém tabelas de eventos, tags, mídias e auditoria, mas não há telas de gestão dedicadas a elas. O item Eventos do menu filtra publicações da categoria Eventos Acadêmicos.

As imagens citadas pelo seed não existem no repositório. O portal mostra esses conteúdos sem uma capa quebrada; imagens enviadas pelo formulário são exibidas normalmente.

Antes de uma implantação pública, ainda será necessário configurar credenciais próprias, HTTPS, limitação de tentativas de login, recuperação de senha, política de backups e revisar permissões de infraestrutura. Esta revisão validou o projeto local para demonstração, não uma implantação de produção.
