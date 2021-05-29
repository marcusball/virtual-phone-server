
CREATE TABLE person (
    id         SERIAL PRIMARY KEY,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);

COMMENT ON TABLE person IS 'Represents each real user of this app';

CREATE TABLE phone_number (
    id           SERIAL PRIMARY KEY,
    phone_number VARCHAR(20) NOT NULL,
    created_at   TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE phone_number IS 'Every phone number known to this app';

CREATE TABLE contact (
    id          SERIAL PRIMARY KEY,
    person_id   INTEGER NOT NULL REFERENCES person(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name        VARCHAR(128) NULL,
    phone_id    INTEGER NULL REFERENCES phone_number(id) ON DELETE CASCADE ON UPDATE CASCADE,
    description TEXT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);
COMMENT ON TABLE contact IS 'The details for each phone number that has interacted with a Person.';

CREATE TYPE MESSAGESTATUS AS ENUM (
    'unknown',
    'accepted',
    'queued',
    'sending',
    'sent',
    'receiving',
    'received',
    'delivered',
    'undelivered',
    'failed'
);
COMMENT ON TYPE MESSAGESTATUS IS 'The delivery status of a text message from Twilio';

CREATE TABLE message (
    id         SERIAL PRIMARY KEY,
    person_id  INTEGER NOT NULL REFERENCES person(id) ON DELETE CASCADE ON UPDATE CASCADE,
    contact_id INTEGER NOT NULL REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
    body       TEXT NULL,
    status     MESSAGESTATUS NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP
);  
COMMENT ON TABLE message IS 'Each message sent or received through this application.';

CREATE OR REPLACE FUNCTION UTCFMT(TIMESTAMPTZ) RETURNS CHAR 
    AS $$ SELECT TO_CHAR($1 AT TIME ZONE 'UTC', 'YYYY-MM-DD"T"HH24:MI:SS"Z"') $$
    LANGUAGE SQL;

SELECT diesel_manage_updated_at('person');
SELECT diesel_manage_updated_at('phone_number');
SELECT diesel_manage_updated_at('contact');
SELECT diesel_manage_updated_at('message');