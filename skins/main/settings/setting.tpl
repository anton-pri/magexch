{if $type eq "numeric"}
<input type="text" class="form-control" size="10" name="{$name}" value="{$value|formatnumeric}"{if $is_disabled} disabled{/if} />

{elseif $type eq "text"}
<input type="text" class="form-control" size="30" name="{$name}" value="{$value|escape:html}"{if $is_disabled} disabled{/if} />

{elseif $type eq 'password'}
<input type="password" class="form-control" size="30" name="{$name}" value="{$value|escape:html}"{if $is_disabled} disabled{/if} />

{elseif $type eq "checkbox"}
<input type="hidden" name="{$name}" value="N"{if $is_disabled} disabled{/if}/>
<input type="checkbox" name="{$name}"{if $value eq "Y"} checked="checked"{/if}  value="Y"{if $is_disabled} disabled{/if}/>

{elseif $type eq "textarea"}
<textarea name="{$name}" class="form-control" cols="30" rows="5"{if $is_disabled} disabled{/if}>{$value|escape:html}</textarea>

{elseif $type eq "editor"}
{include file='main/textarea.tpl' name=$name data=$value disabled=$is_disabled}

{elseif ($type eq "selector" || $type eq "multiselector") && $variants ne ''}
{if $type eq "multiselector"}
<select name="{$name}[]" class="form-control" multiple="multiple" size="5"{if $is_disabled} disabled{/if}>
{else}
<select name="{$name}"{if $auto_submit} onchange="javascript: cw_submit_form(document.processform)"{/if} class="setting_select form-control">
{/if}
{foreach from=$variants item=vitem key=vkey}
    <option value="{$vkey}"{if $vitem.selected} selected="selected"{/if}>{$vitem.name}</option>
{/foreach}
</select>

{elseif $type eq 'country'}
{include file="admin/select/country.tpl" name=$name value=$value}

{elseif $type eq 'state'}
{include file="main/select/state.tpl" name=$name default=$value required='O'}

{elseif $type eq 'date'}
{include file="main/select/date_format.tpl" name=$name value=$value}

{elseif $type eq 'time'}
{include file="main/select/time_format.tpl" name=$name value=$value}

{elseif $type eq 'log'}
{include file="main/select/log_type.tpl" name=$name value=$value}

{elseif $type eq 'currency'}
{include file="main/select/currency.tpl" name=$name value=$value}

{elseif $type eq 'shipping'}
{include file='admin/select/shipping.tpl' name="`$name`[]" values=$value multiple=true read_only=$is_disabled}

{elseif $type eq 'singleshipping'}
{include file='admin/select/shipping.tpl' name=$name values=$value multiple=false read_only=$is_disabled}

{elseif $type eq 'memberships'}
{include file='admin/select/membership.tpl' name="`$name`[]" value=$value multiple=true disabled=$is_disabled}

{elseif $type eq 'doc_status'}
{include file="main/select/doc_status.tpl" status=$value mode="select" name="`$name`[]" multiple=true extra="size=\"10\""}

{elseif $type eq 'newslists'}
{include file="main/select/newslists.tpl" name=$name value=$value}

{elseif $type eq 'image'}
<a href="index.php?target=special_images#{$name}">{$lng.lbl_upload}</a>

{elseif $type eq 'link'}
	{$value}
{/if}
