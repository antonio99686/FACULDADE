# FACULDADE — Sistema de Gestão Acadêmica v2.0

PHP 8.1+ / MySQL 8.0.16+ / WampServer. Os 11 módulos administrativos da atividade original foram preservados, incluindo a correção de `max()` no dashboard.

## Atualizar uma instalação existente
1. Faça backup da pasta atual e exporte seu banco `faculdade` no phpMyAdmin.
2. Copie os arquivos da nova pasta `faculdade_v2` para a pasta atual do WampServer (ex.: `C:\wamp64\www\FACULDADE`), substituindo os arquivos. **Não importe novamente** `database/faculdade.sql` em banco que já possui dados.
3. Ajuste a chave `base_url` em `config.php` para `http://localhost/FACULDADE` (ou o nome real da pasta).
4. Acesse o site e entre com a conta administrativa antiga.
5. Abra `http://localhost/FACULDADE/upgrade.php` uma única vez. A migração adiciona `perfil`, `id_aluno`, `id_professor` e `ativo` em `sistema_usuario`, mantendo os administradores antigos.
6. Vá à opção **Contas de acesso**. Cadastre um professor/aluno nos módulos normais antes de criar o login correspondente.
7. Use `index.php?p=login` para entrar com qualquer um dos perfis. Professor/aluno é encaminhado automaticamente ao seu próprio portal.

## Nova instalação
1. Importe `database/faculdade.sql` no MySQL.
2. Ajuste `config.php`.
3. Entre em `/setup.php` e cadastre o primeiro administrador.
4. Faça login, execute `/upgrade.php`, crie alunos e professores e depois seus acessos em **Contas de acesso**.

## Permissões
- Administrador: todos os 11 módulos, relatórios, exportação, contas e senhas.
- Professor: consulta apenas turmas a ele atribuídas, estudantes dessas turmas e edita as duas notas; a média é calculada pelo banco.
- Aluno: consulta apenas seus dados, cursos, boletim, média, telefones e auxílio.

## Atenção
- Não há cadastro público automático de aluno/professor. Apenas o administrador cria credenciais e vínculos.
- Ao desativar uma conta, ela deixa de fazer novos logins. Sessões já abertas não são revogadas de imediato neste protótipo.
- `upgrade.php` exige sessão de administrador e pode ser removido após a migração.
- Para publicação em produção: adicionar limitação de tentativas de login, HTTPS, auditoria, políticas de senha, validação de CPF e revisão de privilégios e fluxo de recuperação.
- O banco original não possui frequência nem mensagens. Essas funcionalidades não foram inventadas para evitar modificar o modelo do exercício.
