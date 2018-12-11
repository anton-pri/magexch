{include_once file='categories_ajax/include_js.tpl'}
{assign var='id' value=$name|id}

{if $read_only}

{select_category category_id=$value|default:$default_category_id assign="category"}
#{$category.category_id} {$category.category}
{elseif !$multiple}
{select_category category_id=$value|default:$default_category_id assign="category"}
<div class="form-inline">
  <div class="form-group">
    <input type="text" name="{$name}" id="{$id}_catid" value="{$category.category_id}" size="7" style="width: 70px;"{if $disabled} disabled{/if} class="form-control" />
  </div>
  <div class="form-group">
    <input type="text" id="{$id}_catname" value="{$category.category}" size="28" class="category form-control {if $class}{$class}{/if}" {if $disabled} disabled{else} readonly{/if} />
  </div>
  <div class="form-group">
    <img src="{$ImagesDir}/categories.png" width="16" onclick="javascript: return cw_show_categories('{$category.category_id}', '{$id}', '');" id="{$id}_link" class="category_selector" />
  </div>
</div>
<div id="{$id}_show_category" style="display:none">
    <div class="bd"><div class="category_ajax" id="{$id}_body">{$lng.lbl_loading}</div></div>
</div>
{else}
    {if $disabled}
{if $value}
{foreach from=$value key=index item=category_id}
{select_category category_id=$category_id assign='category'}
<input type="text" value="{$category.category_id}" size="7" style="width: 70px;" disabled class="form-control"  />
<input type="text" value="{$category.category|escape}" style="width: 245px;" size="28" disabled class="form-control"  /><br/>
{/foreach}
{else}
{$lng.lbl_none}
{/if}
    {else}

<div id="{$id}_show_category" style="display:none">
    <div class="bd"><div class="category_ajax" id="{$id}_body">{$lng.lbl_loading}</div></div>
</div>
<table cellpadding="0" cellspacing="0" class="category_select" border="0">
<tr>
    <td id="{$id}_box_1"><input class="form-control" type="text" name="{$name}" id="{$id}_catid_0" value="" style="width: 70px;" size="7"{if $disabled} disabled{/if} /></td>
    <td id="{$id}_box_2"><input class="form-control" type="text" name="name_{$name}" id="{$id}_catname_0" value="" class="category" size="28"{if $disabled} disabled{else} readonly{/if} /></td>
    <td id="{$id}_box_3">
        <img src="{$ImagesDir}/categories.png" width="16" onclick="javascript: cat_id = explode('_', this.id); index=cat_id.pop(); el = document.getElementById('{$id}_catid_'+index).value; return cw_show_categories(el, '{$id}', index);" id="{$id}_link_0" class="pointer" />
    </td>
    <td id="{$id}_add_button" class="add">{include file='main/multirow_add.tpl' mark=$id}</td>
</tr>
</table>

{*
<div class="form-inline">
  <div class="form-group">
    <input type="text" name="{$name}" id="{$id}_catid_0" value="" style="width: 70px;" size="7"{if $disabled} disabled{/if} class="form-control" />
  </div>
  <div class="form-group">
    <input type="text" name="name_{$name}" id="{$id}_catname_0" value="" class="form-control" size="28"{if $disabled} disabled{else} readonly{/if} />
  </div>
  <div class="form-group">
        <img src="{$ImagesDir}/categories.png" class="category_selector" width="16" onclick="javascript: cat_id = explode('_', this.id); index=cat_id.pop(); el = document.getElementById('{$id}_catid_'+index).value; return cw_show_categories(el, '{$id}', index);" id="{$id}_link_0" class="pointer" />
  </div>
  <div class="form-group">
    {include file='main/multirow_add.tpl' mark=$id}
  </div>
</div>
*}
{if $value}
<script type="text/javascript">
{foreach from=$value key=index item=category_id}
{select_category category_id=$category_id assign='category'}
{if $category}
add_inputset_preset('{$id}', document.getElementById('{$id}_add_button'), false, 
    [
    {ldelim}regExp: /{$id}_catid/, value: '{$category.category_id}'{rdelim},
    {ldelim}regExp: /{$id}_catname/, value: '{$category.category|escape:json}'{rdelim},
    {ldelim}regExp: /{$id}_link/, value: '0'{rdelim},
    ]
);
{/if}
{/foreach}
</script>
{/if}
    {/if}
{/if}
