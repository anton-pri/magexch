<script language="Javascript">
{literal}
function cw_generated_docs_search(user) {
    substring = document.getElementById('user_generated_docs_substring').value;
    fromdate = document.getElementById('user_generated_docs_fromdate').value;
    todate = document.getElementById('user_generated_docs_todate').value;
    document.getElementById('user_generated_docs').src="index.php?target={/literal}{$current_target}{literal}&mode=generated_docs&user="+user+"&substring="+substring+"&fromdate="+fromdate+"&todate="+todate;
}
{/literal}
</script>
<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
{include file='main/select/date.tpl' name='user_generated_docs_fromdate' value=$search_prefilled.admin.creation_date_start} -
{include file='main/select/date.tpl' name='user_generated_docs_todate' value=$search_prefilled.admin.creation_date_end}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_substring}</label>
    <input type="text" id="user_generated_docs_substring" size="32" maxlength="32" value="{$purchased_products.substring}" />
    {include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_generated_docs_search(`$user`)"}
</div>

<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=generated_docs&user={$user}" id="user_generated_docs"></iframe>

{include file='main/users/sections/custom.tpl'}
