<?php

$db = new SQLite3('../db/mochinho.sqlite3');

$sql = null;

if (@$_REQUEST['q'] === 'create') {

  $sql = <<<SQL
CREATE TABLE IF NOT EXISTS usuarios (
  id     INTEGER     NOT NULL PRIMARY KEY,
  nome   VARCHAR(50) NOT NULL,
  email  VARCHAR(50) NOT NULL,
  senha  VARCHAR(32) NOT NULL,
  status INTEGER DEFAULT 0 NOT NULL,
  token  CHAR(36) NULL
);

CREATE TABLE IF NOT EXISTS areas (
  id   INTEGER     NOT NULL PRIMARY KEY,
  nome VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS cursos (
  id              INTEGER     NOT NULL PRIMARY KEY,
  nome            VARCHAR(50) NOT NULL,
  resumo          VARCHAR(100),
  programa        VARCHAR(500),
  vagas           INTEGER     NOT NULL,
  data_inicio     DATE        NOT NULL,
  data_termino    DATE        NOT NULL,
  dias            VARCHAR(28) NOT NULL,
  horario_inicio  TIME        NOT NULL,
  horario_termino TIME        NOT NULL,
  carga_horaria   INTEGER     NOT NULL,
  imagem          BLOB,
  tipo_imagem     VARCHAR(3),
  id_area         INTEGER     NOT NULL REFERENCES areas (id)
);

CREATE TABLE IF NOT EXISTS inscricoes (
  id_usuario INTEGER NOT NULL,
  id_curso   INTEGER NOT NULL,
  concluiu   BOOLEAN DEFAULT FALSE NOT NULL,
  CONSTRAINT inscricao_pk PRIMARY KEY (id_usuario, id_curso)
);
SQL;

}

if (@$_REQUEST['q'] === 'drop') {

  $sql = <<<SQL
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS areas;
DROP TABLE IF EXISTS cursos;
DROP TABLE IF EXISTS inscricoes;
SQL;

}

if (@$_REQUEST['q'] === 'seed') {

  $sql = <<<SQL
INSERT INTO usuarios (nome, email, senha, status)
VALUES ('Seu Madruga', 'madruga@chaves.mx', 'madruga', 1);

INSERT INTO areas (nome)
VALUES ('Artes'), ('Beleza'), ('Comunicação'), ('Informática'), ('Gastronomia'), ('Idiomas'), ('Moda'), ('Saúde');
INSERT INTO cursos (nome, vagas, data_inicio, data_termino, dias, horario_inicio, horario_termino, carga_horaria, id_area)
VALUES ('Petit Gateu Avançado', 15, '2018-10-01', '2018-11-30', 'seg', '19:00', '22:00', 90, (SELECT id FROM areas WHERE nome = 'Gastronomia'));
SQL;

}

if ($sql) $db->exec($sql);
else die('Nenhuma ação foi definida, use ?q=create|drop|seed');

die('OK');
