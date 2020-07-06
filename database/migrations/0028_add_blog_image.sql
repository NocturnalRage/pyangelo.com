alter table blog add column blog_image varchar(255) NOT NULL after slug;

insert into db_change
values (
  28,
  'Add a thumbnail column to the blog table to store an image for each blog.',
  '0028_add_blog_image.sql',
  now()
);
