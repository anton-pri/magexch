<?php

$doc_items = cw_query("select di.doc_id, di.item_id, di.product_id, di.history_cost, di.amount, di.price from cw_docs_items di inner join cw_docs d on d.doc_id=di.doc_id and d.date>1488326400  where di.amount>1 order by doc_id, item_id");

foreach ($doc_items as $di) {
    $mc_price = cw_query_first($s="SELECT * FROM cw_products_prices WHERE product_id='$di[product_id]' AND quantity<=$di[amount] and quantity>1 ORDER BY quantity DESC LIMIT 1");

    if (!empty($mc_price)) {

         if ($mc_price['list_price']!=$di['history_cost']) {
            $cost_message = ' cost needs update';
         } else {
            $cost_message = ' cost is correct';
         }

      if ($mc_price['price']!=$di['price']) {
        if ($mc_price['list_price']!=$di['history_cost']) {
          print_r($di);
          print("<br>");
          print_r($mc_price); print("<br>$s<br>");

          print("<h2>Price mismatch! $cost_message</h2>");

          $mc_price2 = cw_query_first($s="SELECT * FROM cw_products_prices WHERE product_id='$di[product_id]' AND quantity<=$di[amount] AND price='$di[price]' ORDER BY quantity DESC LIMIT 1"); 
          if ($mc_price2['quantity'] == 1) 
              $mc_price2['list_price'] = cw_query_first_cell("SELECT cost FROM cw_products WHERE product_id=$di[product_id]");  


          if (!empty($mc_price2)) { 
            print_r($mc_price2); print("<br>");
            if ($mc_price2['list_price']!=$di['history_cost']) 
              print("<h2>Second try: cost still needs to be updated</h2>");  
            elseif ($mc_price2['list_price']==$di['history_cost']) 
              print("<h2>Second try: Correct cost found</h2>");
          }
 print("<hr>");
        } else {
          print_r($di);
          print("<br>");
          print_r($mc_price);  
          print("<h2>Price mismatch! cost cant be correct</h2>");
          $mc_price2 = cw_query_first($s="SELECT * FROM cw_products_prices WHERE product_id='$di[product_id]' AND quantity<=$di[amount] AND price='$di[price]' ORDER BY quantity DESC LIMIT 1");
          if ($mc_price2['quantity'] == 1)
              $mc_price2['list_price'] = cw_query_first_cell("SELECT cost FROM cw_products WHERE product_id=$di[product_id]");

          if (!empty($mc_price2)) { 
            print_r($mc_price2); print("<br>");
            if ($mc_price2['list_price']!=$di['history_cost']) { 
              print("<h2>Second try: cost still needs to be updated</h2>");  

              print($s="UPDATE cw_docs_items SET history_cost='$mc_price2[list_price]' WHERE item_id='$di[item_id]'"); print("<br>");
//db_query($s);

            } elseif ($mc_price2['list_price']==$di['history_cost']) 
              print("<h2>Second try: Correct cost found</h2>");
          }

 print("<hr>");

        } 
      } else {
        if ($mc_price['list_price']!=$di['history_cost']) {
          print_r($di);
          print("<br>");
          print_r($mc_price); print("<br>$s<br>");

          print("<h2>Price OK! $cost_message</h2>");

          print($s="UPDATE cw_docs_items SET history_cost='$mc_price[list_price]' WHERE item_id='$di[item_id]'");
//          db_query($s);
print("<br>");

 print("<hr>");
        }
      } 
    } else {
    //  print("<h2>No mc price!</h2>");
    }
}


die('past orders mc cost fix');
