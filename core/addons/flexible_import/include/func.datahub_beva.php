<?php

function cw_dh_get_beva_buff_table_name() {
    global $tables;

    global $is_interim;
    $interim_ext = '';
    if ($is_interim)
        $interim_ext = 'interim_';
    $buff_table = $tables['datahub_'.$interim_ext.'import_buffer'];

    return $buff_table;
} 

function cw_dh_build_extra_qty_cols($tbl_alias, $colnames, $from, $to) {
    $extra_qty_columns_arr = array();
    for ($i=$from; $i <= $to; $i++) {
        foreach ($colnames as $colname) {
            if (strpos($colname,'expr:') !== false) {
                $colname = str_replace('expr:','',$colname); 
                $colname = str_replace('###$###',$i, $colname);
                $extra_qty_columns_arr[] = $colname;
            } else {
                if (!empty($tbl_alias))   
                    $extra_qty_columns_arr[] = "$tbl_alias.`$colname$i`";
                else 
                    $extra_qty_columns_arr[] = "`$colname$i`";
            } 
        }
    }
    $extra_qty_columns = implode(', ', $extra_qty_columns_arr);
    return $extra_qty_columns;
}

function cw_dh_BevAFeedsMunge() {

    $extra_qty_columns = cw_dh_build_extra_qty_cols('u', array('qty', 'd_type', 'discount'), 1, 5);

    global $tables, $config;
    $sql = "SELECT 
                'Feed_BEVA' as `Source`, 
                u.`current` as `current`, 
                u.`confstock` as `confstock`, 
                u.`companies` as `wholesaler`, 
                u.`producer` as `producer`, 
                u.`bdesc` AS `Wine`, 
                concat(Left(Trim(Replace(Replace(Replace(Replace(If(InStr(u.`bdesc`,'ml'),Left(u.`bdesc`,
                  IF(INSTR(trim(u.`bdesc`), ' ') > 0,
                     length( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                     ) ) - length( right( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                     ) , instr( reverse( trim( 
                     SUBSTRING(u.`bdesc`,1, instr(u.`bdesc`, 'ml'))
                    ) ) , ' ' ) -1 ) )
                  , 0)
                  ),u.`bdesc`),Coalesce(u.`Vintage`,'~'),''),Coalesce(u.`Producer`,'~'),''),If(IsNull(u.`Producer`),'~',Right(u.`Producer`,LENGTH(u.`Producer`)-IF(INSTR(trim( u.`Producer` ), ' ') > 0, length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) ), 0))),''),If(IsNull(u.`Producer`) or not Instr(u.`Producer`,' '),'~',Mid(
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                     length( trim( 
                     SUBSTRING(u.`Producer`,1, 
                     IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                      , 0)
                     )
                     ) ) - length( right( trim( 
                  SUBSTRING(u.`Producer`,1, 
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                       length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                  )
                  ) , instr( reverse( trim( 
                  SUBSTRING(u.`Producer`,1, 
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                      length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                  )
                  ) ) , ' ' ) -1 ) )   
                  , -1)
                  +1,
                  IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                  length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                  , 0)
                                                -1-
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                
                                                length( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                
                                                )
                                                ) ) - length( right( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                
                                                )
                                                ) , instr( reverse( trim( 
                                                SUBSTRING(u.`Producer`,1, 
                                                IF(INSTR(trim( u.`Producer` ), ' ') > 0,
                                                        length(trim( u.`Producer`) ) - length( right( trim(u.`Producer`), instr( reverse(trim( u.`Producer` )) , ' ' ) -1 ) )
                                                , 0)
                                                )
                                                 ) ) , ' ' ) -1 ) )   
                                                
                                                , -1)
                                                +1)
                                                ),'')),255), IF(u.lwbn='L' AND u.companies='DOMAIN',' (limited)','')) as `Name`,

                                                u.`vintage` AS `Vintage`, 
                                                If(IsNull(u.`size`) Or u.`size`='0','',If(u.`size`='1500','1.5Ltr',If(u.`size`='3000','3.0Ltr',If(u.`size` <1000,
                                                CONCAT(CAST(u.`size` as CHAR) , 'ml'),
                                                If(u.`size`>=1000,CONCAT(CAST(Round(CAST(u.`size` as DECIMAL(10,2))/1000,3) as CHAR) , 'Ltr'),'Other'))))) as `Size`, 
                                                u.`xref` as `xref`, 
                                                u.`botpercase` AS `bottles_per_case`,
                                                null as `catalogid`,
                                                `r`.`region_2` AS `Region`, 
                                                `r`.`region_1` AS `country`, 
                                                ucwords(u.`grape`) AS `varietal`, 
                                                `r`.`region_3` AS `Appellation`, 
                                                `r`.`region_4` AS `sub-appellation`, 
                                                Round(If(IsNull(u.`case_price`) or u.`case_price` = '0',u.`bot_price`,u.`case_price`/u.`botpercase`),2) AS `cost`, 
                                                null AS `Parker_rating`, 
                                                null AS `Parker_review`,
                                                null AS `Spectator_rating`, 
                                                null AS `Spectator_review`, 
                                                null AS `Tanzer_rating`, 
                                                null AS `Tanzer_review`,
                                                $extra_qty_columns,
                                                null as `description`
                                                FROM $tables[datahub_BevAccessFeeds] u  LEFT JOIN $tables[datahub_beva_reg_text] r on u.`reg_text` = `r`.`reg_text`
                                                WHERE (u.`descriptio` not like '%Combo%' and u.`descriptio` not like '%PK%' and ((current = 'Y' and confstock = 'Y') 
                                                or InStr('".$config['flexible_import']['BevA_wholesalers_always_on']."',
                                                
                                                Trim(Left(CONCAT(Coalesce(u.`companies`,'xxx') , ' '),Instr(CONCAT(Coalesce(u.`companies`,'xxx') , ' '),' ')))) > 0) 
                                                
                                                ) and LENGTH(Trim(Coalesce(u.`companies`,' '))) > 0";
                return $sql;
        }

