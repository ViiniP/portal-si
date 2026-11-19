# Portal SI · ESUCRI

Portal acadêmico em PHP e PostgreSQL para publicar projetos, notícias e iniciativas do curso de Sistemas de Informação.

## Abrir no ambiente atual

Com Apache ligado e o banco configurado disponível (PostgreSQL local ou Aiven), acesse:

- Portal: http://localhost/portal-si/public/index.php
- Login: http://localhost/portal-si/app/views/admin/login.php
- Cadastro: http://localhost/portal-si/app/views/admin/cadastro_usuario.php

Não é necessário compilar o frontend. Fontes e estilos funcionam sem CDN. Com o banco no Aiven, o servidor PHP precisa de acesso à internet.

O projeto mantém as URLs PHP existentes em `app/views/admin`. Sirva a raiz do projeto (como o XAMPP já faz), não somente a pasta `public`.

## Requisitos

PHP 8.1 ou superior com `pdo_pgsql`, `mbstring`, `fileinfo` e `iconv`. Os testes HTTP também usam `curl`, `dom` e `proc_open`. PostgreSQL deve estar disponível.

Neste computador, use **C:\xampp\php\php.exe**. O `php` encontrado primeiro no PATH é outra instalação, sem as extensões necessárias habilitadas.

A configuração aceita `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_SSLMODE` e `DB_SSLROOTCERT` por variáveis de ambiente. Os valores locais padrão foram mantidos em `config/database.php`.

### Banco no Aiven

Neste computador, a conexão pode ser configurada em `config/database.local.php`, um arquivo PHP que retorna um array com as mesmas chaves `DB_*`. Esse arquivo é privado e ignorado pelo Git. Um `DB_HOST` definido no ambiente desativa a configuração local inteira, permitindo selecionar outro banco sem misturar credenciais.

A migração de 19/11/2026 copiou as 8 tabelas do `portal_si` local para o banco `defaultdb` do serviço `pg-vinipi`. Foram comparados todos os registros e as 7 sequências de IDs antes da ativação da conexão neste computador.

Para o Aiven, use `DB_SSLMODE=verify-full` e `DB_SSLROOTCERT` com o caminho absoluto do certificado CA obtido no console do serviço. O certificado local fica em `config/aiven-ca.pem`. Nunca coloque senhas ou URLs com credenciais no README ou em arquivos versionados. O projeto não carrega arquivos `.env` automaticamente.

A migração usa `pg_dump` e `pg_restore --no-owner --no-acl --single-transaction --exit-on-error` em um banco de destino vazio. Backups privados ficam em `database/private/`, fora do Git e bloqueados por HTTP. O banco PostgreSQL local é preservado. Após a troca, novas gravações no Aiven não são sincronizadas automaticamente com o banco local.

Os testes de integração continuam usando o banco local e um schema descartável, independentemente de `database.local.php`. Para voltar o portal ao PostgreSQL local, remova ou renomeie esse arquivo privado, ou defina todas as variáveis de conexão no ambiente do servidor PHP.

O Aiven hospeda somente o banco: o site PHP e os uploads continuam no servidor onde o portal está instalado. O plano e os créditos de teste devem ser acompanhados no console do Aiven.

### Publicação gratuita no Render

O `Dockerfile` executa PHP 8.3 com Apache e as extensões PostgreSQL/mbstring. O `render.yaml` descreve um serviço gratuito. Crie um **Web Service / Docker** a partir deste repositório, selecione **Free**, configure as variáveis `DB_*` com os dados do Aiven e adicione o certificado como arquivo secreto `aiven-ca.pem` (disponível em `/etc/secrets/aiven-ca.pem`). Nunca envie `database.local.php`, backups ou senhas ao repositório.

Use `DB_SSLMODE=verify-full`, `DB_SSLROOTCERT=/etc/secrets/aiven-ca.pem`, `APP_HTTPS=1`, `UPLOAD_STORAGE=database` e `PORT=80`. A verificação de saúde é `/public/index.php`. O HTTPS externo é gerenciado pelo Render; `APP_HTTPS` garante cookies de sessão seguros atrás desse proxy.

O script de inicialização copia somente o certificado CA público para `/var/run/portal-si/database-ca.pem`, legível pelo usuário do Apache. A senha permanece na variável privada do serviço e a conexão continua usando `verify-full`.

Na inicialização, `database/migrate.php` cria, se necessário, somente a tabela adicional `portal_imagens`. Não recria o schema nem importa o seed. Com `UPLOAD_STORAGE=database`, as novas capas são armazenadas no PostgreSQL, até 2 MB cada, e servidas por `public/imagem.php`; assim persistem quando o container reinicia. Sem essa variável, o envio local de arquivos continua funcionando. Capas antigas em disco precisam ser migradas separadamente, caso existam publicações que as utilizem.

O serviço gratuito pode suspender após 15 minutos sem acessos e demora para iniciar novamente. As sessões de login podem expirar após reinícios. O banco Aiven tem cobrança/plano independente do Render. Essa configuração destina-se à demonstração do projeto.

Para testar os dois modos de armazenamento, execute a integração normalmente e depois com `$env:UPLOAD_STORAGE='database'`. Ambos usam schemas locais descartáveis.

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
