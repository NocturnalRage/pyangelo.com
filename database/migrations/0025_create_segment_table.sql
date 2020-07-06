CREATE TABLE segment(
  segment_id tinyint unsigned NOT NULL,
  segment_name varchar(50) NOT NULL,
  segment_method varchar(50) NOT NULL,
  PRIMARY KEY (segment_id)
) COMMENT='Defines which method from the CampaignRepository that will be called to get the list of subscribers.';

INSERT INTO segment (segment_id, segment_name, segment_method)
VALUES
(1, 'All Members', 'getAllSubscribers'),
(2, 'Free Members', 'getFreeSubscribers');

alter table campaign drop foreign key campaign_ibfk_2;
alter table campaign drop column list_id;
alter table campaign add column segment_id tinyint unsigned NOT NULL AFTER campaign_status_id;
update campaign set segment_id = 1;
alter table campaign add foreign key (segment_id) references segment(segment_id);

insert into db_change
values (
  25,
  'Create the segment table to enable more targeted email campaigns.',
  '0025_create_segment_table.sql',
  now()
);
