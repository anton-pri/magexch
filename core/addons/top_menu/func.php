<?php
function get_cat_location_helper($c_id, $c_type) 
{
      global $tables, $u_location;
      if ($c_type == 'u') {
        $parent_cat   =  cw_query_first("select parent_id_tag as prefix,parent_id as parent_id from {$tables['top_menu_user_categories']} where category_id=$c_id;"); 
      }
      if ($c_type == 'p') {
        $parent_cat   =  cw_query_first("select parent_id from {$tables['categories']} where category_id=$c_id;");
        $parent_cat['prefix'] = 'p';
      }
      array_push($u_location, $parent_cat);

      if ($parent_cat['parent_id'])
         get_cat_location_helper($parent_cat['parent_id'], $parent_cat['prefix']);
      return $u_location;
}

function cw_top_menu_location($id)
{

      global $tables, $u_location;
      $u_location = array();
      $prefix = substr($id, 0, 1);
      $cat_id = intval(str_replace($prefix, '', $id));

      get_cat_location_helper($cat_id, $prefix);
      foreach ($u_location as $loc) {
        extract($loc);
        if ($prefix == 'u')
          $category_name_temp = cw_query_first("select category from {$tables['top_menu_user_categories']} where category_id=$parent_id;");
        if ($prefix == 'p')
          $category_name_temp = cw_query_first("select category from {$tables['categories']} where category_id=$parent_id;");
        $category_name = $category_name_temp['category'];
        if ($parent_id != 0)
          $locations[] = compact('parent_id', 'category_name', 'prefix');
      } 
      if ($locations)
        return array_reverse($locations);
}

function cw_top_menu_make_sql_query($parent = 0)
{
    global $tables, $app_web_dir;
    cw_load('category');
    if (APP_AREA == 'customer') {
      $pcats_ = cw_func_call('cw_category_search', array(
        'data' => array(
            'active' => 1,
            'all' => 1
        )
      ));
    } else {
      $pcats_ = cw_func_call('cw_category_search', array(
        'data' => array(
            'active' => 1,
            'parent_id' => $parent,
            'all' => 1
        )
      ));
    }
    $pcats_ = $pcats_[0];
    
    foreach ($pcats_ as $v) {
        extract($v);
        $mid  = 'p' . $category_id;
        $pmid = $parent_id;
        if ($pmid != 0)
            $pmid = 'p' . $pmid;
        $title  = $category;
        $pcount = $product_count_web;
        //$link="$app_web_dir/?target=index&cat=$category_id";
        $link   = cw_call('cw_core_get_html_page_url', array(array(
            'var' => 'index',
            'cat' => $category_id
        )));
        $pos    = $tm_pos;
        $active = $tm_active;
        $title2 = $tm_title;
        $type   = 'pcat';  
        $sub_sql_p   = "select category_id from {$tables['categories']} where parent_id=$category_id;";
        $sub_sql_u   = "select category_id from {$tables['top_menu_user_categories']} where parent_id=$category_id;";
        $subcategories_p = preg_filter('/^/', 'p', cw_query_column($sub_sql_p));
        $subcategories_u = preg_filter('/^/', 'u', cw_query_column($sub_sql_u));
        $subcategories = array_merge($subcategories_u, $subcategories_p);
        $location = cw_top_menu_location($mid);
        if (APP_AREA != 'customer' || $active != 0)
            $pcats[] = compact('mid', 'pmid', 'title', 'pcount', 'link', 'pos', 'active', 'title2', 'type', 'subcategories', 'location');
    }
    unset($pcats_);
    
    $fields = "CONCAT('u',category_id) as mid, CONCAT(parent_id_tag,parent_id) as pmid, category as title,\n";
    $fields .= "0 as pcount, link, pos, active, '' as title2, 'ucat' as type\n";
    if (APP_AREA == 'customer')
        $where = "where active=1";
    else
        $where = "where parent_id=$parent";

    $sql   = "select $fields from {$tables['top_menu_user_categories']} $where;";
    $ucats_ = cw_query($sql);
    
    foreach ($ucats_ as $v) {
        extract($v);
        $parent_u = intval(str_replace('u', '', $mid));
        $sub_sql_u   = "select category_id from {$tables['top_menu_user_categories']} where parent_id=$parent_u;";
        $subcategories = preg_filter('/^/', 'u', cw_query_column($sub_sql_u));
        $location = cw_top_menu_location($mid);
        if (APP_AREA != 'customer' || $active != 0)
            $pcats[] = compact('mid', 'pmid', 'title', 'pcount', 'link', 'pos', 'active', 'title2', 'type', 'subcategories', 'location');
    }

    unset($ucats_);

    if (count($ucats) == 0) 
      $ucats = array();
    if (count($pcats) == 0) 
      $pcats = array(); 
    $cats = array_merge($pcats, $ucats);
    foreach ($cats as $k => $v)
        $sorter[$k] = $v['pos'];
    array_multisort($sorter, $cats);
    
    return $cats;
}

