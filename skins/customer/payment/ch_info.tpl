{include file='common/subheader.tpl' title=$lng.lbl_check_information}
<script type="text/javascript">
<!--
requiredFields[requiredFields.length] = new Array('check_name','{$lng.lbl_ch_name|escape}');
requiredFields[requiredFields.length] = new Array('check_ban','{$lng.lbl_ch_bank_account|escape}');
requiredFields[requiredFields.length] = new Array('check_brn','{$lng.lbl_ch_bank_routing|escape}');
{if $payment_data.ccinfo}
requiredFields[requiredFields.length] = new Array('check_number','{$lng.lbl_ch_number|escape}');
{/if}
-->
</script>

<div class="input_field_1">
    <label>{$lng.lbl_ch_name}</label>
    <input type="text" id="check_name" name="check_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ch_bank_account}</label>
    <input type="text" id="check_ban" name="check_ban" autocomplete="off" size="32" maxlength="20" value="" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ch_bank_routing}</label>
    <input type="text" id="check_brn" name="check_brn" autocomplete="off" size="32" maxlength="20" value="" />
</div>

{if $payment_data.ccinfo}
<div class="input_field_1">
    <label>{$lng.lbl_ch_number}</label>
    <input type="text" id="check_number" name="check_number" autocomplete="off" size="32" maxlength="20" value="" />
</div>
{/if}
