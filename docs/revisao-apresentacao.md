# Revisão para a apresentação de 12/11/2026

Revisão realizada em 11/11/2026. O fluxo principal do portal foi corrigido e as telas receberam uma identidade visual consistente.

## Panorama do código

O projeto utiliza PHP renderizado no servidor, PostgreSQL via PDO, sessões e CSS responsivo. Há separação entre modelos, helpers e views, mas não existe um roteador MVC central: várias páginas administrativas processam a requisição antes de renderizar o HTML.

A principal fragilidade encontrada era a falta de integração entre partes já existentes. A estrutura do banco estava mais completa que os formulários. O caminho público estava vazio, os redirecionamentos divergiam dos nomes reais dos arquivos e parte do CRUD usava colunas de outro modelo de dados.

## Problemas corrigidos

| Área | Problema encontrado | Resultado |
|---|---|---|
| Entrada | `public/index.php` vazio | Portal carrega publicações reais; entrada da raiz redireciona |
| Navegação pública | Links de categoria e leitura apontavam para páginas ausentes | Categorias, leitura e retorno à página inicial implementados |
| Cadastro de conta | Formulário e link apontavam para `cadastro.php`, que não existe | Caminho correto, validação no servidor e preservação dos dados |
| Modelo de usuário | Código PHP com entidades HTML, causando erro de sintaxe | Modelo válido, consultas parametrizadas e hash seguro |
| Autenticação | Controlador vazio, conexão imprimia conteúdo antes de redirects | Autenticação centralizada e conexão silenciosa |
| Publicações | Cadastro omitindo slug obrigatório e sem publicação efetiva | Slug único, resumo, situação e data de publicação |
| Edição | Colunas inexistentes: imagem, texto, id_categoria e video_url | Campos compatíveis com o schema PostgreSQL |
| Layout de gestão | Cadastro/listagem sem documento HTML completo; header ausente | Cabeçalho, navegação, formulários, tabela e rodapé compartilhados |
| Exclusão | Conexão ausente, colunas incorretas e remoção por GET | Confirmação visual, POST e validação de sessão/permissão/CSRF |
| Upload | Capa anterior poderia ser apagada antes de salvar a edição | Gravação primeiro; remoção posterior; limpeza de novo arquivo em caso de falha |
| Segurança de formulários | Ausência de CSRF e permissões inconsistentes | Token em formulários, controle por perfil e autoria |
| Logout | Sessão não era destruída completamente | Sessão e cookie encerrados; ação por POST |
| Dados de exemplo | Hashes fictícios não autenticavam | Seed válido para instalações novas, sem mudar contas existentes |
| Responsividade | Tabela fazia a página inteira ultrapassar a largura móvel | Rolagem limitada à tabela, com página ajustada à tela |
| Apache | Arquivo `.htacess` vazio e nome incorreto | `.htaccess` funcional e bloqueio de recursos internos |

## Estilo e usabilidade

- Painel com navegação lateral, indicadores reais e atividade recente.
- Login e cadastro com apresentação institucional e campos consistentes.
- Editor dividido em conteúdo, organização e mídia.
- Listagem com busca, filtro de situação, identificação de autor e ações.
- Mensagens de sucesso, validação, estados vazios e confirmação de exclusão.
- Categorias e leitura com cabeçalho e rodapé comuns.
- Foco visível, rótulos associados, link para pular ao conteúdo e redução de movimento.
- Layouts adaptados a telas pequenas, sem dependência de fontes/CDNs externas.

## Validação

- **32 arquivos PHP** verificados, sem falhas de sintaxe.
- **64 verificações de integração HTTP**, usando PHP do XAMPP e PostgreSQL real em schema isolado.
- Cobertura: cadastro, e-mail duplicado, senhas inválidas, login, contas inativas, sessões, proteção de rotas, CSRF, perfis, autoria, CRUD, filtros, publicação/rascunho, upload válido/inválido, troca de imagem, exclusão, escape HTML, links públicos e estilos.
- **30 verificações de largura no navegador**: dez telas em três tamanhos solicitados (360, 768 e 1366 px). As medidas reais do navegador, ajustadas pelo zoom, estão em `tests/artifacts/responsividade.json`.
- Nenhuma dessas 30 combinações apresentou rolagem horizontal na página; a tabela tem sua própria rolagem.
- Console do navegador sem avisos ou erros durante a conferência.
- Apache/XAMPP: portal, login e estilos retornaram HTTP 200; cinco caminhos internos verificados retornaram HTTP 403, como esperado.
- As três folhas de estilo ativas passaram na verificação de formatação do Prettier.
- Evidências locais em `tests/artifacts`, ignoradas pelo Git.
- A conferência funcional autenticada utilizou contas descartáveis do seed, não a senha pessoal do usuário.
- Os dados e as credenciais do banco original foram preservados.

## Roteiro de apresentação (5–7 minutos)

1. **Objetivo — 30 s.** Abra a página inicial e explique que o portal reúne projetos, pesquisas e ações do curso.
2. **Consulta pública — 1 min.** Entre em Extensão, abra uma publicação e mostre autor, data e texto.
3. **Acesso — 30 s.** Entre com sua conta administrativa já existente.
4. **Painel — 30 s.** Apresente indicadores e atividade recente.
5. **Criação — 2 min.** Crie um conteúdo de demonstração com título, categoria, resumo, texto e uma imagem pequena. Selecione Publicado e salve.
6. **Resultado — 30 s.** Abra o portal e mostre a nova publicação.
7. **Gestão — 1 min.** Pesquise pelo título, edite o texto e salve. Mostre a confirmação de exclusão; pode cancelar para manter o material.
8. **Encerramento — 30 s.** Explique o fluxo aluno → revisão → editor e encerre a sessão.

Sugestão de conteúdo para digitar:
- Título: Tecnologia que aproxima a comunidade
- Categoria: Extensão
- Resumo: Estudantes de Sistemas de Informação conectam conhecimento acadêmico a desafios reais da comunidade.
- Texto: Nosso projeto reúne estudantes, professores e organizações locais para desenvolver soluções digitais. O portal permite registrar as iniciativas e compartilhar seus resultados.

## Pontos para o ensaio

- Ligue Apache e PostgreSQL antes de abrir o portal.
- Use sua conta existente com perfil administrador ou editor para publicar. Contas novas são alunos.
- Foi identificado pelo menos um administrador ativo com hash de senha válido no banco atual. Algumas contas antigas ainda têm hashes fictícios; nenhuma senha existente foi substituída.
- As senhas documentadas no seed são para uma instalação nova; não presumir que elas funcionam nas contas antigas.
- Separe uma imagem JPG/PNG/GIF de até 2 MB.
- A página funciona sem CDN; a abertura de um vídeo no YouTube depende de internet.
- Não reaplique o schema no banco existente.
- As tabelas de eventos, tags, mídias e auditoria representam estrutura disponível para evolução; não apresentar módulos administrativos dessas entidades como já implementados.
- O corpo da publicação aceita texto simples, não um editor de HTML rico.
- Testes locais e revisão visual não substituem testes de carga ou uma auditoria completa para publicação na internet.
