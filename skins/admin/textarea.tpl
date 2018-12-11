{if !$disabled}

{assign var="safe_field" value=$name|regex_replace:"/(\[|\])/":"_"}
{*assign var="width" value=$width|default:"500"}
{assign var="height" value=$height|default:"300"*}
{assign var="width" value="570px"}
{assign var="height" value="150px"}

<div class="editor">
	<textarea name="{$name}" id="_{$safe_field}_WYS" style="width: {$width}; height: {$height}" class="editor form-control {$class}">{$data|escape}</textarea>
</div>
{if $no_wysywig ne "Y"}
<script>
    CKEDITOR.replace( '{$name}' );
</script>
{/if}
{else}
	<textarea id="{$id}" name="{$name}"{if $cols} cols="{$cols}"{/if}{if $rows} rows="{$rows}"{/if}{if $class} class="{$class}"{/if}{if $style} style="{$style}"{/if}{if $disabled} disabled="disabled"{/if}>{$data|escape:"html"}</textarea>
{/if}