function cw_top_menu_update($data)
{
    global $tables;
    
    $data = explode("\n", trim($data));
    foreach ($data as $v)
        if (trim($v) != '') {
            list($op, $id, $pos, $active, $title, $title_orig, $link) = explode("---", trim($v));
            if ($op == "update" && preg_match("'^p[0-9]*$'", $id)) {
                $mid = intval(str_replace('p', '', $id));
                if ($title == $title_orig)
                    $title = '';
                $tbl       = $tables['categories'];
                $where     = "category_id=$mid";
                $tm_pos    = $pos;
                $tm_active = $active;
                $tm_title  = $title;
                $arr       = compact('tm_pos', 'tm_active', 'tm_title');
                $upd[]     = compact('tbl', 'where', 'arr');
            }
            if ($op == "update" && preg_match("'^u[0-9]*$'", $id)) {
                $mid      = intval(str_replace('u', '', $id));
                $tbl      = $tables['top_menu_user_categories'];
                $where    = "category_id=$mid";
                $category = $title;
                $link     = url2link($link);
                $arr      = compact('pos', 'active', 'category', 'link');
                $upd[]    = compact('tbl', 'where', 'arr');
            }
            if ($op == "add" && trim($title) != '') {
                $pid = trim($id);
                $re  = "'^([a-z])([0-9]*)$'i";
                if (preg_match($re, $pid) || $pid == '0') {
                    if ($pid != '0') {
                        $pid_tag = preg_replace($re, "$1", $pid);
                        $pid     = intval(preg_replace($re, "$2", $pid));
                    } else
                        $pid_tag = '';
                    $link = url2link($link);
                    
                    $tbl           = $tables['top_menu_user_categories'];
                    $category      = $title;
                    $link          = url2link($link);
                    $parent_id_tag = $pid_tag;
                    $parent_id     = $pid;
                    $arr           = compact('parent_id_tag', 'parent_id', 'category', 'link', 'pos', 'active');
                    $add[]         = compact('tbl', 'arr');
                }
            }
            if ($op == "remove") {
                $set   = str_replace('u', '', $id);
                $del[] = "delete from {$tables['top_menu_user_categories']} where category_id in $set;";
            }
        }
    
    if (isset($upd) && is_array($upd))
        foreach ($upd as $v)
            cw_array2update($v['tbl'], $v['arr'], $v['where']);
    if (isset($add) && is_array($add))
        foreach ($add as $v)
            cw_array2insert($v['tbl'], $v['arr']);
    if (isset($del) && is_array($del))
        foreach ($del as $v)
            db_query($v);

}



function url2link($url)
{
    global $app_config_file;
    $app_web_dir;
    extract($app_config_file['web']);
    $url = trim($url);
    
    if (preg_match("'^[^/]'", $url) && !preg_match("'[a-z]+\:'i", $url))
        $url = '/' . $url;
    if (preg_match("'^/{2,}'", $url))
        $url = preg_replace("'^/{2,}'", "/", $url);
    if ($url == '')
        $url = '/';
    $re1 = "'^http\://" . str_replace('.', '\.', $http_host) . $app_web_dir . "'i";
    $re2 = "'^" . $app_web_dir . "'i";
    if ($http_host != '' && preg_match($re1, $url))
        $ret = preg_replace($re1, "", $url);
    else if ($app_web_dir != '' && preg_match($re2, $url))
        $ret = preg_replace($re2, "", $url);
    else
        $ret = $url;
    return $ret;
}

