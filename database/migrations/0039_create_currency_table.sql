CREATE TABLE currency (
  currency_code varchar(3) NOT NULL,
  currency_description varchar(30) NOT NULL,
  currency_symbol varchar(10) NOT NULL,
  stripe_divisor smallint NOT NULL,
  PRIMARY KEY (currency_code)
) COMMENT='Currencies that the PyAngelo website supports.';

INSERT INTO currency VALUES
('AUD','Australian Dollars','$',100),
('CAD','Canadian Dollars','$',100),
('CHF','Swiss Francs','CHF',100),
('CZK','Czech Republic Korunas','Kč ',100),
('DKK','Danish Kroner','kr ',100),
('EUR','Euros','&euro;',100),
('GBP','British Pounds','&pound;',100),
('HKD','Hong Kong Dollars','HK$',100),
('HUF','Hungarian Forint','Ft ',100),
('ILS','Israeli New Sheqels','₪',100),
('INR','Indian Rupees','₹',100),
('JPY','Japanese Yen','&yen;',1),
('MXN','Mexican Pesos','$',100),
('NOK','Norwegian Kroner','kr ',100),
('NZD','New Zealand Dollars','$',100),
('PHP','Philippine Pesos','₱',100),
('PLN','Polish Zloty','zł',100),
('SEK','Swedish Kronor','kr ',100),
('SGD','Singapore Dollars','$',100),
('THB','Thai Baht','฿',100),
('TWD','New Taiwan Dollars','NT$',100),
('USD','US Dollars','$',100);

insert into db_change
values (
  39,
  'Create the currency table.',
  '0039_create_currency_table.sql',
  now()
);
