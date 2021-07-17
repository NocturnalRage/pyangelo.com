insert into currency
(currency_code, currency_description, currency_symbol, stripe_divisor)
values
('VND', 'Vietnamese Đồng', '&#8363;', 1),
('CNY', 'Chinese Renminbi', '&yen;', 100),
('MYR', 'Malaysian Ringgit', 'RM', 100),
('ZAR', 'South African Rand', 'R', 100),
('QAR', 'Qatari Riyal', 'ر.ق', 100),
('NGN', 'Nigerian Naira', '&#8358;', 100),
('FJD', 'Fijian Dollars', 'FJ$', 100),
('BBD', 'Barbadian Dollars', 'Bds$', 100),
('BND', 'Brunei Dollars', 'B$', 100),
('BRL', 'Brazilian Real', 'R$', 100),
('EGP', 'Egyptian Pounds', 'E£', 100),
('IDR', 'Indonesian Rupiah', 'Rp', 100),
('JMD', 'Jamaican Dollars', 'J$', 100),
('LKR', 'Sri Lankan Rupees', 'Ɍs', 100),
('RUB', 'Russian Ruble', '₽', 100);

update country set currency_code = 'VND' where country_code = 'VN';
update country set currency_code = 'CNY' where country_code = 'CN';
update country set currency_code = 'MYR' where country_code = 'MY';
update country set currency_code = 'ZAR' where country_code = 'ZA';
update country set currency_code = 'QAR' where country_code = 'QA';
update country set currency_code = 'NGN' where country_code = 'NG';
update country set currency_code = 'FJD' where country_code = 'FJ';
update country set currency_code = 'BBD' where country_code = 'BB';
update country set currency_code = 'BND' where country_code = 'BN';
update country set currency_code = 'BRL' where country_code = 'BR';
update country set currency_code = 'EGP' where country_code = 'EG';
update country set currency_code = 'IDR' where country_code = 'ID';
update country set currency_code = 'JMD' where country_code = 'JM';
update country set currency_code = 'LKR' where country_code = 'LK';
update country set currency_code = 'RUB' where country_code = 'RU';

insert into db_change
values (
  43,
  'Add more currencies.',
  '0043_add_currencies.sql',
  now()
);
