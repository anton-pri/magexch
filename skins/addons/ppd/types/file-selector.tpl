{if $attribute.type eq 'file-selector'}

<script type="text/javascript">
    {if $config.ppd.ppd_icons_dir ne ''}
    var ppd_dir = "{$config.ppd.ppd_icons_dir|escape}";
    {literal}
    var ppd_pattern=new RegExp("^/");
    if (ppd_pattern.test(ppd_dir) == false) {
        ppd_dir = '/' + ppd_dir;
    }
    {/literal}
    {/if}
</script>
{assign var='cleaned_field_name' value=$fieldname|regex_replace:"/[^A-Za-z0-9 ]/":"_"}
{assign var='cleaned_field_name' value="`$cleaned_field_name``$attribute.field`"}
<input id="path_{$cleaned_field_name}" type="text" size="22" maxlength="255" name="{$fieldname}[{$attribute.field}]" value="{$attribute.value|escape}" readonly="readonly" style="margin-bottom: 7px; clear: both; display: block; float: none;" />
<div style="float:left;"><input id="{$cleaned_field_name}" type="button" value="{$lng.lbl_change|strip_tags:false|escape}" onclick="javascript: ppd_popup_files(this, 'name_', 'path_', false);" style="height:20px"/></div>
<div style="float:right;"><input id="delete_{$cleaned_field_name}" type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: $('#path_{$cleaned_field_name}').val(''); cw_fire_event($('#path_{$cleaned_field_name}').get(0), 'keydown');" style="height:20px"/></div>

    <div class="clear"></div>
{/if}
