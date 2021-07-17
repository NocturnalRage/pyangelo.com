ALTER table person
ADD COLUMN stripe_customer_id varchar(255) COLLATE utf8_bin AFTER detected_country_code;

ALTER table person
ADD COLUMN last4 varchar(4) AFTER stripe_customer_id;

ALTER table person
ADD COLUMN premium_end_date datetime AFTER last4;

insert into db_change
values (
  42,
  'Add stripe details to the person table.',
  '0042_add_stripe_details_to_person_table.sql',
  now()
);
