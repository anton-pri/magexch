{* states list *}
{*tunnel func='cw_map_get_states' assign='states' via='cw_call'*}
{if $states}
<select name="{$name}" id="{$name|id}"{if $disabled} disabled{/if} class="form-control {if $class ne ""}{$class}{/if}">
<option value="">{$lng.lbl_state}</option>
{foreach from=$states item=state}
<option value="{$state.state_code}" {if $default eq $state.state_code || (!$default and $state.state_code eq $config.General.default_country)} selected="selected"{/if}>{$state.state}{if $show_code}&nbsp;({$state.state_code}){/if}</option>
{/foreach}
</select>
{else}
<input type="text" class="form-control" name="{$name}" id="{$name|id}"{if $disabled} disabled{/if} value="{$default}" placeholder="{$lng.lbl_state}" />
{/if}
