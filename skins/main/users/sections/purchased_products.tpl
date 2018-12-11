<script language="Javascript">
{literal}
function cw_purchased_products_search(user) {
    substring = document.getElementById('user_purchased_products_substring').value;
    fromdate = document.getElementById('user_purchased_products_fromdate').value;
    todate = document.getElementById('user_purchased_products_todate').value;
    document.getElementById('user_purchased_products').src="index.php?target={/literal}{$current_target}{literal}&mode=purchased_products&user="+user+"&substring="+substring+'&fromdate='+fromdate+'&todate='+todate;
}
{/literal}
</script>
<div class="input_field_0">
    <label>{$lng.lbl_substring}</label>
    <input type="text" id="user_purchased_products_substring" size="32" maxlength="32" value="{$purchased_products.substring}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_date}</label>
{include file='main/select/date.tpl' name='user_purchased_products_fromdate' value=$purchased_products.creation_date_start} -
{include file='main/select/date.tpl' name='user_purchased_products_todate' value=$purchased_products.creation_date_end}
</div>

<div class="clear"></div>

<div class="left" style="margin: 10px 0;">
{include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript:cw_purchased_products_search(`$user`)" style='btn'}
</div>

<iframe width="100%" height="250" src="index.php?target={$current_target}&mode=purchased_products&user={$user}" id="user_purchased_products"></iframe>

{include file='main/users/sections/custom.tpl'}
