
{capture name=dialog}





{tunnel func='cw_user_get_info' via='cw_call' param1=$customer_id param2=2048 assign='my_seller_data'}
<!-- {$my_seller_data|@debug_print_var} -->

<div style="margin-left: 20%;">
<div style="float:left; width:450px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_seller_payment_info}</h3></div>
<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_payment_info_note}</div>
<div style="width:450px;" class="jasellerblock-content">

<iframe allowtransparency="true" src="
https://form.jotform.com/70433622381955?yourEmail={$user_account.email} &yourUsername={$my_seller_data.custom_fields_by_name.username}" frameborder="0" style="width:400px; height:950px; border:none;" scrolling="no">
	</iframe>






</div></div><div>
<div style="margin-left: 500px;"><img src="/cw/images/Payment_Details_Avatar.gif" width="116" height="271"></div>
<div style="clear: both;"></div>











{/capture}
{include file="admin/wrappers/section.tpl" title="Seller Payment Information" content=$smarty.capture.dialog extra='width="100%"'}
