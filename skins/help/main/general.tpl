{$lng.txt_help_zone_title}
<p />
<table cellspacing="0" cellpadding="0" width="100%" class="help_zone">

{if $usertype eq "P" }
<tr> 
<td height="15" class="Text">{include file='buttons/button.tpl' button_title=$lng.lbl_warehouses_zone href="index.php?target=home"}</td>
</tr>
{/if}

<tr>
<td height="15" class="Text">
    {capture name="page_url"}{pages_url var='help' section='password'}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_recover_password href=$smarty.capture.page_url}
</td>
</tr>

{if $usertype eq "C" or $usertype eq "B"}
<tr> 
<td height="15" class="Text">
    {capture name="page_url"}{pages_url var="help" section="contactus"}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_contact_us href=$smarty.capture.page_url}
</td>
</tr>
{/if}


<tr> 
<td height="15" class="Text">
    {capture name="page_url"}{pages_url var="help" section="faq"}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_faq_long href=$smarty.capture.page_url}
</td>
</tr>

<tr> 
<td valign="top" height="15" class="Text">
    {capture name="page_url"}{pages_url var="help" section="business"}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_privacy_statement href=$smarty.capture.page_url}
</td>
</tr>

<tr> 
<td valign="top" height="15" class="Text">
    {capture name="page_url"}{pages_url var="help" section="conditions"}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_terms_n_conditions href=$smarty.capture.page_url}
</td>
</tr>

<tr> 
<td valign="top" height="15" class="Text">
    {capture name="page_url"}{pages_url var="help" section="about"}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_about_our_site href=$smarty.capture.page_url}
</td>
</tr>

{include file='help/menu-list.tpl'}
</table>
{cms service_code="help_general_menu"}
