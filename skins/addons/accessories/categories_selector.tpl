<select{if $name ne ""} name="{$name}"{/if}{if $style ne ""} style="{$style}"{/if}{if $multiple ne ""} multiple="multiple"{if $size gt 0} size="{$size}"{/if}{/if}>
  {foreach from=$categories item="category"}
    <option value="{$category.category_id}"{if $category.selected} selected="selected"{/if}>{$category.$display_field|escape}</option>
  {/foreach}
</select>
