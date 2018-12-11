{assign var='id' value=$name|id}
{*include_once_src file='main/include_js.tpl' src='js/change_country_ajax.js'*}

<script type="text/javascript">
$(document).ready(function() {ldelim}
    cw_address_select_init({$user|default:0});
{rdelim});
</script>

<div id="address" href='index.php?target=user&mode=addresses&action=save&user={$userinfo.customer_id}'>

</div>

{if !$is_checkout && $userinfo.customer_id}
{include file="admin/buttons/button.tpl" button_title=$lng.lbl_save_address onclick="javascript: if ($('#address').validate().form()) submitFormPart('address',cw_register_init);" style="btn-green push-20"}
{/if}
