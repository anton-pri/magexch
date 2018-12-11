{include file='common/page_title.tpl' title=$lng.lbl_add_static_page}

<div class="box">
<form action="index.php?target={$current_target}" method="post" name="pages_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_id" value="{$page_id}" />

{*
<div class="input_field_1">
    <label>{$lng.lbl_url}</label>
    {$catalogs.front}/
    <input type="text" id="url" name="page_data[url]" value="{$page_data.url}" />
    .html<br/>
    {if $page_data.url}
    <a href="{$catalogs.front}/{$page_data.url|escape}.html" target=_blank>{$lng.lbl_page}</a>
    {/if}
</div>
*}
{if $page_data.page_id}
<div class="input_field_0">
    <label>{$lng.lbl_url}</label>
    http://{$app_http_host}{pages_url var="pages" page_id=$page_data.page_id}
</div>
{/if}
<div class="input_field_1">
    <label>{$lng.lbl_title}</label>
    <input type="text" id="title" name="page_data[title]" value="{$page_data.title|escape}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_content}</label>
    {include file='main/textarea.tpl' name='page_data[content]' data=$page_data.content init_mode='exac'}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_active}</label>
    <input type="hidden" name="page_data[active]" value="0" />
    <label><input type="checkbox" name="page_data[active]" value="1" {if $page_data.active} checked{/if} /></label>
</div>

{include file='main/attributes/object_modify.tpl'}

{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript: cw_submit_form('pages_form')" acl='__2906'}
</form>

</div>
