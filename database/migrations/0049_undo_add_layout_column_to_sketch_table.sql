ALTER TABLE sketch DROP COLUMN layout;

DELETE from db_change where change_id = 49;
