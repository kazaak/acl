-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_aco
--

CREATE TABLE tusk_aco (
  id NUMBER NOT NULL PRIMARY KEY,
  collection_id NUMBER NOT NULL,
  path VARCHAR2(20) NOT NULL
);

CREATE SEQUENCE tusk_seq_aco_id START WITH 1 INCREMENT BY 1;
CREATE INDEX tusk_idx_aco_collection_id ON tusk_aco(collection_id);
CREATE INDEX tusk_idx_aco_path ON tusk_aco(path);

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_aco_collection
--

CREATE TABLE tusk_aco_collection (
  id NUMBER NOT NULL PRIMARY KEY,
  alias VARCHAR2(20) NOT NULL,
  model VARCHAR2(15) NOT NULL,
  foreign_key NUMBER NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);
CREATE SEQUENCE tusk_seq_aco_collection_id START WITH 1 INCREMENT BY 1;
CREATE INDEX tusk_idx_aco_collection_alias ON tusk_aco_collection(alias);

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_action
--

CREATE TABLE tusk_action (
  id NUMBER NOT NULL PRIMARY KEY,
  name VARCHAR2(15) NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);
-- NOTE start with 10; we have hardcoded values below from inserts
CREATE SEQUENCE tusk_seq_action_id START WITH 10 INCREMENT BY 1;
CREATE INDEX tusk_idx_action_name ON tusk_action(name);

-- --------------------------------------------------------
--
-- Daten für Tabelle tusk_action
--
INSERT ALL 
    INTO tusk_action(id, name) VALUES (5, 'create')
    INTO tusk_action(id, name) VALUES (6, 'read')
    INTO tusk_action(id, name) VALUES (7, 'update')
    INTO tusk_action(id, name) VALUES (8, 'delete')
    INTO tusk_action(id, name) VALUES (9, 'grant')
SELECT * FROM dual;

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_aro
--
CREATE TABLE tusk_aro (
  id NUMBER NOT NULL PRIMARY KEY,
  collection_id NUMBER NOT NULL,
  path VARCHAR2(20) NOT NULL
);

CREATE SEQUENCE tusk_seq_aro_id START WITH 1 INCREMENT BY 1;
CREATE INDEX tusk_idx_aro_collection_id ON tusk_aro(collection_id);
CREATE INDEX tusk_idx_aro_path ON tusk_aro(path);

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_aro_collection
--
CREATE TABLE tusk_aro_collection (
  id NUMBER NOT NULL PRIMARY KEY,
  alias VARCHAR2(20) NOT NULL,
  model VARCHAR2(15) NOT NULL,
  foreign_key NUMBER NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);
CREATE SEQUENCE tusk_seq_aro_collection_id START WITH 1 INCREMENT BY 1;
CREATE INDEX tusk_idx_aro_collection_alias ON tusk_aro_collection(alias);

-- --------------------------------------------------------
--
-- Tabellenstruktur für Tabelle tusk_permission
--
CREATE TABLE tusk_permission (
  id NUMBER PRIMARY KEY,
  aco_id NUMBER NOT NULL,
  aro_id NUMBER NOT NULL,
  aco_path VARCHAR2(11) NOT NULL,
  aro_path VARCHAR2(11) NOT NULL,
  action_id NUMBER NOT NULL,
  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
);

CREATE SEQUENCE tusk_seq_permission_id START WITH 1 INCREMENT BY 1;
CREATE INDEX tusk_idx_permission_aco_id ON tusk_permission(aco_id,aro_id,aco_path,aro_path);
CREATE INDEX tusk_idx_permission_action_id ON tusk_permission(action_id);
CREATE INDEX tusk_idx_permission_created ON tusk_permission(created);
