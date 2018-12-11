{select_country assign='countries'}
{include_once_src file='main/include_js.tpl' src='js/change_country_ajax.js'}
{include_once file='main/map/ajax_define.tpl'}

<select class="form-control" name="{$name}" id="{$name|id}"{if $disabled} disabled{/if}{if $region_name && $region_enabled} onchange="cw_map_ajax_update_regions_list(this.value, '{$region_name|id}', '{$region_value}', '{$state_name|id}', '', '{$county_name|id}', '');{if $is_checkout} cw_check_address('{$is_main}');{/if}"{elseif $state_name && $state_enabled} onchange="cw_map_ajax_update_states_list(this.value, '-1', '{$state_name|id}', '', '{$county_name|id}', '');{if $is_checkout} cw_check_address('{$is_main}');{/if}"{/if}>
<option value="">{$lng.lbl_please_select}</option>
{foreach from=$countries item=country}
<option value="{$country.country_code}"{if $default eq $country.country_code} selected="selected"{/if}>{$country.country}{if $show_code}&nbsp;({$country.country_code}){/if}</option>
{/foreach}
</select>
{if $region_name && $region_enabled}
<script type="text/javascript">
cw_map_ajax_update_regions_list('{$default|default:$config.General.default_country}', '{$region_name|id}', '{$region_value}', '{$state_name|id}', '{$state_value|default:$config.General.default_state}', '{$county_name|id}', '{$county_value|default:$config.General.default_county}', '{$show_code}');
</script>
{/if}
{if $state_name && $state_enabled}
<script type="text/javascript">
cw_map_ajax_update_states_list('{$default}', '-1', '{$state_name|id}', '{$state_value|default:$config.General.default_state}', '{$county_name|id}', '{$county_value}', '{$show_code}');
</script>
{/if}
