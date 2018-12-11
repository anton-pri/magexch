
{capture name=dialog}





{tunnel func='cw_user_get_info' via='cw_call' param1=$customer_id param2=2048 assign='my_seller_data'}
<!-- {$my_seller_data|@debug_print_var} -->

<div style="margin-left: 20%;">
<div style="float:left; width:450px;" class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_about_this_title_basic}</h3></div>
<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_about_this_title_basic_note}</div>
<div style="width:450px;" class="jasellerblock-content">



<iframe allowtransparency="true" src="
https://form.jotform.com/70446148006956?yourEmail={$user_account.email} &yourUsername={$my_seller_data.custom_fields_by_name.username}" frameborder="0" style="width:400px; height:1200px; border:none;" scrolling="no">
	</iframe>






</div></div><div>
<div style="margin-left: 500px;"><a target="_blank" href="{$catalogs.customer}/contribute-content-about-this-title.html"><img src="/cw/images/About_this_title_Avatar.gif" width="142" height="490"></a></div>
<div style="clear: both;"></div>











{/capture}
{include file="admin/wrappers/section.tpl" title="About this title - Basic" content=$smarty.capture.dialog extra='width="100%"'}

