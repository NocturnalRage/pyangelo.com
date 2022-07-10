alter table stripe_product modify product_description VARCHAR(255) not null;

update stripe_product
set product_name = 'Full Access',
    product_description = 'Provides full access to every PyAngelo tutorial'
where product_name = 'PyAngelo Premium Membership';

insert into stripe_product values (
'prod_M18MmyAl5aUI8u',
'Full Access Plus',
'Provides full access to every PyAngelo tutorial plus increases the maximum upload size of assets from 8MB to 64MB.',
TRUE
);

insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbv3qa97KE',
'prod_M18MmyAl5aUI8u',
'AUD',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbnmg3N9E5',
'prod_M18MmyAl5aUI8u',
'BBD',
1995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbln6Dt8HV',
'prod_M18MmyAl5aUI8u',
'BND',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbeoVkws4R',
'prod_M18MmyAl5aUI8u',
'BRL',
3295,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbRPhzYpfC',
'prod_M18MmyAl5aUI8u',
'CAD',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmbQGrEkLhO',
'prod_M18MmyAl5aUI8u',
'CHF',
1125,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmb5e80sENu',
'prod_M18MmyAl5aUI8u',
'CNY',
6750,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65mAkvBrl8hmblCbKMytQ',
'prod_M18MmyAl5aUI8u',
'CZK',
22500,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbl4nIKv2B',
'prod_M18MmyAl5aUI8u',
'DKK',
7495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbRItlT9dx',
'prod_M18MmyAl5aUI8u',
'EGP',
19500,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbZE1a9YzR',
'prod_M18MmyAl5aUI8u',
'EUR',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbKvCG9zzZ',
'prod_M18MmyAl5aUI8u',
'FJD',
2250,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmb5czQMkSs',
'prod_M18MmyAl5aUI8u',
'GBP',
895,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbg60GlkBB',
'prod_M18MmyAl5aUI8u',
'HKD',
7995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbpBNtIXLC',
'prod_M18MmyAl5aUI8u',
'HUF',
300000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbVw0TzcvF',
'prod_M18MmyAl5aUI8u',
'IDR',
13500000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbdHOG24jF',
'prod_M18MmyAl5aUI8u',
'ILS',
3695,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbNZru3lFc',
'prod_M18MmyAl5aUI8u',
'INR',
60000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbY1LLHjue',
'prod_M18MmyAl5aUI8u',
'JMD',
135000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65nAkvBrl8hmbLlCymQTk',
'prod_M18MmyAl5aUI8u',
'JPY',
1200,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbfQz1IBWN',
'prod_M18MmyAl5aUI8u',
'LKR',
150000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmb8gl2gBfw',
'prod_M18MmyAl5aUI8u',
'MXN',
18000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbTft52KWX',
'prod_M18MmyAl5aUI8u',
'MYR',
3695,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmb9fJi3LkL',
'prod_M18MmyAl5aUI8u',
'NGN',
300000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbJ2VAeiWi',
'prod_M18MmyAl5aUI8u',
'NOK',
8995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbE9jQ9MaP',
'prod_M18MmyAl5aUI8u',
'NZD',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbGln7r6sZ',
'prod_M18MmyAl5aUI8u',
'PHP',
45000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbmJEiqhLW',
'prod_M18MmyAl5aUI8u',
'PLN',
3695,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbGqMvlDbN',
'prod_M18MmyAl5aUI8u',
'QAR',
3695,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbHmrgLt4m',
'prod_M18MmyAl5aUI8u',
'RUB',
60000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65pAkvBrl8hmb1wBO7d27',
'prod_M18MmyAl5aUI8u',
'SEK',
9995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbdQ8YsNIR',
'prod_M18MmyAl5aUI8u',
'SGD',
1495,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbpC1Ddqmx',
'prod_M18MmyAl5aUI8u',
'THB',
33000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65oAkvBrl8hmbvn2mYwfY',
'prod_M18MmyAl5aUI8u',
'TWD',
30000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65pAkvBrl8hmbihcQCMGf',
'prod_M18MmyAl5aUI8u',
'USD',
995,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65pAkvBrl8hmbCAYHQJwZ',
'prod_M18MmyAl5aUI8u',
'VND',
210000,
'month',
TRUE
);
insert into stripe_price values (
'price_1LJ65pAkvBrl8hmbn7IwrBXw',
'prod_M18MmyAl5aUI8u',
'ZAR',
13595,
'month',
TRUE
);

insert into db_change
values (
  57,
  'Add full access plus product for Stripe.',
  '0057_stripe_product_add.sql',
  now()
);
