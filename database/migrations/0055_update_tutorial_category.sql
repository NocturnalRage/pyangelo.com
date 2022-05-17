alter table tutorial_category
modify category VARCHAR(30) not null;

alter table tutorial_category
modify category_slug VARCHAR(30) not null;

UPDATE tutorial_category
set category = 'Introduction to Programming',
    category_slug = 'introduction-to-programming'
where tutorial_category_id = 1;

UPDATE tutorial
SET    slug = 'introduction-to-graphical-programming'
where  tutorial_id = 1;

insert into db_change
values (
  55,
  'Update getting-started category to introduction-to-programming.',
  '0055_update_tutorial_category.sql',
  now()
);
