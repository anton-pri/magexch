<?php

                $sql = "DELETE FROM pos_stranded";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

                $sql = "ALTER TABLE pos CHANGE `Alternate Lookup` `Alternate Lookup` INT( 11 ) NOT NULL";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

                $sql = "INSERT IGNORE INTO pos_stranded ( 
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
                                                Replace(CONCAT(Left(Trim(i.Producer),10) , TRIM(CONCAT(Left(CONCAT(i.name , \" \"),14) , Right(CONCAT(\" \" , i.Vintage),2) , \".\" , Left(CONCAT(i.Size , \" \"),3)))),\" \",\"\") AS Expr2,
                                                CONCAT(CAST(COALESCE(i.Producer,' ') AS CHAR) , \" \" , CAST(COALESCE(i.name,' ') AS CHAR) , \" \" , CAST(COALESCE(i.Vintage,' ') AS CHAR) , \" \" , CAST(COALESCE(i.size,' ') AS CHAR)) AS Expr3,
                                                x.item_id,
                                                i.Vintage AS Expr4,
                                                i.size,                         
                                                x.cost_per_bottle,
                                                IF(COALESCE(f.xref, '') = '', Round(CAST(x.cost_per_bottle as DECIMAL(19,4))*1.5,0)-0.01, Round(IF(IsNull(`f`.`case_price`) or f.case_price = 0 or f.case_price <= f.bot_price,`f`.`bot_price`,`f`.`case_price`/`f`.`botpercase`)*1.5,0)-0.01),
                                                i.country,
                                                x.supplier_id,
                                                i.Producer,
                                                i.varietal,
                                                x.bot_per_case,
                                                x.xref                          
                                                
                                                FROM item_xref AS x  
                                                INNER JOIN item_store2 AS i2 ON ( i2.item_id = x.item_id and x.store_id = 1)
                                                INNER JOIN item as i ON x.item_id = i.ID
                                                LEFT JOIN pos AS p ON p.`Alternate Lookup`  = i.ID
                                                INNER JOIN item_price AS pr ON x.item_id = pr.item_id and pr.store_id = 1 
                                                LEFT JOIN BevAccessFeeds AS f ON f.xref = x.xref        
                                                WHERE    i.ID   > 0
                                                AND x.store_id =1
                                                AND COALESCE( p.`Alternate Lookup` , 0 ) = 0";

                db_query($sql); //mysql_query($sql) or sql_error($sql);

                //pos::binlocation_to_varchar();
                $sql = "ALTER TABLE pos CHANGE `Alternate Lookup` `Alternate Lookup` VARCHAR(255 ) NOT NULL";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

                //sometimes probs with dup_catid in item table
                //perhaps it can be solved with the query above but this works for now
                $sql = "DELETE FROM pos_stranded
                                                WHERE `Item Number` 
                                                IN (
                                                        SELECT  `Item Number`  FROM pos
                                                )";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

                //update Item Name
                $sql = "UPDATE pos_stranded
                                                SET `Item Name` = CONCAT(`Item Number` , ' ', `Item Name`)
                                                WHERE CAST(TRIM(`Item Number`) as CHAR)  <>  LEFT(TRIM(`Item Name`), LENGTH(`Item Number`))";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

//add MSRP and Custom Price 1
                $sql = "UPDATE (item_store2 AS si 
                                                INNER JOIN pos_stranded AS q ON si.store_sku = q.`Item Number`) 
                                                INNER JOIN item_price AS pr ON si.item_id = pr.item_id and pr.store_id = 1 
                                                SET q.`MSRP` = pr.price,
                                                q.`Custom Price 1` = pr.price
                                                WHERE si.store_id = 1";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

//add vendor name               
                $sql = "UPDATE pos_stranded as ps
                                                INNER JOIN Supplier as s
                                                ON s.supplier_id = ps.`Vendor Code`
                                                SET ps.`Vendor Name` = s.SupplierName";
                db_query($sql); //mysql_query($sql) or sql_error($sql);

        //delete anything with no cost
        $sql = "DELETE FROM pos_stranded WHERE COALESCE(`Average Unit Cost`, 0) = 0";
        db_query($sql); //mysql_query($sql) or sql_error($sql);

        $sql = 'SELECT  *
                                        FROM pos_stranded  
                                        ORDER BY `Item Number` DESC';

        $orphaned_pos_count = cw_query_first_cell("SELECT count(*) FROM pos_stranded");
        if ($orphaned_pos_count) {
            $ts =  date('Y-m-d__H-i-s');
            cw_datahub_exportMysqlToXls('pos_stranded', "stranded_$ts.xls", $sql);
        } else {
            echo 'no stranded records to export';
        }

die;
