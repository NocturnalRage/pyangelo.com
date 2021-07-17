CREATE TABLE stripe_event (
  event_id varchar(255) COLLATE utf8_bin NOT NULL,
  api_version varchar(30) NOT NULL,
  created_at datetime NOT NULL,
  event_type varchar(255) NOT NULL,
  PRIMARY KEY (event_id)
) COMMENT='Stripe events to ensure they are not processed twice.';

insert into db_change
values (
  41,
  'Create the stripe_event table.',
  '0041_create_stripe_event_table.sql',
  now()
);
