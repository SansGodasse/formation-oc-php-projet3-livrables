-- Création de la base de données des visiteurs préinscrits

DROP DATABASE IF EXISTS oc_projet3;

CREATE DATABASE oc_projet3 CHARACTER SET 'utf8';

USE oc_projet3;

CREATE TABLE ffpa_visitor(
    v_email VARCHAR(100) NOT NULL,
    v_name VARCHAR(100) NOT NULL,
    v_registration_date DATETIME NOT NULL,
    PRIMARY KEY (v_email)
)
ENGINE = InnoDB;

CREATE TABLE ffpa_projection(
    p_date DATE,
    PRIMARY KEY (p_date)
)
ENGINE = InnoDB;

CREATE TABLE ffpa_visitor_projection(
    vp_visitor_email VARCHAR(100),
    vp_projection_date DATE,
    vp_number SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (vp_visitor_email, vp_projection_date),
    CONSTRAINT fk_vp_visitor_email_v_email
        FOREIGN KEY (vp_visitor_email) REFERENCES ffpa_visitor(v_email)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT fk_vp_projection_date_p_date
        FOREIGN KEY (vp_projection_date) REFERENCES ffpa_projection(p_date)
            ON DELETE CASCADE
            ON UPDATE CASCADE
)
ENGINE = InnoDB;

-- Insertion des dates du festival du 5 au 8 août 2019
INSERT INTO ffpa_projection
VALUES ('2019-08-05');

INSERT INTO ffpa_projection
VALUES ('2019-08-06');

INSERT INTO ffpa_projection
VALUES ('2019-08-07');

INSERT INTO ffpa_projection
VALUES ('2019-08-08');

-- Table pour l'administration
CREATE TABLE ffpa_admin(
    a_email VARCHAR(50)
)
ENGINE = InnoDB;

-- Insertion de l'adresse email par défaut
INSERT INTO ffpa_admin
VALUES ('nicolas.renvoise.dev@gmail.com');

-- Table pour mes messages de débuggage
CREATE TABLE ffpa_log(
    log_id INT UNSIGNED,
    log_content TEXT,
    PRIMARY KEY (log_id)
)
ENGINE = InnoDB;