insert into Supplier (SupplierName, short_name) values ('Angels', 'ANG');
update Supplier set supplier_id = ID where short_name = 'ANG';
select @sup_id:=supplier_id from Supplier where short_name = 'ANG';

