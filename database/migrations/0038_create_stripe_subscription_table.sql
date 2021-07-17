CREATE TABLE stripe_subscription (
  subscription_id varchar(255) COLLATE utf8_bin NOT NULL,
  person_id int unsigned NOT NULL,
  cancel_at_period_end tinyint unsigned NOT NULL,
  canceled_at datetime,
  current_period_start datetime NOT NULL,
  current_period_end datetime NOT NULL,
  stripe_customer_id varchar(255) COLLATE utf8_bin NOT NULL,
  stripe_plan_id varchar(20) NOT NULL,
  start datetime NOT NULL,
  status varchar(30) NOT NULL,
  percent_off tinyint unsigned NOT NULL DEFAULT 0,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (subscription_id),
  UNIQUE KEY (stripe_customer_id),
  KEY (person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (stripe_plan_id) REFERENCES membership_plan(stripe_plan_id)
) COMMENT='Subscription details of every premium member.';

insert into db_change
values (
  38,
  'Create the stripe_subscription table.',
  '0038_create_stripe_subscription_table.sql',
  now()
);
