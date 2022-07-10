alter table stripe_product modify product_description VARCHAR(255) not null;

UPDATE stripe_product
SET    product_name = 'Full Access',
       product_description = 'Provides full access to every PyAngelo tutorial'
WHERE  product_id = 'prod_JvIKdSouFisljN';

INSERT INTO stripe_product
(stripe_product_id, product_name, product_description, active)
VALUES
('prod_M1KtdHu7goh2Lg', 'Full Access Plus', 'Provides full access to every PyAngelo tutorial plus increases the maximum upload size of assets from 8MB to 64MB.', 1);

INSERT INTO stripe_price
(stripe_price_id, stripe_product_id, currency_code, price_in_cents, billing_period, active)
VALUES
('price_1LJIDkAkvBrl8hmbkTP8kBNO','prod_M1KtdHu7goh2Lg','aud',1495, 'month', 1)
('price_1LJIDkAkvBrl8hmbCwEgascZ','prod_M1KtdHu7goh2Lg','bbd',1995, 'month', 1)
('price_1LJIDkAkvBrl8hmbADwHkWhl','prod_M1KtdHu7goh2Lg','bnd',1495, 'month', 1)
('price_1LJIDkAkvBrl8hmblza89Iu6','prod_M1KtdHu7goh2Lg','brl',3295, 'month', 1)
('price_1LJIDkAkvBrl8hmbMRZADoDX','prod_M1KtdHu7goh2Lg','cad',1495, 'month', 1)
('price_1LJIDkAkvBrl8hmbrhSR7T9v','prod_M1KtdHu7goh2Lg','chf',1125, 'month', 1)
('price_1LJIDkAkvBrl8hmbvttWwTEq','prod_M1KtdHu7goh2Lg','cny',6750, 'month', 1)
('price_1LJIDkAkvBrl8hmbe8N1a0gc','prod_M1KtdHu7goh2Lg','czk',22500, 'month', 1)
('price_1LJIDkAkvBrl8hmb70vtxdXT','prod_M1KtdHu7goh2Lg','dkk',7495, 'month', 1)
('price_1LJIDjAkvBrl8hmbn0sn5QMy','prod_M1KtdHu7goh2Lg','egp',19500, 'month', 1)
('price_1LJIDjAkvBrl8hmbR4HFaqjt','prod_M1KtdHu7goh2Lg','eur',995, 'month', 1)
('price_1LJIDjAkvBrl8hmbdl7Y0w1C','prod_M1KtdHu7goh2Lg','fjd',2250, 'month', 1)
('price_1LJIDjAkvBrl8hmbpQOcWqhd','prod_M1KtdHu7goh2Lg','gbp',895, 'month', 1)
('price_1LJIDjAkvBrl8hmbPSgG0BRE','prod_M1KtdHu7goh2Lg','hkd',7995, 'month', 1)
('price_1LJIDjAkvBrl8hmbpxeQWL0l','prod_M1KtdHu7goh2Lg','huf',300000, 'month', 1)
('price_1LJIDjAkvBrl8hmbcMFZUnf8','prod_M1KtdHu7goh2Lg','idr',13500000, 'month', 1)
('price_1LJIDjAkvBrl8hmbN4cKd9BY','prod_M1KtdHu7goh2Lg','ils',3695, 'month', 1)
('price_1LJIDjAkvBrl8hmb3ElVhjt2','prod_M1KtdHu7goh2Lg','inr',60000, 'month', 1)
('price_1LJIDjAkvBrl8hmbZZ6K6FS8','prod_M1KtdHu7goh2Lg','jmd',135000, 'month', 1)
('price_1LJIDjAkvBrl8hmbPPX4SR3o','prod_M1KtdHu7goh2Lg','jpy',1200, 'month', 1)
('price_1LJIDjAkvBrl8hmbMrS5QMiu','prod_M1KtdHu7goh2Lg','lkr',150000, 'month', 1)
('price_1LJIDjAkvBrl8hmbG3J2y6EE','prod_M1KtdHu7goh2Lg','mxn',18000, 'month', 1)
('price_1LJIDjAkvBrl8hmbpMELp5YD','prod_M1KtdHu7goh2Lg','myr',3695, 'month', 1)
('price_1LJIDjAkvBrl8hmbApFEM5XN','prod_M1KtdHu7goh2Lg','ngn',300000, 'month', 1)
('price_1LJIDjAkvBrl8hmb8bqgVqGK','prod_M1KtdHu7goh2Lg','nok',8995, 'month', 1)
('price_1LJIDjAkvBrl8hmbrxcxDBq6','prod_M1KtdHu7goh2Lg','nzd',1495, 'month', 1)
('price_1LJIDjAkvBrl8hmbihaF8Ts9','prod_M1KtdHu7goh2Lg','php',45000, 'month', 1)
('price_1LJIDjAkvBrl8hmb5fcEy6u0','prod_M1KtdHu7goh2Lg','pln',3695, 'month', 1)
('price_1LJIDjAkvBrl8hmbwmUudWCv','prod_M1KtdHu7goh2Lg','qar',3695, 'month', 1)
('price_1LJIDiAkvBrl8hmbiEqEkPPv','prod_M1KtdHu7goh2Lg','rub',60000, 'month', 1)
('price_1LJIDiAkvBrl8hmbMzHHqiBS','prod_M1KtdHu7goh2Lg','sgd',1495, 'month', 1)
('price_1LJIDiAkvBrl8hmbDpkymQBv','prod_M1KtdHu7goh2Lg','thb',33000, 'month', 1)
('price_1LJIDiAkvBrl8hmbeqMvoMIM','prod_M1KtdHu7goh2Lg','twd',30000, 'month', 1)
('price_1LJIDiAkvBrl8hmbz8oMZmmp','prod_M1KtdHu7goh2Lg','usd',995, 'month', 1)
('price_1LJIDiAkvBrl8hmb58AuwnY8','prod_M1KtdHu7goh2Lg','sek',9995, 'month', 1)
('price_1LJIDiAkvBrl8hmbMRhwqXdf','prod_M1KtdHu7goh2Lg','vnd',210000, 'month', 1)
('price_1LJIDiAkvBrl8hmbRqof7DyP','prod_M1KtdHu7goh2Lg','zar',13595, 'month', 1)

insert into db_change
values (
  57,
  'Add full access plus product for Stripe.',
  '0057_stripe_product_add_prod.sql',
  now()
);
