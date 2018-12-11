<div style="padding-left: 80px; padding-top: 200px; padding-right: 60px; background-image: url(http://www.magazineexchange.co.uk/cw/xc_skin/images/Help_Section_Images/Customer_Account_Registration_bg.gif); width:680px;">

<br>
<!--<h1>{$lng.lbl_reg_customer_acc}</h1>-->
<div id="register_customer">
<font class="Text">
<div align="left"><font size="2">{$lng.lbl_reg_customer_acc_text}</font>
<br><br><hr><br><b><font size="2">
{$lng.lbl_reg_customer_acc_note}</font></b></div>
<br><br>

{$lng.txt_fields_are_mandatory}

</font>
<br />
<br />

{capture name=section2}

{include file="customer/acc_manager/register_customer.tpl"}
{/capture}
{include file='common/section.tpl' is_dialog=0 title=$lng.lbl_profile_details content=$smarty.capture.section2}
</div>
</div>
