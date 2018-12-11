<script type="text/javascript">
<!--
var card_types = new Array();
var card_cvv2 = new Array();
{foreach from=$card_types item=c}
card_types["{$c.code}"] = "{$c.type}";
card_cvv2["{$c.code}"] = "{$c.cvv2}";
{/foreach}
var force_cvv2 = {if !$payment_data.ccinfo && ($config.General.check_cc_number eq "Y" || $smarty.get.mode eq 'checkout')}true{else}false{/if};
var txt_cc_number_invalid = "{$lng.txt_cc_number_invalid|strip_tags|escape:javascript}";
var current_year = parseInt(('{$smarty.now|date_format:"%Y"}').replace(/^0/gi, ""));
var current_month = parseInt(('{$smarty.now|date_format:"%m"}').replace(/^0/gi, ""));
var lbl_is_this_card_expired = "{$lng.lbl_is_this_card_expired|strip_tags|escape:javascript}";
var lbl_cvv2_is_empty = "{$lng.lbl_cvv2_is_empty|escape:javascript}";
var lbl_cvv2_isnt_correct = "{$lng.lbl_cvv2_isnt_correct|escape:javascript}";
var lbl_cvv2_must_be_number = "{$lng.lbl_cvv2_must_be_number|escape:javascript}";

var check_cc_number = "{$config.General.check_cc_number}";
-->
</script>
{include_once_src file='main/include_js.tpl' src='js/check_cc_number_script.js'}
