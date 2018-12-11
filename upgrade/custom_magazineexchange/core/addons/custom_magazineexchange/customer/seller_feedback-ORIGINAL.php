<?php

if ($REQUEST_METHOD == "POST" && $action=='save_seller_feedback') {

   //die(serialize($_POST));
   cw_array2insert('magexch_sellers_feedback', 
       array(
           'customer_id' => $customer_id, 
           'seller_id' => $order_seller_id,
           'doc_id' => $doc_id, 
           'rating' => $seller_rating, 
           'review' => addslashes($seller_review)
       ), 
       true
   );

   cw_add_top_message("The seller feed back has beed saved", 'I');
   cw_header_location("index.php?target=docs_O&mode=search#magexch_box_orders");
}
