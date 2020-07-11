alter table tutorial add column single_sketch boolean NOT NULL after tutorial_level_id;

update tutorial set single_sketch = false;

insert into db_change
values (
  32,
  'Add a column to indicate if a tutorial should have a single sketch.',
  '0032_add_single_sketch_to_tutorial.sql',
  now()
);
