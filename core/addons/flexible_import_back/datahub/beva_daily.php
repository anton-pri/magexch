<?php

set_time_limit(86400);

cw_include('addons/flexible_import/include/func.datahub_beva.php');

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





print("<h1>BevAccess Daily Update script</h1><br />");

$search_prefilled = array();

$search_prefilled['sort_field']     = ($sort && $sort !=""? $sort : "id");
$search_prefilled['sort_direction'] = ($sort_direction && $sort_direction!=0 ? 0 : 1);
$search_prefilled['items_per_page'] = ($items_per_page ? $items_per_page : 20);
$search_prefilled['page']           = ($page ? $page : 1);
$search_prefilled['unserialize_fields'] = true;

$all_fi_profiles = cw_call('cw_flexible_import_get_profiles', array('params'=>$search_prefilled));

$beva_UP_prod_profiles = cw_call('cw_datahub_filter_profiles', array($all_fi_profiles, $tables['datahub_beva_UP_prod']));

$beva_UP_prod_profile = reset($beva_UP_prod_profiles);

if (!empty($beva_UP_prod_profile)) {

    if (!file_exists($beva_UP_prod_profile['recurring_import_path']))
        $beva_UP_prod_profile['recurring_import_path'] = rtrim($config['flexible_import']['flex_import_files_folder'],'/').'/'.$beva_UP_prod_profile['recurring_import_path'];

    if (file_exists($beva_UP_prod_profile['recurring_import_path'])) {
        $parsed_file = cw_flexible_import_run_profile($beva_UP_prod_profile['id'], array($beva_UP_prod_profile['recurring_import_path']));


        cw_dh_correct_x_UP_prod_links();
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_beva_UP_prod] WHERE companies LIKE '%opici%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_BevAccessFeeds] WHERE companies LIKE '%opici%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_beva_UP_prod] WHERE companies LIKE '%WILDMN%'");
        cw_csvxc_logged_query("DELETE FROM $tables[datahub_BevAccessFeeds] WHERE companies LIKE '%WILDMN%'");

        cw_csvxc_logged_query("UPDATE $tables[datahub_beva_UP_prod] SET xref = CONCAT(rtrim(ltrim(coalesce(prod_id,' '))) , '-' , trim(Left(CONCAT(coalesce(skus,' ') , ' '),InStr(CONCAT(coalesce(skus,' ') , ' '),' '))))");

        cw_csvxc_logged_query("UPDATE $tables[datahub_BevAccessFeeds] SET current = '', confstock = ''");

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
      $res_str = "ERROR: Import file $beva_UP_prod_profile[recurring_import_path] was not found";
    }
} else {
    $res_str = "ERROR: BevAccess_UP_prod profile is not set up";
}


print("<h3>$res_str...</h3><a href='index.php?target=datahub_buffer_match'>Return to main edit page</a>");
die;

