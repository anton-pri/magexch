insert into Supplier (SupplierName, short_name) values ('Wilson Daniels', 'WLS');
update Supplier set supplier_id = ID where short_name = 'WLS';
select supplier_id from Supplier where short_name = 'WLS';

