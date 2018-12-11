<?php
global $customer_id, $tables;
if (defined('IS_AJAX')) {


    $action = $_GET['action'];
    $review_id = intval($_GET['review_id']);

    $user_ip = $_SERVER["REMOTE_ADDR"] . '_' . $_SERVER["HTTP_X_FORWARDED_FOR"] . '_' . $_SERVER["HTTP_CLIENT_IP"];

   if (cw_query_first_cell("SELECT COUNT(*) FROM  $tables[products_reviews] WHERE customer_id = '$customer_id' and review_id = '$review_id'") >0) {
       echo json_encode(array('err'=>'you can not rate own review'));
       exit();
   }

   $user_rate = cw_query_first("SELECT id, rate FROM  $tables[products_reviews_ratings] WHERE review_id = '$review_id' and ((0 != $customer_id and customer_id = '$customer_id') or sess_id = '$APP_SESS_ID' or remote_ip='$user_ip')");
   
   $already_like = $user_rate['rate'] == 1;
   $already_dislike = $user_rate['rate'] == 2;

   if($action=='like')
      $rate = 1;
    elseif($action=='dislike')
      $rate = 2;
    else exit();

    if(!$user_rate)
        cw_array2insert('products_reviews_ratings', array('customer_id'=>$customer_id, 'review_id'=> $review_id,'sess_id'=>$APP_SESS_ID,'remote_ip'=>$user_ip, 'rate'=>$rate), true);
    elseif(($action=='like' && $already_dislike==true) || ($action=='dislike' && $already_like == true))
        cw_array2update('products_reviews_ratings', array('rate'=>$rate), "id = '$user_rate[id]'");
    else{
        echo json_encode(array('err'=>0));
        exit();
    }

    $votes['p_vote'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_reviews_ratings] WHERE review_id = '$review_id' AND rate =1");
    $votes['n_vote'] = cw_query_first_cell("SELECT COUNT(*) FROM $tables[products_reviews_ratings] WHERE review_id = '$review_id' AND rate =2");

    echo json_encode($votes);

}
exit();
