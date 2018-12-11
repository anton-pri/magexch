update Supplier set short_name='VIAS' where SupplierName like '%Vias%';
select @sup_id:=supplier_id  from Supplier where short_name='VIAS';
