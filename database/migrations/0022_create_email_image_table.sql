CREATE TABLE email_image (
  image_id int unsigned NOT NULL auto_increment,
  image_name varchar(255) NOT NULL,
  image_width smallint unsigned NOT NULL,
  image_height smallint unsigned NOT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (image_id)
) COMMENT='Stores images which can be used in a PyAngelo email.';

insert into db_change
values (
  22,
  'Create the email_image table.',
  '0022_create_email_image_table.sql',
  now()
);
