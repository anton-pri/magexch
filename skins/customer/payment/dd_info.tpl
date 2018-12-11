{include file='common/subheader.tpl' title=$lng.lbl_check_information}
<script type="text/javascript">
<!--
requiredFields.push(['debit_name','{$lng.lbl_ch_name|escape}']);
requiredFields.push(['debit_bank_account','{$lng.lbl_ch_bank_account|escape}']);
requiredFields.push(['debit_bank_number','{$lng.lbl_ch_bank_routing|escape}']);
-->
</script>

<div class="input_field_1">
    <label>{$lng.lbl_ch_name}</label>
    <input type="text" id="debit_name" name="debit_name" size="32" maxlength="20" value="{if $userinfo.lastname ne ""}{$userinfo.firstname} {$userinfo.lastname}{/if}" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ch_bank_account}</label>
    <input type="text" id="debit_bank_account" name="debit_bank_account" autocomplete="off" size="32" maxlength="20" value="" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ch_bank_routing}</label>
    <input type="text" id="debit_bank_number" name="debit_bank_number" autocomplete="off" size="32" maxlength="20" value="" />
</div>

<div class="input_field_1">
    <label>{$lng.lbl_ch_bank_name}</label>
    <input type="text" id="debit_bank_name" name="debit_bank_name" autocomplete="off" size="32" maxlength="20" value="" />
</div>