function cw_dh_insert_beva() {
    global $tables;
    $query = cw_dh_BevAFeedsMunge();

    $buff_table = cw_dh_get_beva_buff_table_name(); 

    $extra_qty_buff_columns = cw_dh_build_extra_qty_cols('', array('bot_qty', 'cost_per_case'), 2, 6);
    $extra_qty_munge_columns = 
        cw_dh_build_extra_qty_cols('a',
            array("expr:IF(`a`.d_type###$###='b',`a`.`qty###$###`, `a`.`qty###$###`*CAST(coalesce(`a`.`bottles_per_case`,'12') as SIGNED))",
                  "expr:IF(`a`.d_type###$###='b',CAST(coalesce(`a`.`bottles_per_case`,'12') as SIGNED)*`a`.discount###$###,`a`.discount###$###)"), 
            1, 5); 

    $sql = "INSERT INTO $buff_table ( Source, wholesaler, Producer, Wine, Name, Vintage, `size`, item_xref, item_xref_bot_per_case, Region, country, varietal, Appellation, `sub-appellation`, item_xref_cost_per_bottle, Parker_rating, Parker_review, Spectator_rating, Spectator_review, Tanzer_rating, Tanzer_review, Description, $extra_qty_buff_columns, store_id)
                SELECT `a`.`Source` AS Source, `a`.`wholesaler` AS wholesaler, ucwords(`a`.`producer`) AS producer, ucwords(`a`.`Wine`) AS Wine, ucwords(`a`.`Name`) AS Name, `a`.`Vintage` AS Vintage, `a`.`Size` AS `Size`, `a`.`xref` AS xref, `a`.`bottles_per_case` AS bottles_per_case,  ucwords(`a`.`Region`) AS Region, ucwords(`a`.`country`) AS country, `a`.`varietal` AS varietal, ucwords(`a`.`Appellation`) AS Appellation, ucwords(`a`.`sub-appellation`) AS `sub-appellation`, `a`.`cost` AS cost, `a`.`Parker_rating` AS Parker_rating, `a`.`Parker_review` AS Parker_review, `a`.`Spectator_rating` AS Spectator_rating, `a`.`Spectator_review` AS Spectator_review, `a`.`Tanzer_rating` AS Tanzer_rating, `a`.`Tanzer_review` AS Tanzer_review, `a`.`description`, $extra_qty_munge_columns, '1' 
    FROM ($query) AS a";

    cw_csvxc_logged_query($sql);

//    cw_csvxc_logged_query("DELETE $buff_table.* FROM $buff_table INNER JOIN cw_datahub_import_buffer_blacklist bl ON bl.item_xref=$buff_table.item_xref");
    global $is_interim;
    cw_call('cw_datahub_clean_buffer_by_blacklist', array($is_interim));
}

