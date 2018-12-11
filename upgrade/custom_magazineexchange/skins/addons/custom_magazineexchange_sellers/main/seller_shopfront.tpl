
<form name="shopfront_edit_form" method="post" action="index.php?target=seller_shopfront">
<input type="hidden" name="mode" value="update" />


{capture name=section}


<div style="margin: 0 auto; width:610px;">
<div class="block block-themed animated fadeIn">
<div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_configure_shopfront}</h3></div>
<div class="col-sm-12" style="padding:10px 0 10px 15px;">{$lng.lbl_configure_shopfront_note}</div>
<div class="jasellerblock-content">



  <div class="block">

<div style="float:left;">

    <div class="block-content" style="padding:20px !important">
    <div class="form-group">
        <label>{$lng.lbl_shop_company_name}</label>{$lng.lbl_configure_shopfront_tooltip}
        <input class="form-control" type="text" name="posted_data[shop_name]" value="{$shopfront.shop_name}" />
     
     <div class="form-group" style="margin-top:15px;">
        <label>{$lng.lbl_shop_logo}</label>{$lng.lbl_configure_shopfront_tooltip2}
        <div>{include file='main/images/edit.tpl' image=$shopfront.image delete_url="index.php?target=seller_shopfront&mode=delete_image" in_type='shopfront_images'}</div>

</div>

</div>
</div>

</div>


<div style="float:right; margin-right:4%;"><a target="_blank" href="{$catalogs.customer}/help-centre-selling-back-issues-my-shopfront.html"><img src="/cw/images/Shopfront_Avatar3.gif" width="214" height="275"></a></div>


<div style="clear: both;"></div> 

      <div class="block" style="padding:20px !important"> 
      <div class="form-group">
        <label>{$lng.lbl_shop_holday_settings}</label><br>
        <label for='seller_holiday_settings_on' style="font-weight: lighter;">{$lng.lbl_shop_holday_settings_on}: </label>&nbsp; &nbsp; &nbsp;<input type="checkbox" name="posted_data[holiday_settings]" id='seller_holiday_settings_on' value='1' {if $shopfront.holiday_settings eq 1}checked="checked"{/if} /><br>
        <label style="float:left; font-weight: lighter;">{$lng.lbl_shop_holday_settings_return_date}:</label>&nbsp;<div class="col-md-4">{include file='main/select/date.tpl' value=$shopfront.holiday_settings_return_date|default:0 name='posted_data[holiday_settings_return_date]'}</div><br><br><br>

 
      <div class="form-group">
        <label>{$lng.lbl_shop_short_description}</label>
        <textarea class="form-control" name="posted_data[short_desc]" style="width: auto" cols="65" rows="3">{$shopfront.short_desc}</textarea>
      </div>
      <div class="form-group">
        <label>{$lng.lbl_shop_long_description}</label>
        <textarea class="form-control" name="posted_data[long_desc]" style="width: auto" cols="65" rows="7">{$shopfront.long_desc}</textarea>
      </div>

      

      <div id="sticky_content" class="buttons">
          {include file='buttons/button.tpl' button_title=$lng.lbl_update class="btn-green btn" href="javascript:cw_submit_form('shopfront_edit_form', 'update');"}
      </div>

    </div>
  </div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_mag_my_shopfront}
</div></div></div>
</form>
