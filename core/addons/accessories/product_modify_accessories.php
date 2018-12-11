<?php

global $product_id, $mode, $action;

if (empty($product_id)) return;

$order_by_step = 10;
$js_tab_name = $link_type==1 ? 'accessories' : 'upselling';
$add_linked_products_link = 'index.php?target=products&mode=details&product_id=' . $product_id . '&js_tab=' . $js_tab_name;
$update_product_accessories_config_link = 'index.php?target=products&mode=details&product_id='.$product_id;

if ($REQUEST_METHOD == 'POST' && !empty($product_id)) {
  switch ($action) {
    case 'add_linked_product':
      $linked_product_ids = trim($linked_product_ids);
      if (strlen($linked_product_ids) == 0) {
        $top_message = array(
          'type'    => 'E',
          'content' => cw_get_langvar_by_name('msg_ac_err_no_linked_products_selected')
        );
        cw_header_location($add_linked_products_link);
      }
      $linked_product_ids = explode(' ', trim($linked_product_ids));
      if (!empty($linked_product_ids) && is_array($linked_product_ids)) {
        $linked_product_ids = array_map('trim', $linked_product_ids);
        $linked_product_ids = array_map('intval', $linked_product_ids);
        $linked_product_options['orderby']               = intval($linked_product_options['orderby']);
        $linked_product_options['is_bidirectional_link'] = empty($linked_product_options['is_bidirectional_link']) ? false : true;
        $linked_product_options['active']                = empty($linked_product_options['active']) ? 'N' : 'Y';
        $start_order_by  = $linked_product_options['orderby'];
        $next_order_by   = (floor($linked_product_options['orderby'] / $order_by_step) + 1) * $order_by_step;
        $order_by_offset = $next_order_by - $linked_product_options['orderby'];
        $next_order_by   = $start_order_by;
        $i = ($order_by_offset == 0 ? 1 : 0);
        foreach ($linked_product_ids as $linked_product_id) {
          cw_array2insert(
            'linked_products',
            array(
              'product_id'        => $product_id,
              'linked_product_id' => $linked_product_id,
              'orderby'           => $next_order_by,
              'active'            => $linked_product_options['active'],
              'link_type'         => $link_type,
            ),
            true
          );
          if ($linked_product_options['is_bidirectional_link']) {
            cw_array2insert(
              'linked_products',
              array(
                'product_id'        => $linked_product_id,
                'linked_product_id' => $product_id,
                'orderby'           => $next_order_by,
                'active'            => $linked_product_options['active'],
                'link_type'         => $link_type,
              ),
              true
            );
          }
          $next_order_by = $start_order_by + $order_by_offset + ($i * $order_by_step);
          $i++;
        }
      }
      cw_header_location($add_linked_products_link);
      break;
    case 'delete_linked_products':
      if (!empty($delete_linked_products) && is_array($delete_linked_products)) {
        $delete_linked_products = array_keys($delete_linked_products);
        db_query("DELETE FROM $tables[linked_products] WHERE product_id = '".$product_id."' AND linked_product_id IN ('".implode("','", $delete_linked_products)."')");
      }
      cw_header_location($add_linked_products_link);
      break;
    case 'update_linked_products':
      if (!empty($linked_products) && is_array($linked_products)) {
        foreach ($linked_products as $linked_product_id => $linked_product) {
          $linked_product_id = intval($linked_product_id);
          if (empty($linked_product_id)) continue;
          $linked_product['orderby'] = intval($linked_product['orderby']);
          $linked_product['active']  = empty($linked_product['active']) ? 'N' : 'Y';
          $is_bidirectional_link     = empty($linked_products_options[$linked_product_id]['is_bidirectional_link']) ? false : true;
          cw_array2update('linked_products', $linked_product, "product_id = '".$product_id."' AND linked_product_id = '".$linked_product_id."' and link_type='$link_type'");
          if ($is_bidirectional_link) {
            $query = "SELECT COUNT(*) FROM $tables[linked_products] WHERE product_id = '".$linked_product_id."' AND linked_product_id = '".$product_id."' and link_type='$link_type'";
            $is_entry_exists = intval(cw_query_first_cell($query));
            if ($is_entry_exists) {
              cw_array2update(
                'linked_products',
                array(
                  'active' => $linked_product['active']
                ),
                "product_id = '".$linked_product_id."' AND linked_product_id = '".$product_id."' and link_type='$link_type'"
              );
            }
            else {
              cw_array2insert(
                'linked_products',
                array(
                  'product_id'        => $linked_product_id,
                  'linked_product_id' => $product_id,
                  'orderby'           => $linked_product['orderby'],
                  'active'            => $linked_product['active'],
                  'link_type'         => $link_type,
                ),
                true
              );
            }
          }
          else {
            db_query("DELETE FROM $tables[linked_products] WHERE product_id = '".$linked_product_id."' AND linked_product_id = '".$product_id."' and link_type='$link_type'");
          }
        }
      }
      cw_header_location($add_linked_products_link);
      break;
  }
  return false;
}

$query = "SELECT MAX(orderby) " .
         "FROM $tables[linked_products] " .
         "WHERE product_id = '".$product_id."' ";
$max_order_by = intval(cw_query_first_cell($query));
$next_order_by = (floor($max_order_by / $order_by_step) + 1) * $order_by_step;

$product_accessories = cw_call('cw_ac_get_linked_products',array($product_id,1));
$product_upselling = cw_call('cw_ac_get_linked_products',array($product_id,0));

$smarty->assign('product_accessories', $product_accessories);
$smarty->assign('product_upselling', $product_upselling);
/*
$query = "SELECT alp.linked_product_id, p.product, alp.orderby, alp.active, IF(ISNULL(alp_aux.product_id), 'N', 'Y') AS is_bidirectional_link " .
         "FROM $tables[linked_products] AS alp " .
         "INNER JOIN $tables[products] AS p " .
           "ON alp.linked_product_id = p.product_id " .
         "LEFT JOIN $tables[linked_products] AS alp_aux " .
           "ON alp_aux.product_id = alp.linked_product_id AND alp_aux.linked_product_id = alp.product_id " .
         "WHERE alp.product_id = '".$product_id."' " .
         "ORDER BY alp.orderby ASC";
$linked_products_result = db_query($query);
$total_items = db_num_rows($linked_products_result);
db_free_result($linked_products_result);
$navigation = cw_core_get_navigation($target, $total_items, $page);
$navigation['script'] = $add_linked_products_link;
$linked_products = cw_query($query." LIMIT $navigation[first_page], $navigation[objects_per_page]");
*/
$smarty->assign('order_by_step', $order_by_step);
$smarty->assign('next_order_by', $next_order_by);

?>
