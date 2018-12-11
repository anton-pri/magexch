insert into Supplier (SupplierName, short_name) values ('Cynthia Hurley French Wines', 'CYN');
update Supplier set supplier_id = ID where short_name = 'CYN';
select supplier_id from Supplier where short_name = 'CYN';

