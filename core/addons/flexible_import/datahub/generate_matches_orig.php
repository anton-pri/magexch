<?php

set_time_limit(86400);

global $analysed_fields_main; 
$analysed_fields_main = array('Producer', 'name', 'Vintage', 'Size', 'Region', 'country', 'keywords', 'varietal', 'Appellation', 'sub_appellation');

global $analysed_fields_buffer; 
$analysed_fields_buffer = array('Wine', 'Producer', 'Name', 'Vintage', 'size', 'country', 'Region', 'varietal', 'Appellation', 'sub-appellation');

function cw_dh_unified_size ($vol_str) { 
   if (!empty($vol_str)) {
        $vol_str = strtolower($vol_str);
        if (strpos($vol_str, "ltr") !== false) {
           $num = 1000*floatval((str_replace("ltr","",$vol_str)));
        } elseif (strpos($vol_str, "ml") !== false) {
           $num = intval(str_replace("ml","",$vol_str));
        }
    }
    return $num;
}


Function cw_dh_ArrayMergeKeepKeys() {
      $arg_list = func_get_args();
      foreach((array)$arg_list as $arg){
          foreach((array)$arg as $K => $V){
              $Zoo[$K]=$V;
          }
      }
    return $Zoo;
}

function cw_dh_analysed_fields_qry($analysed_fields) {
    $analysed_fields_qry = array();
    foreach ($analysed_fields as $f) {
        $analysed_fields_qry[] = "COALESCE(`$f`,'')";
    }
    return $analysed_fields_qry;
}

function cw_dh_replace_chars($str) {
    $str = str_replace(array('`','-','\'','#', '"', '(', ')', ']', '['), '', $str);
    $str = str_replace(array('.','!','?',',','\\'), ' ', $str); 
    return $str;
}

function cw_dh_addreplaces($str) {
    $repl = array('' => array('`','-','\'','#', '"', '(', ')', ']', '['), ' '=>array('.','!','?',',','\\'));
 
    foreach ($repl as $to_chr => $from_arr) {
        foreach ($from_arr as $from_chr) {
            $str = "REPLACE($str, '".addslashes($from_chr)."', '$to_chr')";
        }  
    }
    return $str; 
}

if ($rebuild_search_cache || 1) {
    $main_cache_qry = "REPLACE INTO $tables[datahub_match_search_cache] (ID, words) SELECT ID, ".cw_dh_addreplaces("LOWER(CONCAT(' ',".implode(", ' ' ,", cw_dh_analysed_fields_qry($analysed_fields_main)).",' '))")." as row FROM $tables[datahub_main_data]";

    print($main_cache_qry."<br>"); 
    db_query($main_cache_qry);
    print("Search cache is rebuilt<br><br>\n\n");
}

function cw_dh_get_buffer_item_data($buffer_table_id) {
    global $tables, $analysed_fields_buffer;
    return cw_query_first("SELECT `".implode('`,`', $analysed_fields_buffer)."` FROM $tables[datahub_import_buffer] WHERE table_id = $buffer_table_id");
}


