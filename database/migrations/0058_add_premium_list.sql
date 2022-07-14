INSERT INTO list
(list_id, list_name, created_at, updated_at)
VALUES
(2, 'Premium Newsletter', now(), now());

insert into db_change
values (
  58,
  'Add premium newsletter to list.',
  '0058_add_premium_list.sql',
  now()
);
