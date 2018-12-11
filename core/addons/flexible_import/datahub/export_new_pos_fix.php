<?php

print("<H1>New items pos and item_store2 update</H1><br>");


db_query("update item_store2 is2 inner join pos p on p.`Item Description`='' and p.`Alternate Lookup`=is2.item_id and p.`Item Number`!=is2.store_sku set is2.store_sku=p.`Item Number`");
db_query("replace into item_store2 (store_sku, store_id, item_id) select `Item Number`, 1, `Alternate Lookup` from pos where `Alternate Lookup` not in (select item_id from item_store2) and `Alternate Lookup`!=0 and `Alternate Lookup`!=''");

db_query("update pos p inner join item_xref ix on REPLACE(p.`Custom Field 5`,' ','')=ix.xref and p.`Vendor Code`=0 and ix.supplier_id!=0 set p.`Vendor Code`=ix.supplier_id");
db_query("update pos p inner join Supplier supp on supp.supplier_id=p.`Vendor Code` and p.`Vendor Code`!=0 and p.`Vendor Name`='' set p.`Vendor Name`=supp.SupplierName");

print("<h3>Done, run Transfer live once again to see effect in new items export...<a href='index.php?target=datahub_main_edit'>Return to main edit page</a></h3><br><br><br>");
die;
