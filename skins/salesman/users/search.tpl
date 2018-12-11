{jstabs}
default_tab="{$js_tab|default:"basic_search"}"
default_template="main/users/search_form.tpl"

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"

[basic_search]
{assign var='lbl' value="lbl_search_user_`$current_search_type`"}
title="{$lng.$lbl}"

[adv_search_address]
title={$lng.lbl_search_customer_by_address}

[adv_search_tax]
title={$lng.lbl_adv_customer_search_admin}

[adv_search_web]
title={$lng.lbl_adv_customer_search_web}

[adv_search_mailing]
title={$lng.lbl_adv_customer_search_mailing}

{/jstabs}

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">
{include file='tabs/js_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections}
</form>

{if $mode eq 'search'}
{include file='main/users/search_results.tpl'}
{/if}