function cw_dh_update_beva_inventory() {
    global $tables, $config;

    global $is_interim;

//    if (!$is_interim)
//        cw_csvxc_logged_query("UPDATE item_xref SET qty_avail = '0' WHERE CAST(Left(coalesce(xref,'xxx'),3) as SIGNED) > 0");

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = '_interim';

    cw_flexible_import_ix_copy_table('beva', $interim_ext);

    $extra_qty_cols = array();
    for($i=1; $i<=5;$i++) {
        $k = $i+1; 
        $extra_qty_cols[] = "b.bot_qty$k = IF(f.d_type$i='b',f.qty$i, f.qty$i*CAST(coalesce(f.`botpercase`,'12') as SIGNED))";
        $extra_qty_cols[] = "b.cost_per_case$k = IF(f.d_type$i='b',CAST(coalesce(f.`botpercase`,'12') as SIGNED)*f.discount$i,f.discount$i)"; 
    } 
    $extra_qty_cols_query = implode(', ', $extra_qty_cols);

    $buff_table = cw_dh_get_beva_buff_table_name();

        cw_csvxc_logged_query("
            UPDATE $buff_table b, $tables[datahub_BevAccessFeeds] f SET
                b.item_xref_qty_avail = 
                    If(
                          (
                              (f.current = 'Y' and f.confstock = 'Y') OR 
                              InStr('".$config['flexible_import']['BevA_wholesalers_always_on']."',
                                     Trim(
                                         Left(
                                             CONCAT(coalesce(f.companies,'xxx'), ' '),
                                             Instr(CONCAT(coalesce(f.companies,'xxx') , ' '),' ')                                                    
                                             )
                                         )
                                   ) > 0
                          )
                          AND NOT Instr('".$config['flexible_import']['BevA_wholesalers_exclude']."', 
                                         Trim(
                                             Left(
                                                 CONCAT(coalesce(f.companies,'xxx'), ' '),
                                                 Instr(CONCAT(coalesce(f.companies,'xxx') , ' '),' ')                                                                                                   )
                                             )
                                       ),
                          9999,0
                      ),
                b.item_xref_min_price = 0, 
                b.item_xref_bot_per_case = CAST(coalesce(f.`botpercase`,'12') as SIGNED), 
                b.item_xref_cost_per_bottle = Round(f.bot_price,2), 
                b.item_xref_cost_per_case = 
                    Round(
                        If(coalesce(f.case_price,'0') = '0', 
                           f.bot_price*(CAST(coalesce(f.`botpercase`,'12') as SIGNED)),
                           f.case_price
                          )
                        ,2
                    ), 
                $extra_qty_cols_query
            WHERE f.xref=b.item_xref
        ");
}

function cw_dh_apply_splitcase_to_cost() {
    global $tables;

    $buff_table = cw_dh_get_beva_buff_table_name();

    $sql = "UPDATE ($buff_table AS i 
            INNER JOIN $tables[datahub_BevAccessFeeds] AS f 
            ON i.item_xref = f.xref) 
            LEFT JOIN $tables[datahub_splitcase_charges] AS c 
            ON Trim(Left(coalesce(CONCAT(f.companies , ' '),'x '),
            InStr(coalesce(CONCAT(f.companies , ' '),'x '),' ')))=c.company 
            SET split_case_charge = coalesce(c.charge*If(coalesce(i.item_xref_bot_per_case,12) > 1 
            AND InStr(c.company,'COLONY'),12/coalesce(i.item_xref_bot_per_case,12),
            If(coalesce(i.item_xref_bot_per_case,12) = 1,0,1)),0.75)";

    cw_csvxc_logged_query($sql);

    $sql = "UPDATE `$buff_table` AS i INNER JOIN $tables[datahub_BevAccessFeeds] AS f ON i.item_xref = f.xref AND f.companies LIKE '%COLONY%' SET i.split_case_charge=IF(f.size='750',2.40,IF(f.size='375',1.15,IF(f.size='1500',4.60,2.40)))";

    cw_csvxc_logged_query($sql);


    $sql = "UPDATE `$buff_table` AS i INNER JOIN $tables[datahub_BevAccessFeeds] AS f ON i.item_xref = f.xref AND f.companies LIKE '%LAUBER%' SET i.split_case_charge=IF(f.size='750',2.40,IF(f.size='1500',4.80,2.40))";

    cw_csvxc_logged_query($sql);


    $sql = "UPDATE `$buff_table` AS i INNER JOIN $tables[datahub_BevAccessFeeds] AS f ON i.item_xref = f.xref AND (f.companies LIKE '%MSCOTT%' OR f.companies LIKE 'WINBOW%') SET i.split_case_charge=0.66";

    cw_csvxc_logged_query($sql);


}

