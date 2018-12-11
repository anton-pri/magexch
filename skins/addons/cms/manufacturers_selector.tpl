<select class="form-control" {if $name ne ""} name="{$name}"{/if}{if $style ne ""} style="{$style}"{/if}{if $multiple ne ""} multiple="multiple"{if $size gt 0} size="{$size}"{/if}{/if}>
  {foreach from=$manufacturers item="manufacturer"}
    <option value="{$manufacturer.manufacturer_id}"{if $manufacturer.selected} selected="selected"{/if}>{$manufacturer.$display_field|escape}</option>
  {/foreach}
</select>
