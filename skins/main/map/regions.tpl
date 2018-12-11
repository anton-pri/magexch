<select class="form-control" name="{$name}" id="{$name|id}" {$style} disabled{if $county_name} onchange="cw_map_ajax_update_states_list('{$country_value|default:$config.General.default_country}', this.value, '{$state_name|id}', '{$state_value|default:$config.General.default_state}', '{$county_name|id}', '{$county_value}');{if $is_checkout} cw_check_address('{$is_main}');{/if}"{/if}>
<option value="">{$lng.lbl_please_select}</option>
</select>