function link2url($link)
{
    global $app_web_dir;
    if (preg_match("'^/'", $link))
        return $app_web_dir . $link;
    else
        return $link;
}



function top_menu_process($r, $pid = '0', $lev = 1, $path_to = '')
{
    $r2 = array();
    foreach ($r as $v)
        if ($v['pmid'] == $pid)
            $r2[] = $v;
    if (empty($r2))
        return array();
    foreach ($r2 as $v) {
        extract($v);
        $title_orig = $title;
        if ($title2 != '')
            $title = $title2;
        $children = '';
        if ($type == 'ucat')
            $link = link2url($link);
        if (preg_match("'^[a-z]*\://.*'i", $link))
            $target = " target=\"_blank\"";
        else
            $target = "";
        if (APP_AREA == 'customer') {
          $subitems = top_menu_process($r, $mid, $lev + 1, "$path_to$title &#187;");
          if (APP_AREA == 'customer' && $pid == '0' && empty($subitems) && $pcount > 0)
              $subitems['pseudo'] = array(
                  'title' => "$pcount " . cw_get_langvar_by_name('prod_fnd_ins') . " $title &#187;",
                  'link' => $link,
                  'subitems' => array()
              );
          $pcount += $subitems['tcount'];
          unset($subitems['tcount']);
          $children .= "\t" . $subitems['allchildren'];
          unset($subitems['allchildren']);
        
          $children = preg_replace("'\t+'", ",", trim($children));
        } else {
          $subitems_ajax = $subcategories;

          $children = implode(",", $subitems_ajax);
          $children = preg_replace("'\t+'", ",", trim($children));
        }
        if (APP_AREA == 'customer')
            $items[$mid] = compact('title', 'link', 'subitems', 'pcount', 'target', 'type', 'lev');
        else
            $items[$mid] = compact('pmid', 'lev', 'title', 'link', 'title_orig', 'subitems_ajax', 'path_to', 'pcount', 'active', 'pos', 'children', 'type');
        if ($pid != '0')
            if (!isset($items['tcount']))
                $items['tcount'] = $pcount;
            else
                $items['tcount'] += $pcount;
        if ($pid != '0')
            if (!isset($items['allchildren']))
                $items['allchildren'] = "$children\t$mid";
            else
                $items['allchildren'] .= "\t$children\t$mid";
    }
    return $items;
}


function sub_menu_process($r, $pid = '0', $lev = 2, $path_to = '')
{
    foreach ($r as $v) {
        extract($v);
        $title_orig = $title;
        if ($title2 != '')
            $title = $title2;
        $children = '';
        if ($type == 'ucat')
            $link = link2url($link);
        if (preg_match("'^[a-z]*\://.*'i", $link))
            $target = " target=\"_blank\"";
        else
            $target = "";

        $subitems_ajax = $subcategories;
        $children = implode(",", $subitems_ajax);
        $children = preg_replace("'\t+'", ",", trim($children));

        $items[$mid] = compact('pmid', 'lev', 'title', 'link', 'title_orig', 'subitems_ajax', 'path_to', 'pcount', 'active', 'pos', 'children', 'type', 'location');
        if ($pid != '0')
            if (!isset($items['tcount']))
                $items['tcount'] = $pcount;
            else
                $items['tcount'] += $pcount;
        if ($pid != '0')
            if (!isset($items['allchildren']))
                $items['allchildren'] = "$children\t$mid";
            else
                $items['allchildren'] .= "\t$children\t$mid";
    }
    return $items;
}


function top_menu_smarty_init() {

    $key = '_only_cleanup_rebuild_';

    if (!($top_menu = cw_cache_get($key, 'top_menu'))) {
        $top_menu = top_menu_process(cw_top_menu_make_sql_query()); 
        cw_cache_save($top_menu, $key, 'top_menu');
    }

    return $top_menu;
}
