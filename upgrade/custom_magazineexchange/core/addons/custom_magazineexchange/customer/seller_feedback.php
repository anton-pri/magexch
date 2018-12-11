<?php

if ($REQUEST_METHOD == "POST" && $action=='save_seller_feedback') {

   //die(serialize($_POST));
   cw_array2insert('magexch_sellers_feedback', 
       array(
           'customer_id' => $customer_id, 
           'seller_id' => $order_seller_id,
           'doc_id' => $doc_id, 
           'rating' => $seller_rating, 
           'review' => addslashes($seller_review),
           'date' => time()
       ), 
       true
   );

   //cw_add_top_message("<span style=\"font-weight: bold; color: #FDCD0D;\">The seller feedback has been saved</span>", 'I'); - defined as css in skins_magazineechange/customer_altskin.css, see the #top_message_content style class

   cw_add_top_message("The seller feedback has been saved", 'I');
   cw_header_location("index.php?target=docs_O&mode=details&doc_id=$doc_id");
}
