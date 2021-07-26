drop table stripe_subscription_payment;
drop table stripe_subscription;
drop table membership_plan;


CREATE TABLE stripe_product (
  stripe_product_id varchar(255) NOT NULL,
  product_name varchar(30) NOT NULL,
  product_description varchar(100) NOT NULL,
  active boolean NOT NULL DEFAULT TRUE,
  PRIMARY KEY (stripe_product_id)
) COMMENT='Available products on the PyAngelo website';

insert into stripe_product values (
'prod_JsEGvVoOxQZkC7',
'PyAngelo Premium Membership',
'Provides full access to every tutorial on the PyAngelo website.',
TRUE
);

CREATE TABLE stripe_price (
  stripe_price_id varchar(255) NOT NULL,
  stripe_product_id varchar(255) NOT NULL,
  currency_code varchar(3) NOT NULL,
  price_in_cents int unsigned NOT NULL,
  billing_period varchar(5) NOT NULL,
  active boolean NOT NULL DEFAULT TRUE,
  PRIMARY KEY (stripe_price_id),
  FOREIGN KEY (stripe_product_id) REFERENCES stripe_product(stripe_product_id),
  FOREIGN KEY (currency_code) REFERENCES currency(currency_code)
) COMMENT='Prices in different currencies for our products';

insert into stripe_price values (
'price_1JEUrpAkvBrl8hmb6AaEIRZN',
'prod_JsEGvVoOxQZkC7',
'AUD',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEUykAkvBrl8hmb9DaF6Crr',
'prod_JsEGvVoOxQZkC7',
'BBD',
1395,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEUzXAkvBrl8hmb7URIzqLF',
'prod_JsEGvVoOxQZkC7',
'BND',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV0GAkvBrl8hmbZbKRPWxy',
'prod_JsEGvVoOxQZkC7',
'BRL',
2195,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV12AkvBrl8hmbhgCGzTrn',
'prod_JsEGvVoOxQZkC7',
'CAD',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV21AkvBrl8hmb9DPQWv2S',
'prod_JsEGvVoOxQZkC7',
'CHF',
750,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV2uAkvBrl8hmbh0Bvr3g9',
'prod_JsEGvVoOxQZkC7',
'CNY',
4495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV3lAkvBrl8hmbKwmEiVMU',
'prod_JsEGvVoOxQZkC7',
'CZK',
15000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV4bAkvBrl8hmbROODOI1u',
'prod_JsEGvVoOxQZkC7',
'DKK',
4995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV5UAkvBrl8hmbYYi9hn2C',
'prod_JsEGvVoOxQZkC7',
'EGP',
13000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV6BAkvBrl8hmbjh1FHBK3',
'prod_JsEGvVoOxQZkC7',
'EUR',
695,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV6tAkvBrl8hmbk7qNejsL',
'prod_JsEGvVoOxQZkC7',
'FJD',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV7mAkvBrl8hmbWHjithHM',
'prod_JsEGvVoOxQZkC7',
'GBP',
595,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV8SAkvBrl8hmbBwmcyWCy',
'prod_JsEGvVoOxQZkC7',
'HKD',
5495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEV9KAkvBrl8hmbpLuPzUlx',
'prod_JsEGvVoOxQZkC7',
'HUF',
200000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVASAkvBrl8hmbDPdk655L',
'prod_JsEGvVoOxQZkC7',
'IDR',
9000000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVBJAkvBrl8hmbZMTiXd7B',
'prod_JsEGvVoOxQZkC7',
'ILS',
2495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVBzAkvBrl8hmbMFpVZweg',
'prod_JsEGvVoOxQZkC7',
'INR',
40000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVDAAkvBrl8hmb3TiaAtdh',
'prod_JsEGvVoOxQZkC7',
'JMD',
90000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVE9AkvBrl8hmbBda1a70e',
'prod_JsEGvVoOxQZkC7',
'JPY',
800,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVF1AkvBrl8hmbaG0c717s',
'prod_JsEGvVoOxQZkC7',
'LKR',
100000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVFXAkvBrl8hmbCr1WVEhQ',
'prod_JsEGvVoOxQZkC7',
'MXN',
12000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVG1AkvBrl8hmbF1dqReYf',
'prod_JsEGvVoOxQZkC7',
'MYR',
2495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVGkAkvBrl8hmbNuwFLlcs',
'prod_JsEGvVoOxQZkC7',
'NGN',
200000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVHVAkvBrl8hmbNVMp4QvV',
'prod_JsEGvVoOxQZkC7',
'NOK',
5995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVI3AkvBrl8hmbt1xPJ0z6',
'prod_JsEGvVoOxQZkC7',
'NZD',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVIdAkvBrl8hmbuExpg0nX',
'prod_JsEGvVoOxQZkC7',
'PHP',
30000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVJIAkvBrl8hmbqCz4IzLV',
'prod_JsEGvVoOxQZkC7',
'PLN',
2495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVK5AkvBrl8hmbcUDk8Yam',
'prod_JsEGvVoOxQZkC7',
'QAR',
2495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVKmAkvBrl8hmbXzwlnVvl',
'prod_JsEGvVoOxQZkC7',
'RUB',
40000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVLYAkvBrl8hmbnNkPg6fU',
'prod_JsEGvVoOxQZkC7',
'SEK',
6495,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVMXAkvBrl8hmbmwuoZb7M',
'prod_JsEGvVoOxQZkC7',
'SGD',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVNCAkvBrl8hmbYluDHLS0',
'prod_JsEGvVoOxQZkC7',
'THB',
22000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVNpAkvBrl8hmbOfK0xSY0',
'prod_JsEGvVoOxQZkC7',
'TWD',
20000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVOgAkvBrl8hmbWeEvzcC3',
'prod_JsEGvVoOxQZkC7',
'USD',
695,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVPLAkvBrl8hmb9NeI9V5T',
'prod_JsEGvVoOxQZkC7',
'VND',
140000,
'month',
TRUE
);
insert into stripe_price values (
'price_1JEVQtAkvBrl8hmbikorches',
'prod_JsEGvVoOxQZkC7',
'ZAR',
8995,
'month',
TRUE
);

CREATE TABLE stripe_subscription (
  subscription_id varchar(255) COLLATE utf8_bin NOT NULL,
  person_id int unsigned NOT NULL,
  cancel_at_period_end tinyint unsigned NOT NULL,
  canceled_at datetime,
  current_period_start datetime NOT NULL,
  current_period_end datetime NOT NULL,
  stripe_customer_id varchar(255) COLLATE utf8_bin NOT NULL,
  stripe_price_id varchar(255) NOT NULL,
  stripe_client_secret varchar(255) NOT NULL,
  start_date datetime NOT NULL,
  status varchar(30) NOT NULL,
  percent_off tinyint unsigned NOT NULL DEFAULT 0,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (subscription_id),
  KEY (stripe_customer_id),
  KEY (person_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (stripe_price_id) REFERENCES stripe_price(stripe_price_id)
) COMMENT='Subscription details of every premium member.';

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
  44,
  'Add product and prices tables for Stripe.',
  '0044_stripe_produts_and_prices.sql',
  now()
);
