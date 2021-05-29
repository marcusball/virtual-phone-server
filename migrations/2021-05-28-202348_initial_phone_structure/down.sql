-- This file should undo anything in `up.sql`

DROP TABLE IF EXISTS message;
DROP TABLE IF EXISTS contact;
DROP TABLE IF EXISTS phone_number;
DROP TABLE IF EXISTS person;

DROP TYPE IF EXISTS MESSAGESTATUS;