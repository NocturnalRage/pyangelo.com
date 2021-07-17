CREATE TABLE membership_plan (
  stripe_plan_id varchar(20) NOT NULL,
  display_plan_name varchar(20) NOT NULL,
  currency_code varchar(3) NOT NULL,
  price_in_cents int unsigned NOT NULL,
  billing_period_in_months smallint unsigned NOT NULL,
  active boolean NOT NULL DEFAULT TRUE,
  PRIMARY KEY (stripe_plan_id),
  KEY (currency_code)
) COMMENT='Available membership plans detailing their price and billing period';

insert into membership_plan values ('Premium_AUD', 'Premium', 'AUD', 999, 1, TRUE);
insert into membership_plan values ('Premium_USD', 'Premium', 'USD', 899, 1, TRUE);

insert into db_change
values (
  37,
  'Create the membership_plan table.',
  '0037_create_membership_plan_table.sql',
  now()
);
