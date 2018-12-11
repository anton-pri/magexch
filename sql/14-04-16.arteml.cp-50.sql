select @cid:=customer_id from cw_customers where usertype='A' and status='Y' limit 1;

insert into cw_products_system_info (product_id, creation_customer_id, modification_customer_id) select p.product_id, @cid, @cid from cw_products p left join cw_products_system_info s on p.product_id=s.product_id where s.product_id IS NULL;
