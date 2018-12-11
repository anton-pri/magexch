{$lng.lbl_customer_order_details_top_note}
<br>
<div class="order_block status_seller_feedback">
<h3>{$lng.lbl_summary}</h3>
<div id="order_summary">

  <table border="0" cellspacing="0" cellpadding="2" width="100%" class="SellerTable">
    <tbody>
      <tr class="ProductTableHead">
          <td align="center" width="30%">{$lng.lbl_status}</td>
          <td align="center" width="35%">{$lng.lbl_seller_information}</td>
          <td align="center" width="35%">{$lng.lbl_feedback}</td>
      </tr>
      <tr>
          <td>{include file="main/select/doc_status.tpl" status=$doc.status mode="static"}</td>
          <td>{include file="main/seller_info.tpl" seller_customer_id=$doc.info.warehouse_customer_id}</td>
          <td>{if in_array($doc.status, array('P', 'S', 'C', 'PO'))}{include file="main/seller_feedback_button.tpl" seller_customer_id=$doc.info.warehouse_customer_id customer_id=$doc.userinfo.customer_id doc_id=$doc.doc_id include_feedback_popup_js_code='Y'}{else}&nbsp;{/if}</td>
      </tr>
   </table>
   <div class='seller_status_notes'>
<!--
      <span>- Click on Seller's username to view their listings, description information and feedback</span>
      <span>- Wait until the order has been dispatched & good received before leaving feedback</span>
      <span>- Click here for an <a>Explanation of Order Status Messages</a></span>
-->
   {cms service_code='order_summary_seller_status_notes'}
   </div>
</div>

<h3>{$lng.lbl_messages}</h3>
<div id="order_messages">

   <a class="contact" style='float:left' href="index.php?target=message_box&mode=new&contact_id={$doc.info.warehouse_customer_id}">{$lng.lbl_contact_seller} ></a>
{tunnel func='magexch_get_admin_customer_id' via='cw_call' param1='magexch_primary_contact_admin_email' assign='magexch_admin_user_id'}
{if $magexch_admin_user_id}
   <a class="contact" style='float:right' href="index.php?target=message_box&mode=new&contact_id={$magexch_admin_user_id}">{$lng.lbl_contact_magazine_exchange} ></a>
{/if}

   <div class='seller_status_notes'>
<!--
      <span>- Contact the Seller with any queries regarding this order BEFORE leaving feedback</span>
      <span>- Contact Magazine Exchange in case of "Not Finished" status or other problems</span>
      <span>- Replies to messages will appear in your <a>My Messages - Received</a> page</span>
-->
   {cms service_code='order_messages_seller_status_notes'}
   </div>
</div>

<h3>{$lng.lbl_seller_information}</h3>
<div id="order_seller_information">
{include file='addons/custom_magazineexchange_sellers/main/orders/seller_info.tpl'}
</div>

</div>
