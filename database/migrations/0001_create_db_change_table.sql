create table db_change (
  `change_id` smallint unsigned NOT NULL COMMENT 'Surrogate key',
  `change_desc` varchar(500) NOT NULL COMMENT 'Description of change',
  `script_name` varchar(50) NOT NULL COMMENT 'Name of the script that was run',
  `date_applied` datetime NOT NULL COMMENT 'The date the change was applied',
  PRIMARY KEY (`change_id`),
  KEY date_applied (date_applied)
) COMMENT='Changes to this database';

insert into db_change
values (
  1,
  'Create the db_changes table to record all scripts that update the database.',
  '0001_create_db_change_table.sql',
  now()
);