function cw_dh_gen_matches($buffer_table_id, $buffer_item_data, &$result_score, $limit=3) {
    global $dh_debug, $tables, $analysed_fields_buffer, $analysed_fields_main;
    global $analysed_words;

    
    $buffer_item_data['size'] = cw_dh_unified_size($buffer_item_data['size']);

    if ($dh_debug) {
        print('<br><br>');
        print_r(array('buffer_item_data' => $buffer_item_data));
        print('<br><br>');
        print_r(array('analysed_fields_buffer' => $analysed_fields_buffer));
    }

    $b_product_words = cw_query_first_cell($s = "SELECT ".cw_dh_addreplaces("LOWER(CONCAT(".implode(", ' ' ,", cw_dh_analysed_fields_qry($analysed_fields_buffer))."))")." as row FROM $tables[datahub_import_buffer] WHERE table_id = '$buffer_table_id'");

    if ($dh_debug)
        print("<br><br>b_product_words: $s <br><br>".$b_product_words);

    $b_words = explode(' ', $b_product_words);

    foreach ($buffer_item_data as $f_name => $f_val) {
        $f_val_words = explode(' ', $f_val);
        if (count($f_val_words) < 2) continue;

        if (count($f_val_words) > 2) {   
            $slice_pos = 0;
            do {
                $slice_pos++;
                $words_sub_arr = array_slice($f_val_words, $slice_pos);
                $b_words[] = cw_dh_replace_chars(implode(' ', $words_sub_arr));  

                $words_sub_arr2 = array_slice($f_val_words, 0, count($f_val_words) - $slice_pos); 
                $b_words[] = cw_dh_replace_chars(implode(' ', $words_sub_arr2));

            } while (count($words_sub_arr) > 1); 
        } else {
            $b_words[] = cw_dh_replace_chars($f_val);
        }
    }   

    $b_words = array_unique($b_words);

    if ($dh_debug) {
        print('<br><br>');
        print_r(array('b_words' => $b_words));
    }

    $b_words_weights = array();
    foreach ($b_words as $wrd) {
        $wrd = trim($wrd);
        if (strlen($wrd) < 3) continue;
        if (!isset($b_words_weights[$wrd])) {
            if (!isset($analysed_words[$wrd])) {
                $b_words_weights[$wrd] = cw_query_first_cell("select count(*) from $tables[datahub_match_search_cache] where words like '% $wrd %' or words like '$wrd %' or words like '% $wrd'");
                cw_array2insert('datahub_words_weight', array('word'=>$wrd, 'weight'=>$b_words_weights[$wrd]), true);
                $analysed_words[$wrd] = $b_words_weights[$wrd]; 
            } else {
                $b_words_weights[$wrd] = $analysed_words[$wrd];
            }
        }
    }

    $all_items_count = cw_query_first_cell("select count(*) from $tables[datahub_main_data]");

    if ($dh_debug) { print('<br><br>');
        print_r($b_words_weights);
    }

    $likes_by_words = array();
    foreach ($b_words_weights as $wrd => $weight) {
        $priority = floatval($all_items_count)/$weight['weight'];
        $likes_by_words[] = "IF(dmsc.words LIKE '%$wrd%',".floatval($priority).", 0)";
    }

    if ($dh_debug) { print('<br><br>');
        print_r($likes_by_words);
    }

    $likeness_field = implode("+",$likes_by_words)." as likeness";

    //INNER JOIN $tables[datahub_main_data] dmd ON dmd.ID=dmsc.ID AND dmd.Size='$buffer_item_data[size]' AND dmd.Vintage='$buffer_item_data[Vintage]'

    $limit_temp = 50;

    $_match_search = cw_query_hash($s = "SELECT dmsc.ID, $likeness_field FROM $tables[datahub_match_search_cache] dmsc having likeness > 1000 ORDER BY likeness DESC LIMIT $limit_temp", 'ID', false);

    if ($dh_debug) {
        print('<br><br>'); 
        print($s); 
        print('<br><br>');
        print_r(array('_match_search'=>$_match_search));
    }


    $match_search = array();
    $reserve_matches = array();
    $reserve_matches2 = array();

    $max_likeness = 0;
    foreach ($_match_search as $ms_ID => $ms_likeness) {
        if ($ms_likeness['likeness'] > $max_likeness) $max_likeness = $ms_likeness['likeness'];  
    }  

    foreach ($_match_search as $ms_ID => $ms_likeness) {
        $reserve = false;

        $main_item_data = cw_query_first("SELECT '$ms_ID' as ID, '$ms_likeness[likeness]' as likeness, `".implode('`,`', $analysed_fields_main)."` FROM $tables[datahub_main_data] WHERE ID='$ms_ID'");

        $main_item_data['Size'] = cw_dh_unified_size($main_item_data['Size']);

        if ($ms_likeness['likeness'] > $max_likeness*0.95) {
            if ($main_item_data['Vintage'] == $buffer_item_data['Vintage'] && $main_item_data['Size'] == $buffer_item_data['size']) {
                $reserve_matches[$ms_ID] = $ms_likeness;
                $reserve = true; 
            } elseif ($main_item_data['Vintage'] == $buffer_item_data['Vintage'] || $main_item_data['Size'] == $buffer_item_data['size']) {
                $reserve_matches2[$ms_ID] = $ms_likeness;
                $reserve = true;
            } 
        }

        if (!$reserve)  
            $match_search[$ms_ID] = $ms_likeness;

/*
        $main_item_data['Size'] = cw_dh_unified_size($main_item_data['Size']);

        if ($main_item_data['Vintage'] == $buffer_item_data['Vintage'] && $main_item_data['Size'] == $buffer_item_data['size']) { 
            $match_search[$ms_ID] = $ms_likeness;
        } else {
            if ($main_item_data['Vintage'] == $buffer_item_data['Vintage'] || $main_item_data['Size'] == $buffer_item_data['size']) {
                if (sizeof($reserve_matches) < $limit+3) 
                    $reserve_matches[$ms_ID] = $ms_likeness;      
            } else {
                if (sizeof($reserve_matches2) < $limit+3) 
                    $reserve_matches2[$ms_ID] = $ms_likeness; 
            } 

            continue;
        }

        if ($dh_debug) { print('<br><br>');
            print_r(array('mid'=>$main_item_data));
        }
*/
        if ((sizeof($match_search) + sizeof($reserve_matches) + sizeof($reserve_matches2)) == $limit) break;

    }

    if ($dh_debug) { print('<hr><br><br>');
        print_r(array('reserve_matches'=>$reserve_matches));
    }

    if ($dh_debug) { print('<hr><br><br>');
        print_r(array('reserve_matches2'=>$reserve_matches2));
    }

    if ($dh_debug) { print('<hr><br><br>');
        print_r(array('match_search'=>$match_search));
    }


    $result = array(); 
    foreach ($reserve_matches as $r_k => $r_v) {
        $result[$r_k] = $r_v;
    }
    foreach ($reserve_matches2 as $r2_k => $r2_v) {
        $result[$r2_k] = $r2_v;
    }
    foreach ($match_search as $ms_k => $ms_v) {
        $result[$ms_k] = $ms_v;
    }

    if ($dh_debug) { print('<hr><br><br>');
        print_r(array('result'=>$result));
    }


/*
    if (count($match_search) == 0) {
        $match_search = $reserve_matches; 
        $result_score = 2;
    }

    if (count($match_search) == 0) {
        $match_search = $reserve_matches2;
        $result_score = 1;
    }

    if (count($match_search) == 0) {
        $result_score = 0;
    }
*/
    return $result;
}

  $result_score_names = array(0=>'No Matches', 1=>'Poor', 2=>'Average', '3'=>'Good');

  $buffer_ids = cw_query_column("select table_id from $tables[datahub_import_buffer] order by table_id asc",'table_id');

  global $analysed_words; 
  $analysed_words = cw_query_hash("select * from $tables[datahub_words_weight]",'word', false);

  global $dh_debug;
  //$dh_debug = true;

  if ($new_only) {
      $qry_where = " where `Match Items` = '' and item_xref not in (select item_xref from $tables[datahub_match_links]) ";
  }
  
  if (isset($tbl_id))  
      $qry_where = " where table_id=$tbl_id";

  if (isset($gen_limit))  
      $qry_limit = "limit ".intval($gen_limit);

  $buffer_ids = cw_query_column("select table_id from $tables[datahub_import_buffer] $qry_where order by table_id asc $qry_limit",'table_id');
  foreach($buffer_ids as $k=> $buffer_table_id) {
      $item_data = cw_dh_get_buffer_item_data($buffer_table_id); 
      cw_flush("<hr><b>Looking for matches for item #$buffer_table_id:</b> <br/>");

      $display_item_data = array();
      foreach($item_data as $fld=>$fld_val) {
          if (!empty($fld_val)) $display_item_data[] = $fld_val;
      } 

      cw_flush(implode(', ', $display_item_data)." <br/>\n");
      $result_score = 3;
      $ms = cw_dh_gen_matches($buffer_table_id, $item_data, $result_score, 4);

      cw_flush("Result score: ".$result_score_names[$result_score]." <br />");

      if ($result_score) 
          cw_flush("Suggested datahub items: ".implode(", ",array_keys($ms)));    
 
      db_query("update $tables[datahub_import_buffer] set `Match Items`='".implode(",",array_keys($ms))."' where table_id='$buffer_table_id'");
  }

