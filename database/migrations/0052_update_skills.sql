CREATE TABLE tutorial_skill (
  tutorial_id int unsigned NOT NULL AUTO_INCREMENT,
  skill_id int unsigned NOT NULL,
  created_at datetime NOT NULL,
  PRIMARY KEY (tutorial_id, skill_id),
  FOREIGN KEY (tutorial_id) REFERENCES tutorial(tutorial_id),
  FOREIGN KEY (skill_id) REFERENCES skill(skill_id)
);

alter table skill drop constraint skill_ibfk_1;
alter table skill drop column tutorial_id;

insert into db_change
values (
  52,
  'Update skills so they can belong to more than one tutorial.',
  '0052_update_skills.sql',
  now()
);
