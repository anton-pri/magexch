<?php
@set_time_limit(86400);
cw_load('category');
//print($config['custom_magazineexchange']['magexch_default_root_category']);
$all_cats = cw_query_column("select category_id from $tables[categories] where parent_id != 0 order by category_id desc");
$all_cats[] = $config['custom_magazineexchange']['magexch_default_root_category'];
$all_cats = array_reverse($all_cats);

foreach ($all_cats as $cid)
    cw_category_update_path($cid);

print('cw_category_update_path is done!');die;