cw_flush("<p /><a href='index.php?target=datahub_buffer_match'>Return to 'Match Imported Items' tab</a>");

//cw_header_location("index.php?target=datahub_buffer_match");


/*

$analysed_data = cw_query("SELECT LOWER(CONCAT(".implode(", ' ' ,",$analysed_fields).")) as row FROM $tables[datahub_main_data] ORDER BY ID ASC LIMIT  110000, 20000");
//print_r($analysed_data);

$analysed_words = cw_query_hash("select * from $tables[datahub_words_weight]",'word', false);

if (!empty($analysed_words)) {
    print_r(array('av1'=>count($analysed_words)));
    print("<br /><br />");
}

foreach ($analysed_data as $d) {
    $str = str_replace(array('.','`','-','!','?','\'','#'),'',str_replace(',',' ', $d['row']));
    $words = explode(' ', $str);
    $words = array_map('trim', $words);
    $lcl_count = 0;
    foreach ($words as $wrd) {
        $wrd = trim($wrd);
        if ($wrd != '' && !in_array($wrd, array_keys($analysed_words))) {
            $lcl_count++; 
            $analysed_words[$wrd] = array('weight' => cw_query_first_cell("select count(*) from $tables[datahub_main_data] where LOWER(CONCAT(".implode(", ' ' ,",$analysed_fields).")) like '%$wrd%'"), 'new'=>true);
        }
    }
    //print($str);
    cw_flush(".$lcl_count ");
}

//print_r($analysed_words);

foreach ($analysed_words as $word => $w) {
    if ($w['new'])  
        cw_array2insert('datahub_words_weight', array('word'=>$word, 'weight'=>$w['weight']), 'true');
}
*/
exit;
