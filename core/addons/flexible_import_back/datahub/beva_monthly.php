<?php

set_time_limit(86400);

cw_include('addons/flexible_import/include/func.datahub_beva.php');

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


print("<h1>BevAccess Monthly script</h1><br />");

$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;

$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));
//print_r($all_fi_profiles);
$beva_UP_VPR_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_beva_UP_VPR']));
$beva_UP_VPR_profile = reset($beva_UP_VPR_profiles);

if (!empty($beva_UP_VPR_profile)) {

    if (!file_exists($beva_UP_VPR_profile['recurring_import_path'])) 
        $beva_UP_VPR_profile['recurring_import_path'] = rtrim($config['flexible_import']['flex_import_files_folder'],'/').'/'.$beva_UP_VPR_profile['recurring_import_path'];

    if (file_exists($beva_UP_VPR_profile['recurring_import_path'])) {
        $parsed_file = cw_flexible_import_run_profile($beva_UP_VPR_profile['id'], array($beva_UP_VPR_profile['recurring_import_path']));

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
                                                f.companies = v.wholesaler");

         cw_dh_insert_beva();

         $sql = "DELETE FROM $tables[datahub_import_buffer] 
                        WHERE Source = 'Feed_BEVA'
                        AND (COALESCE(Wine, '') = '' AND COALESCE(Producer, '') = '' AND COALESCE(Producer, '') = '')";
         cw_csvxc_logged_query($sql);


        $res_str = "done";
    } else {
        $res_str = "ERROR: BevAccess_UP_VPR file $beva_UP_VPR_profile[recurring_import_path] does not exist";
    }

} else {
    $res_str = "ERROR: BevAccess_UP_VPR profile is not set up";
}

print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to main edit page</a>");
die;

