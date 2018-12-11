<script language="Javascript">
{literal}
function cw_letters_search(user) {
    substring = document.getElementById('user_letters_substring').value;
    fromdate = document.getElementById('user_letters_fromdate').value;
    todate = document.getElementById('user_letters_todate').value;
    document.getElementById('user_letters').src="index.php?target={/literal}{$current_target}{literal}&mode=letters&user="+user+"&substring="+substring+"&fromdate="+fromdate+"&todate="+todate;
}
{/literal}
</script>
<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
    {include file='main/select/date.tpl' name='user_letters_fromdate' value=$search_prefilled.admin.creation_date_start} -
    {include file='main/select/date.tpl' name='user_letters_todate' value=$search_prefilled.admin.creation_date_start}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_substring}</label>
    <input type="text" id="user_letters_substring" size="32" maxlength="32" value="{$purchased_products.substring}" />
    {include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_letters_search(`$user`)" js_to_href='Y'}
</div>

<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=letters&user={$user}" id="user_letters"></iframe>

{include file='main/users/sections/custom.tpl'}
