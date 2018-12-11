<select name="{$name}" class="form-control" id="{$name|id}" disabled{if $city_name} {$style} onchange="cw_map_ajax_update_cities_list(this.value, '{$city_name|id}', '{$city_value}');{if $is_checkout} cw_check_address('{$is_main}');{/if}"{/if}>
	<option value="">{$lng.lbl_please_select}</option>
</select>
