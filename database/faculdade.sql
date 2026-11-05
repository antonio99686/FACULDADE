-- ATIVIDADE DDL 2 - BANCO DE DADOS FACULDADE
-- Solucao para MySQL 8.0.16+ (CHECK aplicado)
-- Execute este script no phpMyAdmin ou MySQL Workbench.

CREATE DATABASE IF NOT EXISTS faculdade
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE faculdade;

CREATE TABLE aluno (
  id_aluno INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  matricula VARCHAR(30) NOT NULL UNIQUE,
  nome VARCHAR(150) NOT NULL,
  cpf CHAR(11) NOT NULL UNIQUE,
  data_nascimento DATE,
  sexo VARCHAR(30),
  status_aluno ENUM('matrícula ativa', 'evadido', 'egresso')
    NOT NULL DEFAULT 'matrícula ativa'
) ENGINE=InnoDB;

CREATE TABLE telefone_aluno (
  id_telefone INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_aluno INT UNSIGNED NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  CONSTRAINT uq_aluno_telefone UNIQUE (id_aluno, telefone),
  CONSTRAINT fk_telefone_aluno FOREIGN KEY (id_aluno)
    REFERENCES aluno(id_aluno) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE curso (
  id_curso INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  carga_horaria_total INT UNSIGNED NOT NULL,
  perfil_profissional TEXT NOT NULL,
  CONSTRAINT ck_carga_curso CHECK (carga_horaria_total > 0)
) ENGINE=InnoDB;

CREATE TABLE disciplina (
  id_disciplina INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  carga_horaria_semanal INT UNSIGNED NOT NULL,
  ementa TEXT NOT NULL,
  CONSTRAINT ck_carga_disciplina CHECK (carga_horaria_semanal > 0)
) ENGINE=InnoDB;

CREATE TABLE curso_disciplina (
  id_curso INT UNSIGNED NOT NULL,
  id_disciplina INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_curso, id_disciplina),
  CONSTRAINT fk_cd_curso FOREIGN KEY (id_curso)
    REFERENCES curso(id_curso),
  CONSTRAINT fk_cd_disciplina FOREIGN KEY (id_disciplina)
    REFERENCES disciplina(id_disciplina)
) ENGINE=InnoDB;

CREATE TABLE professor (
  id_professor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  titulacao ENUM('graduação', 'especialização', 'mestrado', 'doutorado') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE area_professor (
  id_area INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_professor INT UNSIGNED NOT NULL,
  area_atuacao VARCHAR(150) NOT NULL,
  CONSTRAINT uq_professor_area UNIQUE (id_professor, area_atuacao),
  CONSTRAINT fk_area_professor FOREIGN KEY (id_professor)
    REFERENCES professor(id_professor) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE inscricao (
  numero_inscricao INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_aluno INT UNSIGNED NOT NULL,
  id_curso INT UNSIGNED NOT NULL,
  ano_inicio SMALLINT UNSIGNED NOT NULL,
  ano_conclusao SMALLINT UNSIGNED DEFAULT NULL,
  ativa BOOLEAN NOT NULL DEFAULT TRUE,
  -- UNIQUE permite apenas uma inscrição ativa por aluno.
  -- As inscrições inativas mantêm NULL e podem ser múltiplas.
  aluno_ativo INT UNSIGNED GENERATED ALWAYS AS
    (CASE WHEN ativa = 1 THEN id_aluno ELSE NULL END) STORED,
  UNIQUE KEY uq_inscricao_ativa_por_aluno (aluno_ativo),
  CONSTRAINT fk_inscricao_aluno FOREIGN KEY (id_aluno)
    REFERENCES aluno(id_aluno),
  CONSTRAINT fk_inscricao_curso FOREIGN KEY (id_curso)
    REFERENCES curso(id_curso),
  CONSTRAINT ck_ano_inscricao CHECK
    (ano_conclusao IS NULL OR ano_conclusao >= ano_inicio)
) ENGINE=InnoDB;

CREATE TABLE execucao_disciplina (
  id_execucao INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_disciplina INT UNSIGNED NOT NULL,
  id_curso INT UNSIGNED NOT NULL,
  ano SMALLINT UNSIGNED NOT NULL,
  semestre TINYINT UNSIGNED NOT NULL,
  id_professor INT UNSIGNED NOT NULL,
  CONSTRAINT uq_execucao UNIQUE (id_disciplina, id_curso, ano, semestre),
  CONSTRAINT ck_semestre CHECK (semestre IN (1, 2)),
  CONSTRAINT fk_execucao_curso_disciplina
    FOREIGN KEY (id_curso, id_disciplina)
    REFERENCES curso_disciplina(id_curso, id_disciplina),
  CONSTRAINT fk_execucao_professor FOREIGN KEY (id_professor)
    REFERENCES professor(id_professor)
) ENGINE=InnoDB;

CREATE TABLE matricula_disciplina (
  id_matricula INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_aluno INT UNSIGNED NOT NULL,
  id_execucao INT UNSIGNED NOT NULL,
  nota1 DECIMAL(4,2) DEFAULT NULL,
  nota2 DECIMAL(4,2) DEFAULT NULL,
  media DECIMAL(4,2) GENERATED ALWAYS AS
    (CASE WHEN nota1 IS NULL OR nota2 IS NULL THEN NULL
          ELSE ROUND((nota1 + nota2) / 2, 2) END) STORED,
  CONSTRAINT uq_aluno_execucao UNIQUE (id_aluno, id_execucao),
  CONSTRAINT ck_nota1 CHECK (nota1 IS NULL OR (nota1 BETWEEN 0 AND 10)),
  CONSTRAINT ck_nota2 CHECK (nota2 IS NULL OR (nota2 BETWEEN 0 AND 10)),
  CONSTRAINT fk_matricula_aluno FOREIGN KEY (id_aluno)
    REFERENCES aluno(id_aluno),
  CONSTRAINT fk_matricula_execucao FOREIGN KEY (id_execucao)
    REFERENCES execucao_disciplina(id_execucao)
) ENGINE=InnoDB;

CREATE TABLE auxilio_estudantil (
  id_auxilio INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_aluno INT UNSIGNED NOT NULL UNIQUE,
  nome_programa VARCHAR(150) NOT NULL,
  valor_beneficio DECIMAL(10,2) NOT NULL,
  CONSTRAINT ck_valor_auxilio CHECK (valor_beneficio >= 0),
  CONSTRAINT fk_auxilio_aluno FOREIGN KEY (id_aluno)
    REFERENCES aluno(id_aluno)
) ENGINE=InnoDB;

-- Verifique a criação:
SHOW TABLES;
