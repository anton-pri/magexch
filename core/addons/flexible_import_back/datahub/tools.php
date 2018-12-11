<?php

if ($REQUEST_METHOD == 'POST') {
    if ($action == 'pos_export') {
        $export_sqls = array();
        $export_errs = array();
        if ($export_includes['new']) { 
            $datahub_main_data_newpos = "$tables[datahub_main_data]_newpos";
            db_query("drop table if exists $datahub_main_data_newpos"); 
            db_query("create table $datahub_main_data_newpos like $tables[datahub_main_data]");
            db_query("insert into $datahub_main_data_newpos select * from $tables[datahub_main_data]");
            db_query("delete np.* from $datahub_main_data_newpos np inner join $tables[datahub_pos]_snapshot ps on ps.`Alternate Lookup`=np.ID");  
            db_query("delete np.* from $datahub_main_data_newpos np inner join $tables[datahub_main_data]_snapshot ps on ps.ID=np.ID");

            $exp_rows_count = cw_query_first_cell("select count(*) from $datahub_main_data_newpos");

            $pos_item_number = cw_query_first_cell("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = '$tables[datahub_pos]'");
            db_query("drop table if exists $tables[datahub_pos]_new");
            db_query("create table $tables[datahub_pos]_new like $tables[datahub_pos]");
            db_query("alter table $tables[datahub_pos]_new AUTO_INCREMENT = $pos_item_number");
            db_query("insert into $tables[datahub_pos]_new 
                (`Item Name`, `Item Description`, `Alternate Lookup`, `Attribute`, `Size`, `Average Unit Cost`, `Regular Price`, `MSRP`, `Custom Price 1`, `Department Name`, `Department Code`, `Vendor Code`, `Vendor Name`, `Qty 1`, `Custom Field 5`) 
                select Replace(CONCAT(Left(Trim(Producer),10) , TRIM(CONCAT(Left(CONCAT(name , ' '),14) , Right(CONCAT(' ' , Vintage),2) , '.' , Left(CONCAT(Size , ' '),3)))),' ',''), CONCAT(CAST(COALESCE(Producer,' ') AS CHAR) , ' ' , CAST(COALESCE(name,' ') AS CHAR) , ' ' , CAST(COALESCE(Vintage,' ') AS CHAR) , ' ' , CAST(COALESCE(size,' ') AS CHAR)), ID, Vintage, Size, cost, price, 0, 0, country, 0, supplier_id, '', stock, IFNULL(initial_xref,CONCAT('CWSWE-',ID)) 
                from $tables[datahub_main_data]_newpos");

            db_query("update $tables[datahub_pos]_new set `Item Name` = concat(`Item Number`,' ',`Item Name`)");

            $exp_rows_count = cw_query_first_cell("select count(*) from $tables[datahub_pos]_new"); 
            if($exp_rows_count > 0) {
                $export_sqls['new'] = "select * from $tables[datahub_pos]_new";
            } else {
                $export_errs['new'] = 'No new records to export. ';
            }

        } 
        if ($export_includes['changed']) {
            db_query("drop table if exists $tables[datahub_pos]_changed");
            db_query("create table $tables[datahub_pos]_changed like $tables[datahub_pos]");
 
            $sql = "INSERT INTO cw_datahub_pos_changed (`Item Number`, `Item Name`, `Item Description`, `Alternate Lookup`, `Attribute`, `Size`, `Average Unit Cost`, `Regular Price`, `MSRP`, `Custom Price 1`, `Department Name`, `Department Code`, `Vendor Code`, `Vendor Name`, `Qty 1`, `On Order Qty`, `Reorder Point 1`, `Custom Field 1`, `Custom Field 2`, `Custom Field 3`, `Custom Field 4`, `Custom Field 5`)                     SELECT ps.`Item Number` as `Item Number`, ps.`Item Name` as `Item Name`, concat(md.Producer,' ',md.name,' ',md.Vintage,' ',md.Size) as `Item Description`, md.ID as `Alternate Lookup`, md.Vintage as `Attribute`, md.Size as `Size`, 0 as `Average Unit Cost`, md.price as `Regular Price`, ps.`MSRP` as `MSRP`, ps.`Custom Price 1` as `Custom Price 1`, md.country as `Department Name`, ps.`Department Code` as `Department Code`, md.supplier_id as `Vendor Code`, ps.`Vendor Name` as `Vendor Name`, md.stock as `Qty 1`, ps.`On Order Qty`, ps.`Reorder Point 1`, ps.`Custom Field 1`, ps.`Custom Field 2`, ps.`Custom Field 3`, ps.`Custom Field 4`, IFNULL(md.initial_xref,CONCAT('CWSWE-',md.ID)) as `Custom Field 5` from cw_datahub_main_data md inner join cw_datahub_main_data_possnapshot mdps on md.ID=mdps.ID inner join cw_datahub_pos_snapshot ps on ps.`Alternate Lookup`=md.ID where (md.Producer=mdps.Producer AND md.name=mdps.name) AND (md.Vintage!=mdps.Vintage OR md.Size!=mdps.Size OR md.price!=mdps.price OR md.country!=mdps.country OR md.supplier_id!=mdps.supplier_id OR md.stock!=mdps.stock)";
            db_query($sql);

            $sql = "INSERT IGNORE INTO cw_datahub_pos_changed (`Item Number`, `Item Name`, `Item Description`, `Alternate Lookup`, `Attribute`, `Size`, `Average Unit Cost`, `Regular Price`, `MSRP`, `Custom Price 1`, `Department Name`, `Department Code`, `Vendor Code`, `Vendor Name`, `Qty 1`, `On Order Qty`, `Reorder Point 1`, `Custom Field 1`, `Custom Field 2`, `Custom Field 3`, `Custom Field 4`, `Custom Field 5`)                     SELECT ps.`Item Number` as `Item Number`, Replace(CONCAT(Left(Trim(md.Producer),10) , TRIM(CONCAT(Left(CONCAT(md.name , ' '),14) , Right(CONCAT(' ' , md.Vintage),2) , '.' , Left(CONCAT(md.Size , ' '),3)))),' ','') as `Item Name`, CONCAT(CAST(COALESCE(md.Producer,' ') AS CHAR) , ' ' , CAST(COALESCE(md.name,' ') AS CHAR) , ' ' , CAST(COALESCE(md.Vintage,' ') AS CHAR) , ' ' , CAST(COALESCE(md.size,' ') AS CHAR)) as `Item Description`, md.ID as `Alternate Lookup`, md.Vintage as `Attribute`, md.Size as `Size`, 0 as `Average Unit Cost`, md.price as `Regular Price`, ps.`MSRP` as `MSRP`, ps.`Custom Price 1` as `Custom Price 1`, md.country as `Department Name`, ps.`Department Code` as `Department Code`, md.supplier_id as `Vendor Code`, ps.`Vendor Name` as `Vendor Name`, md.stock as `Qty 1`, ps.`On Order Qty`, ps.`Reorder Point 1`, ps.`Custom Field 1`, ps.`Custom Field 2`, ps.`Custom Field 3`, ps.`Custom Field 4`, IFNULL(md.initial_xref,CONCAT('CWSWE-',md.ID)) as `Custom Field 5` from cw_datahub_main_data md inner join cw_datahub_main_data_possnapshot mdps on md.ID=mdps.ID inner join cw_datahub_pos_snapshot ps on ps.`Alternate Lookup`=md.ID where md.Producer!=mdps.Producer OR md.name!=mdps.name OR md.Vintage!=mdps.Vintage OR md.Size!=mdps.Size OR md.price!=mdps.price OR md.country!=mdps.country OR md.supplier_id!=mdps.supplier_id OR md.stock!=mdps.stock";
            db_query($sql);

            $exp_rows_count = cw_query_first_cell("select count(*) from $tables[datahub_pos]_changed");
            if($exp_rows_count > 0) {
                $export_sqls['changed'] = "select * from $tables[datahub_pos]_changed";
            } else {
                $export_errs['changed'] = 'No changed records to export. ';
            }

        } 
        if ($export_includes['orphaned']) { 

            $datahub_main_data_orphpos = "$tables[datahub_main_data]_orphpos";
            db_query("drop table if exists $datahub_main_data_orphpos");
            db_query("create table $datahub_main_data_orphpos like $tables[datahub_main_data]");
            db_query("insert into $datahub_main_data_orphpos select * from $tables[datahub_main_data]");
            db_query("delete np.* from $datahub_main_data_orphpos np inner join $tables[datahub_pos]_snapshot ps on ps.`Alternate Lookup`=np.ID");


            db_query("drop table if exists $tables[datahub_pos]_orphaned");
            db_query("create table $tables[datahub_pos]_orphaned like $tables[datahub_pos]");

                          $sql = "INSERT IGNORE INTO $tables[datahub_pos]_orphaned ( 
                                                `Item Number`,
                                                `Item Name`,
                                                `Item Description`,     
                                                `Alternate Lookup`,                                     
                                                `Attribute`,
                                                `Size`,
                                                `Average Unit Cost`,
                                                `Regular Price`,
                                                `Department Name`,
                                                `Vendor Code`,
                                                `Custom Field 1`,
                                                `Custom Field 2`,
                                                `Custom Field 3`,
                                                `Custom Field 5`
                                                )
                                                SELECT DISTINCT
                                                i2.store_sku AS Expr1, 
                                                Replace(CONCAT(Left(Trim(i.Producer),10) , TRIM(CONCAT(Left(CONCAT(i.name , ' '),14) , Right(CONCAT(' ' , i.Vintage),2) , '.' , Left(CONCAT(i.Size , ' '),3)))),' ','') AS Expr2,
                                                CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , ' ' , CAST(COALESCE(i.name,' ') AS CHAR) , ' ' , CAST(COALESCE(i.Vintage,' ') AS CHAR) , ' ' , CAST(COALESCE(i.size,' ') AS CHAR)) AS Expr3,
                                                i.ID,
                                                i.Vintage AS Expr4,
                                                i.size,                         
                                                i.cost,
                                                IF(COALESCE(f.xref, '') = '', Round(CAST(i.cost as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01),
                                                i.country,
                                                i.supplier_id,
                                                i.Producer,
                                                i.varietal,
                                                i.bot_per_case,
                                                i.initial_xref  
                                                FROM $datahub_main_data_orphpos AS i  
                                                INNER JOIN $tables[datahub_item_store2] AS i2 ON (i2.item_id = i.ID AND i2.store_id=1)
                                                INNER JOIN cw_datahub_transfer_item_price AS pr ON i.ID = pr.item_id and pr.store_id = 1
                                                LEFT JOIN $tables[datahub_BevAccessFeeds] AS f ON f.xref = i.initial_xref        
                                                WHERE i.ID > 0";


            db_query($sql);

            $exp_rows_count = cw_query_first_cell("select count(*) from $tables[datahub_pos]_orphaned");
            if($exp_rows_count > 0) {
                $export_sqls['orphaned'] = "select * from $tables[datahub_pos]_orphaned";
            } else {
                $export_errs['orphaned'] = 'No orphaned records to export. ';
            }
        }
        if (!empty($export_sqls)) {
             $exp_sql = implode(' union all ', $export_sqls);
             $ts = date('Y-m-d__H-i-s');
             cw_datahub_exportMysqlToXls($tables['datahub_pos'], implode('_',array_keys($export_sqls))."_$ts.xls", $exp_sql);
        } elseif (!empty($export_errs)) {
             cw_add_top_message(implode('<br />', $export_errs),'E');
             cw_header_location("index.php?target=datahub_tools"); 
        } else {
             cw_add_top_message('Please select option(s) to export','E');
             cw_header_location("index.php?target=datahub_tools");
        } 
    }

    die;
}

$smarty->assign('main', 'datahub_tools');
