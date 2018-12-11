<script type="text/javascript">
<!--
{literal}
/*
function popup_preview_order_emails(status_code, preview_area) {

    if ($('#preview_order_emails').length==0)
        $('body').append('<div id="preview_order_emails" style="overflow:hidden"></div>');

    var hash = status_code+preview_area;
    if (hash != $('#preview_order_emails').data('hash')) {
        // Load iframe with image selector into dialog
        $('#preview_order_emails').html("<iframe frameborder='no' width='800' height='490' src='index.php?target=preview_order_emails&status_code="+status_code+"&preview_area="+preview_area+"'></iframe>");
    }

    $('#preview_order_emails').data('hash', hash);
    // Show dialog
    sm('preview_order_emails', 830, 530, false, 'Preview order '+preview_area+' emails for status '+status_code);
}
*/

var productstagesForm = null;
$(document).ready(function(){
  productstagesForm = $("#editproductstagesForm");
  productstagesForm.validate();
});

{/literal}
-->
</script>


<form name="product_stages_form" method="post" action="index.php?target=product_stages" id="editproductstagesForm">
<input type="hidden" name="action" value="modify" />

{if $smarty.get.mode ne 'add'}

{capture name=section}

<div class="box">
<div class="clear"></div>
{if $library_stages ne ''}
{foreach from=$library_stages item=ls}
<table class="header statuses" width="100%">
<tr><th colspan="2"><span id="product_stage_{$ls.stage_lib_id}">{$ls.title}</span>
</th></tr>
<tr>
<td valign="top" width="10" style="padding-top:35px;"><img height="1" width="10" alt="" src="{$ImagesDir}/spacer.gif">
<input type="checkbox" name="posted_data[{$ls.stage_lib_id}][deleted]" value="1" /></td>
<td>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_name}</label>
  <input type="text" class='required' name="posted_data[{$ls.stage_lib_id}][title]" value="{$ls.title}" />
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_email_subject}</label>
  <input type="text" class='required' name="posted_data[{$ls.stage_lib_id}][subject]" value="{$ls.subject|escape}" size="65" />
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_email_body}</label>
  <textarea  class='required' name="posted_data[{$ls.stage_lib_id}][body]" style="width: auto" cols="65" rows="7">{$ls.body}</textarea>
<br /><a href="index.php?target=stage_email_test&stage_lib_id={$ls.stage_lib_id}" target="_blank">{$lng.lbl_test_email_template|default:'test email template'}</a>
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_default_period}</label>
  <input type="text" class='required' name="posted_data[{$ls.stage_lib_id}][default_period]" value="{$ls.default_period}" size="5" />
</div>
<div class="input_field_0">
  <label>{$lng.lbl_default_order_status}</label>
  {include file="main/select/doc_status.tpl" status=$ls.default_status normal_array="1" name="posted_data[`$ls.stage_lib_id`][default_status][]" mode="select" multiple="1"}
</div>
<div class="input_field_0">
  <label>{$lng.lbl_orderby}</label>
  <input type="text" name="posted_data[{$ls.stage_lib_id}][pos]" value="{$ls.pos}" size="5" />
</div>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
{/foreach}
{else}
<div>{$lng.lbl_no_product_stages_defined|default:'No product stages defined'}</div>
{/if}

<div id="sticky_content" class="buttons">
    {include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('product_stages_form');"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=product_stages&mode=add"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('product_stages_form', 'delete');"}
</div>


{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_product_stages}

{else}

{capture name=section}

<div class="box">
<div class="clear"></div>
<table class="header statuses" width="100%">
<tr><th colspan="2"><span id="product_stage_new">{$lng.lbl_new_product_stage}</span>
</th></tr>
<tr>
<td valign="top" width="10" style="padding-top:35px;"><img height="1" width="10" alt="" src="{$ImagesDir}/spacer.gif">
</td>
<td>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_name}</label>
  <input type="text" class='required' name="added_data[title]" value="" />
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_email_subject}</label>
  <input type="text" class='required' name="added_data[subject]" value="" size="65" />
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_email_body}</label>
  <textarea  class='required' name="added_data[body]" style="width: auto" cols="65" rows="7"></textarea>
</div>
<div class="input_field_0">
  <label class='required'>{$lng.lbl_default_period}</label>
  <input type="text" class='required' name="added_data[default_period]" value="0" size="5" />
</div>
<div class="input_field_0">
  <label>{$lng.lbl_default_order_status}</label>
  {include file="main/select/doc_status.tpl" name="added_data[default_status][]" mode="select" multiple="1"}
</div>
<div class="input_field_0">
  <label>{$lng.lbl_orderby}</label>
  <input type="text" name="added_data[pos]" value="0" size="5" />
</div>
</td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>

<br />
<br />
<div id="sticky_content" class="buttons">
    {include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript:cw_submit_form('product_stages_form', 'add');"}
</div>

{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_add_new}
{/if}

</form>
