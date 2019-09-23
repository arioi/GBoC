-- creation de la bd et super admin --
--CREATE DATABASE GBoC;
--CREATE ROLE super_admin password 'super_admin' login;
--GRANT ALL ON DATABASE GBoC TO super_admin WITH GRANT OPTION;
DROP TABLE messages;
DROP TABLE tasks;
DROP TABLE events;
DROP TABLE commissions;
DROP TABLE volunteers;
DROP TYPE role;

-- Enum --
/*
CREATE TYPE role AS ENUM ('VOLUNTEER', 'MODERATOR', 'ADMIN');
*/
CREATE TABLE ROLE (
  Role ENUM('VOLUNTEER', 'MODERATOR', 'ADMIN'));
INSERT INTO `role`(`Role`) VALUES ('VOLUNTEER');
INSERT INTO `role`(`Role`) VALUES ('MODERATOR');
INSERT INTO `role`(`Role`) VALUES ('ADMIN');
-- Tables --
/*
CREATE TABLE IF NOT EXISTS volunteers(
    id_volunteer		UUID	PRIMARY KEY,
    name_volunteer		TEXT	NOT NULL,
    surname_volunteer   TEXT	NOT NULL,
    birth_date          DATE	NOT NULL,
    number_tel	        TEXT,
    mail                TEXT    UNIQUE NOT NULL,
    password            TEXT    NOT NULL,
    role	            role	NOT NULL DEFAULT 'VOLUNTEER'
);*/

CREATE TABLE IF NOT EXISTS volunteers(
    id_volunteer		BINARY(16)	PRIMARY KEY,
    name_volunteer		TEXT	NOT NULL,
    surname_volunteer   TEXT	NOT NULL,
    birth_date          DATE	NOT NULL,
    number_tel	        TEXT,
    mail                TEXT    NOT NULL,
    password            TEXT    NOT NULL,
    role	            ENUM('VOLUNTEER','MODERATOR','ADMIN') NOT NULL DEFAULT 'VOLUNTEER',
    FOREIGN KEY(role) REFERENCES role(id)
);
/*CREATE TABLE IF NOT EXISTS commissions(
    id_commission		UUID 	PRIMARY KEY,
    name_commission		TEXT	UNIQUE NOT NULL,
    moderators	        UUID[]	NOT NULL,
    volunteers          UUID[],
    volunteers_waiting  UUID[],
    active              BOOLEAN DEFAULT TRUE
);*/

CREATE TABLE IF NOT EXISTS commissions(
    id_commission		BINARY(16) 	PRIMARY KEY,
    name_commission		VARCHAR(100) UNIQUE NOT NULL,
    active              BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS commissions_moderators(
    id_commission BINARY(16) NOT NULL,
    id_moderator BINARY(16) NOT NULL,
    PRIMARY KEY(id_commission,id_moderator),
    FOREIGN KEY(id_moderator) REFERENCES volunteers(id_volunteer),
    FOREIGN KEY(id_commission) REFERENCES commission(id_commission)
);

CREATE TABLE IF NOT EXISTS commissions_volunteers(
    id_commission BINARY(16) NOT NULL,
    id_volunteer BINARY(16) NOT NULL,
    volunteer_activ BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY(id_commission,id_volunteer),
    FOREIGN KEY(id_volunteer) REFERENCES volunteers(id_volunteer),
    FOREIGN KEY(id_commission) REFERENCES commission(id_commission)
);

/*CREATE TABLE IF NOT EXISTS events(
    id_event		    UUID		PRIMARY KEY,
    name_event		    TEXT		NOT NULL,
    info_event	        TEXT,
    begin_time_event    timestamp   NOT NULL,
    end_time_event      timestamp   NOT NULL,
    places_event	    TEXT,
    expected_people   	INTEGER		DEFAULT 10,
    commissions         UUID[]
);*/

CREATE TABLE IF NOT EXISTS events(
    id_event		    BINARY(16)		PRIMARY KEY,
    name_event		    TEXT		NOT NULL,
    info_event	        TEXT,
    begin_datetime_event    datetime   NOT NULL,
    end_datetime_event      datetime   NOT NULL,
    places_event	    TEXT,
    expected_people   	INTEGER		DEFAULT 10
);

CREATE TABLE IF NOT EXISTS event_commission(
    id_event BINARY(16) NOT NULL,
    id_commission BINARY(16) NOT NULL,
    PRIMARY KEY(id_event,id_commission),
    FOREIGN KEY(id_event) REFERENCES events(id_event),
    FOREIGN KEY(id_commission) REFERENCES commissions(id_commission)
);

/*CREATE TABLE IF NOT EXISTS tasks(
    id_task		            UUID	    PRIMARY KEY,
    event	                UUID	    REFERENCES events(id_event) NOT NULL,
    commission	            UUID 	    REFERENCES commissions(id_commission) NOT NULL,
    name_task		        TEXT	    NOT NULL,
    info_task	            TEXT,
    begin_time_task	        timestamp   NOT NULL,
    end_time_task	        timestamp   NOT NULL,
    places_task	            TEXT,
    max_volunteers	        INTEGER	    NOT NULL,
    registered_volunteers   UUID[]
);*/

CREATE TABLE IF NOT EXISTS tasks(
    id_task		            BINARY(16)	    PRIMARY KEY,
    id_event	                BINARY(16)	    /*REFERENCES events(id_event)*/ NOT NULL,
    id_commission	            BINARY(16) 	    /*REFERENCES commissions(id_commission)*/ NOT NULL,
    name_task		        TEXT	    NOT NULL,
    info_task	            TEXT,
    begin_datetime_task	    datetime   NOT NULL,
    end_datetime_task	    datetime   NOT NULL,
    places_task	            TEXT,
    max_volunteers	        INTEGER	    NOT NULL,
    FOREIGN KEY(id_event) REFERENCES events(id_event),
    FOREIGN KEY(id_commission) REFERENCES commissions(id_commission)
);

CREATE TABLE IF NOT EXISTS task_volunteer(
    id_task BINARY(16) NOT NULL,
    id_volunteer BINARY(16) NOT NULL,
    PRIMARY KEY(id_task, id_volunteer),
    FOREIGN KEY(id_task) REFERENCES tasks(id_task),
    FOREIGN KEY(id_volunteer) REFERENCES volunteers(id_volunteer)
);

/*CREATE TABLE IF NOT EXISTS messages(
    id_message      UUID        PRIMARY KEY,
    messenger       UUID        REFERENCES volunteers(id_volunteer) NOT NULL,
    recipient       UUID        REFERENCES tasks(id_task) NOT NULL,
    time_message    timestamp   NOT NULL,
    message         TEXT        NOT NULL
);*/

CREATE TABLE IF NOT EXISTS messages(
    id_message      BINARY(16)        PRIMARY KEY,
    messenger       BINARY(16)        NOT NULL,
    recipient       BINARY(16)        NOT NULL,
    datetime_message    datetime   NOT NULL,
    message         TEXT        NOT NULL,
    FOREIGN KEY(messenger) REFERENCES volunteers(id_volunteer),
    FOREIGN KEY(recipient) REFERENCES tasks(id_task)
);
