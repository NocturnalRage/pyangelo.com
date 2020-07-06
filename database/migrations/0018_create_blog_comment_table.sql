CREATE TABLE blog_comment (
  comment_id int unsigned NOT NULL AUTO_INCREMENT,
  blog_id int unsigned NOT NULL,
  person_id int unsigned NOT NULL,
  blog_comment text NOT NULL,
  published boolean NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (comment_id),
  KEY blog_comment (blog_id, created_at),
  KEY person_comment (person_id),
  FOREIGN KEY (blog_id) REFERENCES blog(blog_id),
  FOREIGN KEY (person_id) REFERENCES person(person_id)
) COMMENT='Stores comments for a blog.';

insert into db_change
values (
  18,
  'Create the blog_comment table.',
  '0018_create_blog_comment_table.sql',
  now()
);
