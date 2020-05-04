<div class="content bg-image overflow-hidden" style="background-color: #FCCB05;"><div style="float:right; font-family: Century Gothic, Arial, Courier New, Sans-Serif; font-size: 36px; font-color: black;">seller account</div>
  <div class="push-30-t push-15">
<h1 class="h2 text-red animated zoomIn">Seller Dashboard</h1>
    <h2 class="h6 text-black-op animated zoomIn">{$smarty.now|date_format:"%A, %B %e, %Y"}</h2>
    <h2 class="h5 text-black animated zoomIn"><b>Welcome {$user_account.firstname}!</b></h2>

  </div>
</div>
{include file='addons/dashboard/admin/sections/statistics.tpl'}

<table width="100%"><tr><td>
  <div style="margin-left: 6%; margin-right: 6%;">
    <div style="float:left; width:70%; text-align: left; padding: 3px 10px; border-style: solid;  border-width: 1px; background: #eaeaea;">
        {$lng.lbl_welcome_to_seller_area|default:'Welcome to seller area'}
    </div>
    <div style="float:right; width:203px;">
<!-- <div class="seller_holiday_dashboard_box">
{tunnel func='cw\custom_magazineexchange_sellers\mag_get_shopfront' via='cw_call' param1=$customer_id assign='shopfront'}
<div class="seller_holiday_frame">
<span>{$lng.lbl_holiday_settings|upper}:</span> <span style="color:red; font-weight:bold">{if $shopfront.holiday_settings eq 1}{$lng.lbl_hs_on}{else}{$lng.lbl_hs_off}{/if}</span>&nbsp;&nbsp;&nbsp;<a href='index.php?target=seller_shopfront' target='blank' >{$lng.lbl_hs_change}</a>
</div>
</div> -->
      <div style="text-align: center;">
        {if $shopfront.holiday_settings eq 1}
          <img src="https://magazineexchange.co.uk/cw/xc_skin/images/Help_Section_Images/Holiday_Settings_ON.gif" width="203" height="173">
        {else}
          <img src="https://magazineexchange.co.uk/cw/xc_skin/images/Help_Section_Images/Holiday_Settings_OFF.gif" width="203" height="173">
        {/if}
        <br>
        <a href='index.php?target=seller_shopfront' target='blank' >{$lng.lbl_hs_change}</a>
      </div>
      <br>
      <div style="text-align: center;">
        <div class="block-header bg-green"><h3 style="text-align: center;" class="block-title">{$lng.lbl_mag_my_shopfront}</h3></div>
        <div style="width:100%;padding-left:14px;padding-right:14px" class="jasellerblock-content">
          <div style="margin-top:20px">
            {include 
              file='admin/buttons/button.tpl' 
              button_title=$lng.lbl_visit_shopfront|default:'Visit Shopfront' 
              href="../all-magazines?vendorid=`$customer_id`" 
              style="btn-green push-5-r push-20"
              target="_blank"
            }
          </div>
          <div class="my_shopfront_note">
            Check your listings regularly!
          </div>
        </div>
        <a href='index.php?target=seller_shopfront' target='blank' >{$lng.lbl_configure_shopfront|default:'Configure Shopfront'}</a>
      </div>
    </div>
    <div style="clear:both;">
  </div>
</td></tr></table>

{include file='addons/dashboard/admin/sections/sections.tpl'}