function cw_dh_correct_x_UP_prod_links() {
    global $tables;

    global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

    $sql = "SELECT * FROM $tables[datahub_beva_UP_prod]";
    $result = mysqli_query($mysql_connection_id, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $temp = explode(' ', $row['skus']);
        $text = "0" . $row['skus'];
        $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
        $res = mysqli_query($mysql_connection_id, $sql);
        if(mysqli_num_rows($res) == 0) {
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_prod] SET skus = '$text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
                mysqli_query($mysql_connection_id, $sql);
            }

            $new_text = "0" . $text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$new_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_prod] SET skus = '$new_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
                mysqli_query($mysql_connection_id, $sql);
            }
            $third_text = "0" . $new_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$third_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_prod] SET skus = '$third_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
                mysqli_query($mysql_connection_id, $sql);
            }

            $fourth_text = "0" . $third_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$fourth_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_prod] SET skus = '$fourth_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
                mysqli_query($mysql_connection_id, $sql);
            }

            $fifth_text = "0" . $fourth_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where skus = '{$fifth_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_prod] SET skus = '$fifth_text' WHERE skus = '{$row['skus']}' AND prod_id = '{$row['prod_id']}'";
                mysqli_query($mysql_connection_id, $sql);
            }
        }
    }
}


function cw_dh_correct_x_UP_VPR_links() {
    global $tables;

    global $__mysql_connection_id;
    if (!isset($mysql_connection_id)) $mysql_connection_id = $__mysql_connection_id;

    $sql = "SELECT * FROM $tables[datahub_beva_UP_VPR]";
    $result = mysqli_query($mysql_connection_id, $sql);

    while ($row = mysqli_fetch_array($result)) {
        $text = "0" . $row['prod_item'];
        $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
        $res = mysqli_query($mysql_connection_id, $sql);

        if(mysqli_num_rows($res) == 0) {
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);

            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                //echo "$sql<br>";
                cw_csvxc_logged_query($sql);
                                        //$i++;
            }

            //else {
            $new_text = "0" . $text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$new_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$new_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                cw_csvxc_logged_query($sql);
                                                //$i++;
            }
            
            $third_text = "0" . $new_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$third_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$third_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                cw_csvxc_logged_query($sql);
            }

            $fourth_text = "0" . $third_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$fourth_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$fourth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                cw_csvxc_logged_query($sql);
                                    //$i++;
            }

            $fifth_text = "0" . $fourth_text;
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$fifth_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$fifth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                cw_csvxc_logged_query($sql);
                                                //$i++;
            }
            $sixth_text = $row['prod_item'] . 'NV1';
            $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$sixth_text}' AND prod_id = '{$row['prod_id']}'";
            $res = mysqli_query($mysql_connection_id, $sql);
            if(mysqli_num_rows($res) > 0) {
                $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '$sixth_text' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                cw_csvxc_logged_query($sql);
                                                //$i++;
            }

            if($row['prod_item'] == '0' && !empty($row['productid'])) {
                $sql = "SELECT * FROM $tables[datahub_beva_up_prod_xrefs] where prod_item = '{$row['productid']}' AND prod_id = '{$row['prod_id']}'";
                $res = mysqli_query($mysql_connection_id, $sql);
                if(mysqli_num_rows($res) > 0) {
                    $sql = "UPDATE $tables[datahub_beva_UP_VPR] SET prod_item = '{$row['productid']}' WHERE prod_item = '{$row['prod_item']}' AND prod_id = '{$row['prod_id']}'";
                    cw_csvxc_logged_query($sql);
                                                        //$i++;
                }
            }
        }
    }
}

