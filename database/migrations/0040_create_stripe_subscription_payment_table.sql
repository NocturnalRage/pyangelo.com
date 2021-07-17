CREATE TABLE stripe_payment_type (
  payment_type_id tinyint unsigned NOT NULL,
  payment_type_name varchar(10) NOT NULL,
  PRIMARY KEY (payment_type_id)
) COMMENT='Indiciates if this is a payment or a refund.';

INSERT INTO stripe_payment_type values (1, 'Payment');
INSERT INTO stripe_payment_type values (2, 'Refund');

CREATE TABLE stripe_subscription_payment (
  subscription_id varchar(255) COLLATE utf8_bin NOT NULL,
  payment_type_id tinyint unsigned NOT NULL,
  currency_code varchar(3) NOT NULL,
  total_amount_in_cents int NOT NULL,
  paid_at datetime NOT NULL,
  stripe_fee_aud_in_cents int NOT NULL,
  tax_fee_aud_in_cents int NOT NULL,
  net_aud_in_cents int NOT NULL,
  charge_id varchar(255) COLLATE utf8_bin NOT NULL,
  original_charge_id varchar(255) COLLATE utf8_bin NULL,
  refund_status varchar(10) NULL,
  PRIMARY KEY (subscription_id, payment_type_id, paid_at),
  FOREIGN KEY (subscription_id) REFERENCES stripe_subscription(subscription_id),
  FOREIGN KEY (payment_type_id) REFERENCES stripe_payment_type(payment_type_id),
  FOREIGN KEY (currency_code) REFERENCES currency(currency_code)
) COMMENT='Subscription payments and refunds.';

insert into db_change
values (
  40,
  'Create the stripe_subscription_payment table.',
  '0040_create_stripe_subscription_payment_table.sql',
  now()
);
