
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
    updated_at   TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(phone_number)
);
COMMENT ON TABLE phone_number IS 'Every phone number known to this app';

CREATE TABLE contact (
    id          SERIAL PRIMARY KEY,
    person_id   INTEGER NOT NULL REFERENCES person(id) ON DELETE CASCADE ON UPDATE CASCADE,
    name        VARCHAR(128) NULL,
    phone_id    INTEGER NULL REFERENCES phone_number(id) ON DELETE CASCADE ON UPDATE CASCADE,
    description TEXT NULL,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- A single person can't create multiple contacts for the same phone number. 
    UNIQUE (person_id, phone_id)
);
COMMENT ON TABLE contact IS 'The details for each phone number that has interacted with a Person.';
COMMENT ON COLUMN contact.person_id IS 'The Person to whom this contact belongs.';
COMMENT ON COLUMN contact.phone_id IS 'The ID of the phone_number associated with this Contact.';
COMMENT ON COLUMN contact.name IS 'The name for this contact, defined by the Person to whom this contact belongs.';
COMMENT ON COLUMN contact.description IS 'A description or notes about this Contact.';

CREATE TYPE MESSAGE_STATUS AS ENUM (
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
COMMENT ON TYPE MESSAGE_STATUS IS 'The delivery status of a text message from Twilio';

CREATE TYPE MESSAGE_DIRECTION AS ENUM (
    'inbound', 'outbound'
);
COMMENT ON TYPE MESSAGE_DIRECTION IS 'If a message was inbound to, or outbound from, this app.';

CREATE TABLE message (
    id         SERIAL PRIMARY KEY,
    sid        VARCHAR(128) NULL,
    person_id  INTEGER NOT NULL REFERENCES person(id) ON DELETE CASCADE ON UPDATE CASCADE,
    contact_id INTEGER NOT NULL REFERENCES contact(id) ON DELETE CASCADE ON UPDATE CASCADE,
    body       TEXT NULL,
    direction  MESSAGE_DIRECTION NOT NULL,
    status     MESSAGE_STATUS NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(sid)
);  
COMMENT ON TABLE message IS 'Each message sent or received through this application.';
COMMENT ON COLUMN message.sid IS 'The Twilio SID which uniquely identifies each message.';
COMMENT ON COLUMN message.person_id IS 'The Person using this app who sent or received this message.';
COMMENT ON COLUMN message.contact_id IS 'The Contact to whom the message was sent, or from whom the message was received.';
COMMENT ON COLUMN message.status IS 'The status of the message received from Twilio.';
COMMENT ON COLUMN message.direction IS 'Whether the message was sent outbound from this app, or received inbound to it.';

CREATE OR REPLACE FUNCTION UTCFMT(TIMESTAMPTZ) RETURNS CHAR 
    AS $$ SELECT TO_CHAR($1 AT TIME ZONE 'UTC', 'YYYY-MM-DD"T"HH24:MI:SS"Z"') $$
    LANGUAGE SQL;

SELECT diesel_manage_updated_at('person');
SELECT diesel_manage_updated_at('phone_number');
SELECT diesel_manage_updated_at('contact');
SELECT diesel_manage_updated_at('message');