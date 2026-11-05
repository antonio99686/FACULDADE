# FACULDADE — Sistema Acadêmico PHP

## Requisitos
- WampServer com PHP 8.1 ou superior e extensão PDO MySQL habilitada.
- MySQL 8.0.16 ou superior (para CHECK / colunas geradas).
- Apache com mod_authz_core e .htaccess habilitado.

## Instalação no Windows
1. Copie a pasta `faculdade_php` para `C:\wamp64\www\faculdade_php`.
2. No phpMyAdmin, execute o arquivo `database/faculdade.sql` (script original da atividade DDL 2).
3. Abra `config.php` e confira host, porta, banco, usuário, senha e `base_url`.
4. Acesse `http://localhost/faculdade_php/setup.php` e crie seu administrador (senha mínima de 10 caracteres).
5. Entre em `http://localhost/faculdade_php/`.

O `setup.php` cria apenas a tabela adicional `sistema_usuario`, para login, sem mexer nas 11 tabelas originais.
Não há credenciais administrativas predefinidas.

## Funcionalidades
- Login com hash de senha e sessão protegida; instalação de administrador no primeiro uso.
- Dashboard com indicadores, média das notas e inscrições ativas por curso.
- CRUD dos 11 conjuntos de dados originais: alunos, telefones, cursos, disciplinas, grade curricular, professores, áreas, inscrições, ofertas, matrículas/notas, auxílios.
- Seleção de chaves estrangeiras em formulários, validação básica, proteção CSRF, PDO preparado.
- Consulta, pesquisa e paginação em tabelas de cadastro, exportação CSV.
- Ficha individual do aluno com histórico, boletim, telefones e auxílios; impressão.
- Relatórios com quantidades, titulação, situação acadêmica, ofertas e médias por disciplina.

## Fluxo recomendado
1. Cadastre cursos, disciplinas e professores.
2. Vincule as disciplinas aos cursos em **Grade curricular**.
3. Cadastre professores e suas áreas de atuação.
4. Cadastre alunos e telefones.
5. Crie a inscrição de um aluno em um curso (apenas uma ativa por aluno conforme o script do exercício).
6. Crie ofertas de disciplinas para um curso, ano, semestre e professor.
7. Matricule alunos nas ofertas compatíveis com suas inscrições e registre as notas.
8. Consulte relatórios e fichas individuais.

## Regras importantes
- A média é gerada pelo MySQL com a fórmula `(nota1 + nota2)/2` quando ambas são informadas.
- O aluno pode repetir disciplinas em **outra** oferta, mas não duas vezes na mesma execução.
- Um único auxílio por aluno é garantido pelo `UNIQUE` do banco.
- A aplicação bloqueia a matrícula em uma oferta sem inscrição ativa no curso correspondente.
- Não é possível excluir um registro com filhos relacionados protegidos por `FOREIGN KEY`.
- O banco aceita CPF com 11 dígitos, mas não verifica os dígitos verificadores.
- A aplicação não implementa portal público do aluno, recuperação de senha, controle fino de perfis nem auditoria.

## Segurança e publicação
- Este é um projeto acadêmico para execução local no Wamp. Antes de publicar, configure HTTPS, política de senhas, limitação de tentativas de login, backup, logs, proteção de arquivo de configuração na hospedagem e revisão das regras de autorização.
- Não exponha os arquivos de banco e configuração na web. Use DocumentRoot dedicado e variáveis de ambiente para credenciais em produção.
