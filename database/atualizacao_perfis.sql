-- ATUALIZAÇÃO PARA VERSÃO 2.0 (não apaga dados existentes)
-- Execute cada ALTER uma única vez OU use upgrade.php, que detecta as colunas existentes.
USE faculdade;
ALTER TABLE sistema_usuario
  ADD COLUMN perfil ENUM('admin','professor','aluno') NOT NULL DEFAULT 'admin',
  ADD COLUMN id_aluno INT UNSIGNED NULL UNIQUE,
  ADD COLUMN id_professor INT UNSIGNED NULL UNIQUE,
  ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1;
