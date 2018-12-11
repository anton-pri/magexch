<select name="{$name}" {if $disabled}disabled{/if} id="select_user_tax">
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$taxes item=tax}
<option value="{$tax.tax_id}"{if $value eq $tax.tax_id} selected{/if}>{$tax.value}% {$tax.title}</option>
{/foreach}
</select>
{include file="main/visiblebox_link.tpl" mark="open_close_taxes"}

<script language="javascript">
var please_select_lng = "{$lng.lbl_please_select}";

{literal}
function handler_taxes_list(data) {
    select = document.getElementById('select_user_tax');
    while (select.options.length > 0)
        select.options[select.options.length-1] = null;
    select.options[select.options.length] = new Option(please_select, 0);
    sel_index = 0;
    index = 1;
    if (data.taxes)
    for (i in data.taxes) {
        select.options[select.options.length] = new Option(data.taxes[i].value+'% '+data.taxes[i].title, i);
        if (i == data.selected) sel_index = index;
        index++;
    }
    select.selectedIndex = sel_index;
}

function ajax_update_taxes_list() {
    $.ajax({
        "url":{/literal}'index.php?target={$current_target}&mode=taxes&user={$user}&action=ajax_update'{literal},
        "success":handler_taxes_list,
        "dataType":"json",
        "type":"post",
    });
}
{/literal}
</script>

<div id="open_close_taxes" style="display:none;">
<iframe width="300" height="100" src="index.php?target={$current_target}&mode=taxes&user={$user}"></iframe>
</div>
