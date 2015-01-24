alter table grades change grade grade varchar(11);
update grades set grade = NULL where grade = '0';