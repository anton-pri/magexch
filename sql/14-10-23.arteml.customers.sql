select @cid:=customer_id from cw_customers where usertype='A' limit 1;
insert into cw_customers_system_info (customer_id, creation_customer_id, creation_date, modification_customer_id, modification_date)  select c.customer_id, @cid, UNIX_TIMESTAMP(), @cid, UNIX_TIMESTAMP() from cw_customers c left join cw_customers_system_info ci on c.customer_id=ci.customer_id where ci.customer_id IS NULL;
