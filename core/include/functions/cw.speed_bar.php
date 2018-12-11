<?php
function cw_speed_bar_search($params, $return = null) {
    extract($params);

    global $tables, $current_language;

    $fields = $from_tbls = $query_joins = $where = $groupbys = $having = $orderbys = array();

    $orderbys[] = "$tables[speed_bar].orderby";

    $from_tbls[] = 'speed_bar';
    $fields[] = "$tables[speed_bar].*";

# kornev, merge standart and additional variables
    if ($return)
    foreach ($return as $saname => $sadata)
        if (isset($$saname) && is_array($$saname) && empty($$saname)) $$saname = $sadata;

    $language = $language?$language:$current_language;
    $query_joins['speed_bar_lng'] = array(
        'on' => "$tables[speed_bar_lng].item_id = $tables[speed_bar].item_id AND $tables[speed_bar_lng].code = '$language'",
        'only_select' => 1,
    );
    $fields[] = "IFNULL($tables[speed_bar_lng].title, $tables[speed_bar].title) as title";

    if (isset($data['active']))
        $where[] = "$tables[speed_bar].active='$data[active]'";

    $search_query = cw_db_generate_query($fields, $from_tbls, $query_joins, $where, $groupbys, $having, $orderbys);
    return cw_query($search_query);
}

function cw_speed_bar_get_one($item_id, $language, $where = '') {
    global $tables;

    return cw_query_first($sql="select sb.*, IFNULL(sbl.title, sb.title) as title from $tables[speed_bar] as sb left join $tables[speed_bar_lng] as sbl on sbl.item_id=sb.item_id and sbl.code='$language' where sb.item_id='$item_id'".($where?"and $where":'')." order by orderby");
}


function cw_speed_bar_delete($item_id) {
    global $tables;

    db_query("delete from $tables[speed_bar] where item_id='$item_id'");
    db_query("delete from $tables[speed_bar_lng] where item_id='$item_id'");
    cw_call('cw_attributes_cleanup', array($item_id,'B'));
    
}

