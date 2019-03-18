<?php
die('Enable this script');
//$xcdb = "antonp_magexch.";
$xcdb = "";
cw_load('crypt');
$blowfish_key ='f39bd56f34854570cfdbef2c7f2d172b';

/* run this to clean all orders
delete from cw_customers_addresses where customer_id = 0 and address_id in (select current_address_id from cw_docs_user_info); delete from cw_customers_addresses where customer_id = 0 and address_id in (select main_address_id from cw_docs_user_info); truncate table cw_docs_info; truncate table cw_docs; truncate table cw_docs_user_info; truncate table cw_docs_items;  delete from cw_attributes_values where attribute_id = 8;
*/

$xc_orders = cw_query("select o.*,c.cw_id from ".$xcdb."xcart_orders o left join ".$xcdb."xcart_customers c on o.login=c.login where c.cw_id is not null order by o.orderid");

foreach($xc_orders as $xc_o) {

    print_r($xc_o); cw_flush("<br><br>");

    $orderid = $xc_o['orderid']; 
    $products = cw_query("select od.*, sd.login as seller, p.weight, c.cw_id, sd.`comments`, sd.`condition` from ".$xcdb."xcart_order_details od left join ".$xcdb."xcart_seller_data sd on sd.id=od.seller_data_id left join ".$xcdb."xcart_products p on od.productid=p.productid left join ".$xcdb."xcart_customers c on c.login=sd.login where od.orderid='$orderid'");
    //group order products by sellers
    $by_seller = array();
    foreach ($products as $p) {
        if (!isset($by_seller[$p['seller']])) 
            $by_seller[$p['seller']] = array('products'=>array(), 'cw_id'=>$p['cw_id'], 'subtotal'=>0.00 ,'shipping_cost'=>0.00, 'weight'=>0.00, 'total'=>0.00);
        $by_seller[$p['seller']]['products'][] = $p;
        $by_seller[$p['seller']]['subtotal'] += $p['price']*$p['amount']; 
        $by_seller[$p['seller']]['weight'] += $p['weight']*$p['amount'];
    }

    $order_extra = unserialize($xc_o['extra']);

    $xc_o['details'] = text_decrypt($xc_o['details'], $blowfish_key);


cw_flush($xc_o['details']."<br><br>");

    foreach ($by_seller as $seller_login => $new_ord) { 

        $sell_ord = $order_extra['cart_seller_data'][$seller_login];
        if (empty($sell_ord)) {
            $sell_ord = $new_ord;
            $sell_ord['shipping_cost'] = $sell_ord['shipping_cost']; 
            $sell_ord['display_subtotal'] = $sell_ord['subtotal'];
            $sell_ord['discounted_subtotal'] = $sell_ord['subtotal'];
            $sell_ord['display_discounted_subtotal'] = $sell_ord['subtotal'];
            $sell_ord['total'] = $sell_ord['subtotal'] + $sell_ord['shipping_cost']; 
            $sell_ord['total_cost'] = $sell_ord['total'];
        }
        $docs_info_insert = array(
            "warehouse_customer_id" => $new_ord['cw_id'],      
            "customer_notes" => $xc_o['customer_notes'],            
            "details" => cw_crypt_text($xc_o['details']),                  
            "tracking" => $xc_o['tracking'],               
            "notes" => $xc_o['notes'],                      
            "payment_label" => $xc_o['payment_method'],              
            "shipping_label" => $xc_o['shipping'],             
            "subtotal" => $sell_ord['subtotal'],                   
            "display_subtotal" => $sell_ord['display_subtotal'],           
            "discounted_subtotal" => $sell_ord['discounted_subtotal'],        
            "display_discounted_subtotal" => $sell_ord['display_discounted_subtotal'],
            "shipping_cost" => $sell_ord['shipping_cost'],             
            "display_shipping_cost" => $sell_ord['shipping_cost'], 
            "weight" => $new_ord['weight'],  
            "payment_surcharge" => $xc_o['payment_surcharge']*($sell_ord['total_cost']/$xc_o['total']),                    
            "total" => $sell_ord['total_cost'],                     
            "display_total" => $sell_ord['total_cost']
        );            
        $doc_info_id = cw_array2insert('docs_info', $docs_info_insert); 
print_r($docs_info_insert);
cw_flush("<br><br>");
        $doc_insert = array(
            'doc_info_id' => $doc_info_id,
            'type' => 'O',
            'display_id' => $xc_o['orderid'],
            'prefix' => '',
            'display_doc_id' => $xc_o['orderid'],
            'year' => date('Y', $xc_o['date']),
            'date' => $xc_o['date'],
            'status_change' =>  $xc_o['date'],
            'status' => $xc_o['status']
        );
        $doc_id = cw_array2insert('docs', $doc_insert);

        cw_array2insert('attributes_values', array('item_id'=>$doc_id, 'attribute_id'=>8, 'value'=>11, 'item_type'=>'O'));

print_r($doc_insert);
cw_flush("<br><br>");
        $main_address = array(
            'customer_id' => 0,
            'main' => 1,
            'company' => $xc_o['company'],
            'title' => $xc_o['title'],
            'firstname' => $xc_o['b_firstname'],
            'lastname' => $xc_o['b_lastname'],
            'address' => $xc_o['b_address'],
            'city' => $xc_o['b_city'],
            'county' => $xc_o['b_county'],
            'state' => $xc_o['b_state'],
            'country' => $xc_o['b_country'],
            'zipcode' => $xc_o['b_zipcode'],
            'phone' => $xc_o['phone'],
            'fax' => $xc_o['fax']
        );
        $main_address_id = cw_array2insert('customers_addresses', $main_address); 
print_r($main_address);
cw_flush("<br><br>");
        $current_address = array(
            'customer_id' => 0,
            'current' => 1,
            'company' => $xc_o['company'],
            'title' => $xc_o['title'],
            'firstname' => $xc_o['s_firstname'],
            'lastname' => $xc_o['s_lastname'],
            'address' => $xc_o['s_address'],
            'city' => $xc_o['s_city'],
            'county' => $xc_o['s_county'],
            'state' => $xc_o['s_state'],
            'country' => $xc_o['s_country'],
            'zipcode' => $xc_o['s_zipcode'],
            'phone' => $xc_o['phone'],
            'fax' => $xc_o['fax']
        );
        $current_address_id = cw_array2insert('customers_addresses', $current_address); 
print_r($current_address);    
 cw_flush("<br><br>");
        $doc_user_info = array(
            'doc_info_id' => $doc_info_id,
            'customer_id' => $xc_o['cw_id'],
            'membership_id' => 0,
            'usertype' => 'C',
            'main_address_id' => $main_address_id,
            'current_address_id' => $current_address_id,
            'email' => $xc_o['email']
        );
print_r($doc_user_info);
        cw_array2insert('docs_user_info', $doc_user_info);

cw_flush("<br><br>");
        foreach ($new_ord['products'] as $p) { 
            $item_extra_data = 
                array (
                    'product_options' => 
                        array (
                        ),
                    'taxes' => 
                        array (
                        ),
                    'display' => 
                        array (
                            'price' => $p['price'],
                            'net_price' => 0,
                            'discounted_price' => $p['price'],
                            'subtotal' => $p['price'],
                        ),
                        'surcharge' => 0,
                        'seller_item' => 
                            array (
                                'seller_item_id' => $p['seller_data_id'],
                                'condition' => $p['condition'],
                                'comments' => $p['comments'],
                            ),
                );


            $doc_items_info = array(
                'doc_id' => $doc_id,
                'product_id' => $p['productid'],
                'productcode' => $p['productcode'],
                'product' => $p['product'],
                'price' => $p['price'],
                'amount' => $p['amount'],
                'warehouse_customer_id' => $new_ord['cw_id'],
                'extra_data' => addslashes(serialize($item_extra_data)), 
                'seller_data_id' => $p['seller_data_id'] 
            );
print_r($doc_items_info);
            $item_id = cw_array2insert('docs_items', $doc_items_info);   
cw_flush("<br><br>");
        }
    }
}


die;
