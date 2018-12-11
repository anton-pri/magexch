<select name="{$name}" {if $disabled}disabled{/if} id="select_user_cod_type">
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$cod_types item=cod_type}
<option value="{$cod_type.cod_type_id}"{if $value eq $cod_type.cod_type_id} selected{/if}>{$cod_type.title}</option>
{/foreach}
</select>
{include file="main/visiblebox_link.tpl" mark="open_close_cod_type"}

<script language="javascript">
var submit_url = 'index.php?target=cod_types&&mode=ajax_update&iframe=1';
var please_select = "{$lng.lbl_please_select}";

{literal}
function handler(data) {
    select = document.getElementById('select_user_cod_type');
    while (select.options.length > 0)
        select.options[select.options.length-1] = null;
    select.options[select.options.length] = new Option(please_select, 0);
    sel_index = 0;
    index = 1;
    if (data.cods)
    for (i in data.cods) {
        select.options[select.options.length] = new Option(data.cods[i].title, i);
        if (i == data.selected) sel_index = index;
        index++;
    }
    select.selectedIndex = sel_index;
}

function ajax_update_cod_types_list() {
    $.ajax({
    "url":submit_url,
    "success":handler,
    "dataType":"json",
    "type":"post",
    });
}
{/literal}
</script>

<div id="open_close_cod_type" style="display:none;">
<iframe width="300" height="100" src="index.php?target=cod_types&iframe=1"></iframe>
</div>
