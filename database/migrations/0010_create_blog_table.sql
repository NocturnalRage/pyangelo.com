CREATE TABLE blog_category (
  blog_category_id smallint unsigned NOT NULL AUTO_INCREMENT,
  description varchar(30) NOT NULL,
  PRIMARY KEY (blog_category_id)
) COMMENT='The different categories a blog will be related to.';

insert into blog_category values (1, 'PyAngelo Advice');
insert into blog_category values (2, 'PyAngelo News');
insert into blog_category values (3, 'Coding Tips');
insert into blog_category values (4, 'Coding Ideas');

CREATE TABLE blog (
  blog_id int unsigned NOT NULL AUTO_INCREMENT,
  person_id int unsigned NOT NULL,
  title varchar(100) NOT NULL,
  preview varchar(1000) NOT NULL,
  content text NOT NULL,
  slug varchar(105) NOT NULL,
  blog_category_id smallint unsigned NOT NULL,
  featured boolean NOT NULL,
  published boolean NOT NULL,
  published_at datetime NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY (blog_id),
  UNIQUE KEY slug (slug),
  KEY person (person_id),
  KEY published_at (published_at),
  KEY featured (featured),
  FOREIGN KEY (person_id) REFERENCES person(person_id),
  FOREIGN KEY (blog_category_id) REFERENCES blog_category(blog_category_id)
) COMMENT='Holds all PyAngelo blog posts.';

insert into db_change
values (
  10,
  'Create the blog table.',
  '0010_create_blog_table.sql',
  now()
);