function cw_datahub_load_beva_monthly($new_only, $is_interim=false) {
    global $tables, $config;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = '_interim';

    $search_prefilled = array();

    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 1000);
    $search_prefilled['page']           = ($page ? $page : 1);
    $search_prefilled['unserialize_fields'] = true;

    $all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));
//print_r($all_fi_profiles);
    $beva_UP_VPR_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_beva_UP_VPR']));
    $beva_UP_VPR_profile = reset($beva_UP_VPR_profiles);

    if (!empty($beva_UP_VPR_profile)) {

        $beva_UP_VPR_profile['recurring_import_path'] = cw_flexible_import_find_server_file($beva_UP_VPR_profile['recurring_import_path'], $interim_ext);
        if (!file_exists($beva_UP_VPR_profile['recurring_import_path'])) {
            cw_log_add(__FUNCTION__, array('Error'=>'Import file cant be found', 'filename'=>$beva_UP_VPR_profile['recurring_import_path']));   
            return 0;
        }

        if ($new_only) {
            $beva_UP_VPR_profile['file_hash'] = md5_file($beva_UP_VPR_profile['recurring_import_path']);
            $is_file_loaded_already = cw_query_first_cell("SELECT COUNT(*) FROM $tables[flexible_import_loaded_files_hash] WHERE profile_id='".$beva_UP_VPR_profile['id']."' AND hash='".$beva_UP_VPR_profile['file_hash']."'");

            if ($is_file_loaded_already)
                return 0;
        }
        $parsed_file = cw_flexible_import_run_profile($beva_UP_VPR_profile['id'], array($beva_UP_VPR_profile['recurring_import_path']));

        if (empty($beva_UP_VPR_profile['file_hash']))
            $beva_UP_VPR_profile['file_hash'] = md5_file($beva_UP_VPR_profile['recurring_import_path']);

        cw_array2update("flexible_import_profiles", array('recurring_last_run_date'=>time()), "id='$beva_UP_VPR_profile[id]'");
        cw_array2insert('flexible_import_loaded_files_hash', array( 'profile_id'=>$beva_UP_VPR_profile['id'],
                                                                    'hash'=>$beva_UP_VPR_profile['file_hash'],
                                                                    'date_loaded'=>time()));

        cw_dh_correct_x_UP_VPR_links();

        cw_csvxc_logged_query("UPDATE $tables[datahub_beva_UP_VPR] SET xref = CONCAT(trim(prod_id) , '-' , trim(prod_item))");

        cw_csvxc_logged_query("DELETE FROM $tables[datahub_beva_UP_VPR] WHERE wholesaler LIKE '%opici%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_BevAccessFeeds]");

        cw_csvxc_logged_query("INSERT INTO $tables[datahub_BevAccessFeeds] (xref)
                               SELECT DISTINCT v.xref AS xref
                               FROM $tables[datahub_beva_UP_VPR] AS v LEFT JOIN $tables[datahub_BevAccessFeeds] AS f ON v.xref = f.xref
                               WHERE isnull(f.xref) AND Instr('".$config['flexible_import']['BevA_do_not_import']."',v.wholesaler) = 0");


        cw_csvxc_logged_query("UPDATE $tables[datahub_BevAccessFeeds] AS f INNER JOIN $tables[datahub_beva_UP_VPR] 
                               AS v ON f.xref = v.xref SET f.xref = v.xref,
                                                f.bdesc = `v`.`bdesc`,
                                                f.descriptio = `v`.`descriptio`,
                                                f.`size` = `v`.`size`,
                                                f.vintage = `v`.`vintage`,
                                                f.univ_cat = `v`.`univ_cat`,
                                                f.lwbn = `v`.`lwbn`,
                                                f.apc = `v`.`apc`,
                                                f.bestbot = `v`.`bestbot`,
                                                f.`date` = `v`.`date`,
                                                f.botpercase = `v`.`botpercase`,
                                                f.secpack = `v`.`secpack`,
                                                f.wholesaler = `v`.`wholesaler`,
                                                f.prod_item = `v`.`prod_item`,
                                                f.upc = `v`.`upc`,
                                                f.case_price = `v`.`case_price`,
                                                f.bot_price = `v`.`bot_price`,
                                                f.front_nyc = `v`.`front_nyc`,
                                                f.postoff = `v`.`postoff`,
                                                f.spec_price = `v`.`spec_price`,
                                                f.ripcode = `v`.`ripcode`,
                                                f.qty1 = `v`.`qty1`,
                                                f.d_type1 = `v`.`d_type1`,
                                                f.discount1 = `v`.`discount1`,
                                                f.qty2 = `v`.`qty2`,
                                                f.d_type2 = `v`.`d_type2`,
                                                f.discount2 = `v`.`discount2`,
                                                f.qty3 = `v`.`qty3`,
                                                f.d_type3 = `v`.`d_type3`,
                                                f.discount3 = `v`.`discount3`,
                                                f.qty4 = `v`.`qty4`,
                                                f.d_type4 = `v`.`d_type4`,
                                                f.discount4 = `v`.`discount4`,
                                                f.qty5 = `v`.`qty5`,
                                                f.d_type5 = `v`.`d_type5`,
                                                f.discount5 = `v`.`discount5`,
                                                f.qty6 = `v`.`qty6`,
                                                f.d_type6 = `v`.`d_type6`,
                                                f.discount6 = `v`.`discount6`,
                                                f.qty7 = `v`.`qty7`,
                                                f.d_type7 = `v`.`d_type7`,
                                                f.discount7 = `v`.`discount7`,
                                                f.qty8 = `v`.`qty8`,
                                                f.d_type8 = `v`.`d_type8`,
                                                f.discount8 = `v`.`discount8`,
                                                f.qty9 = `v`.`qty9`,
                                                f.d_type9 = `v`.`d_type9`,
                                                f.discount9 = `v`.`discount9`,
                                                f.div1 = `v`.`div1`,
                                                f.div2 = `v`.`div2`,
                                                f.div3 = `v`.`div3`,
                                                f.div4 = `v`.`div4`,
                                                f.div5 = `v`.`div5`,
                                                f.div6 = `v`.`div6`,
                                                f.div7 = `v`.`div7`,
                                                f.div8 = `v`.`div8`,
                                                f.div9 = `v`.`div9`,
                                                f.div10 = `v`.`div10`,
                                                f.div11 = `v`.`div11`,
                                                f.div12 = `v`.`div12`,
                                                f.asst_size = `v`.`asst_size`,
                                                f.organic = `v`.`organic`,
                                                f.kosher = `v`.`kosher`,
                                                f.sparkling = `v`.`sparkling`,
                                                f.productid = `v`.`productid`,
                                                f.deposit = `v`.`deposit`,
                                                f.cale_shelf = `v`.`cale_shelf`,
                                                f.truevint = `v`.`truevint`,
                                                f.prod_id = `v`.`prod_id`,
                                                f.whole_desc = `v`.`whole_desc`,
                                                f.producer = `v`.`producer`,
                                                f.companies = v.wholesaler, 
                                                f.univ_prod = v.univ_prod");

         cw_dh_insert_beva();

         $sql = "DELETE FROM $tables[datahub_import_buffer] 
                        WHERE Source = 'Feed_BEVA'
                        AND (COALESCE(Wine, '') = '' AND COALESCE(Producer, '') = '' AND COALESCE(Producer, '') = '')";
         cw_csvxc_logged_query($sql);


        $res_str = "done";
    } else {
        $res_str = "ERROR: BevAccess_UP_VPR profile is not set up";
    }
    return 1;
}

function cw_datahub_load_beva_daily($new_only, $is_interim=true) {
    global $tables, $config;

    $interim_ext = '';
    if ($is_interim)
        $interim_ext = '_interim';

    $search_prefilled = array();

    $search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
    $search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
    $search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 1000);
    $search_prefilled['page']           = ($page ? $page : 1);
    $search_prefilled['unserialize_fields'] = true;

    $all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

    $beva_UP_prod_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_beva_UP_prod']));

    $beva_UP_prod_profile = reset($beva_UP_prod_profiles);

    if (!empty($beva_UP_prod_profile)) {

        $beva_UP_prod_profile['recurring_import_path'] = cw_flexible_import_find_server_file($beva_UP_prod_profile['recurring_import_path'], $interim_ext);
        if (!file_exists($beva_UP_prod_profile['recurring_import_path'])) {
            cw_log_add(__FUNCTION__, array('Error'=>'Import file cant be found', 'filename'=>$beva_UP_prod_profile['recurring_import_path']));
            return 0;
        }

        if ($new_only) {
            $beva_UP_prod_profile['file_hash'] = md5_file($beva_UP_prod_profile['recurring_import_path']);
            $is_file_loaded_already = cw_query_first_cell("SELECT COUNT(*) FROM $tables[flexible_import_loaded_files_hash] WHERE profile_id='".$beva_UP_prod_profile['id']."' AND hash='".$beva_UP_prod_profile['file_hash']."'");
            if ($is_file_loaded_already)
                return 0;
        }


        $parsed_file = cw_flexible_import_run_profile($beva_UP_prod_profile['id'], array($beva_UP_prod_profile['recurring_import_path']));

        if (empty($beva_UP_prod_profile['file_hash']))
            $beva_UP_prod_profile['file_hash'] = md5_file($beva_UP_prod_profile['recurring_import_path']);

        cw_array2update("flexible_import_profiles", array('recurring_last_run_date'=>time()), "id='$beva_UP_prod_profile[id]'");
        cw_array2insert('flexible_import_loaded_files_hash', array( 'profile_id'=>$beva_UP_prod_profile['id'],
                                                                    'hash'=>$beva_UP_prod_profile['file_hash'],
                                                                    'date_loaded'=>time()));

        cw_dh_correct_x_UP_prod_links();
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_beva_UP_prod] WHERE companies LIKE '%opici%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_BevAccessFeeds] WHERE companies LIKE '%opici%'");

        cw_csvxc_logged_query("DELETE FROM $tables[datahub_beva_UP_prod] WHERE companies LIKE '%WILDMN%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_BevAccessFeeds] WHERE companies LIKE '%WILDMN%'");

        cw_csvxc_logged_query("UPDATE $tables[datahub_beva_UP_prod] SET xref = CONCAT(rtrim(ltrim(coalesce(prod_id,' '))) , '-' , trim(Left(CONCAT(coalesce(skus,' ') , ' '),InStr(CONCAT(coalesce(skus,' ') , ' '),' '))))");

        cw_csvxc_logged_query("UPDATE $tables[datahub_BevAccessFeeds] SET current = '', confstock = ''");

        $sql = "update $tables[datahub_BevAccessFeeds] v inner join $tables[datahub_beva_UP_prod] p on v.univ_prod=p.univ_prod and (v.companies='DOMAIN' and p.companies='DOMAIN' and v.companies=p.companies) and p.vintage=v.vintage and ((p.skus LIKE concat('%',v.prod_item,'%')) OR (v.prod_item LIKE concat('%',p.skus,'%'))) and v.size/1000=p.size set v.xref=p.xref";
        cw_csvxc_logged_query($sql);

        $sql = "UPDATE $tables[datahub_BevAccessFeeds] AS f INNER JOIN $tables[datahub_beva_UP_prod] AS p ON f.xref = p.xref 
                                     SET f.vintage = `p`.`vintage`,
                                         f.prod_id = `p`.`prod_id`,
                                         f.companies = `p`.`companies`,
                                         f.status = `p`.`status`,
                                         f.bdesc = `p`.`bdesc`,
                                         f.botpercase = `p`.`botpercase`,
                                         f.descriptio = `p`.`descriptio`,
                                         f.univ_cat = `p`.`univ_cat`,
                                         f.reg_id = `p`.`reg_id`,
                                         f.truevint = `p`.`truevint`,
                                         f.use_vint = `p`.`use_vint`,
                                         f.grape = `p`.`grape`,
                                         f.kosher = `p`.`kosher`,
                                         f.organic = `p`.`organic`,
                                         f.prod_type = `p`.`prod_type`,
                                         f.importer = `p`.`importer`,
                                         f.cat_id = `p`.`cat_id`,
                                         f.type_id = `p`.`type_id`,
                                         f.rev = `p`.`rev`,
                                         f.des = `p`.`des`,
                                         f.wmn = `p`.`wmn`,
                                         f.rat = `p`.`rat`,
                                         f.fpr = `p`.`fpr`,
                                         f.tek = `p`.`tek`,
                                         f.rec = `p`.`rec`,
                                         f.txt = `p`.`txt`,
                                         f.tas = `p`.`tas`,
                                         f.lab = `p`.`lab`,
                                         f.bot = `p`.`bot`,
                                         f.pho = `p`.`pho`,
                                         f.log = `p`.`log`,
                                         f.oth = `p`.`oth`,
                                         f.lwbn = `p`.`lwbn`,
                                         f.producer = `p`.`producer`,
                                         f.cat_type = `p`.`cat_type`,
                                         f.reg_text = `p`.`reg_text`,
                                         f.sparkling = `p`.`sparkling`,
                                         f.rp = `p`.`rp`,
                                         f.we = `p`.`we`,
                                         f.ws = `p`.`ws`,
                                         f.`current` = `p`.`current`,
                                         f.fortified = `p`.`fortified`,
                                         f.dessert = `p`.`dessert`,
                                         f.closure = `p`.`closure`,
                                         f.pack = `p`.`pack`,
                                         f.packaging = `p`.`packaging`,
                                         f.packtype = `p`.`packtype`,
                                         f.skus = `p`.`skus`,
                                         f.syn = `p`.`syn`,
                                         f.tstamp = `p`.`tstamp`,
                                         f.confstock = `p`.`confstock`,
                                         f.whvint = `p`.`whvint`,
                                         f.univ_prod = `p`.`univ_prod`";
        cw_csvxc_logged_query($sql);

        cw_dh_insert_beva();
        cw_dh_update_beva_inventory();
        cw_dh_apply_splitcase_to_cost();
        $res_str = "done";

    } else {
        $res_str = "ERROR: BevAccess_UP_prod profile is not set up";
    }
    return 1;
}
