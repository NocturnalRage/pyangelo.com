alter table segment drop column segment_method;
alter table segment add column list_id int unsigned NOT NULL;
alter table segment add column autoresponder_where_condition varchar(255) NOT NULL;

update segment
set    list_id = 1,
       autoresponder_where_condition = '1=1'
where  segment_id = 1; 

update segment
set    list_id = 1,
       autoresponder_where_condition = '1=1'
where  segment_id = 2; 


alter table segment add foreign key (list_id) references list(list_id);

insert into db_change
values (
  26,
  'Modify the segment table to also cater for autoresponders.',
  '0026_modify_segment_table.sql',
  now()
);
