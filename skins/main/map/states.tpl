<select class="form-control" name="{$name}{if $multiple}[]{/if}" {if $profile_fields.address.state.is_required}class='required'{/if}  {if $multiple} multiple{/if} id="{$name|id}" {$style} disabled{if $county_name} onchange="cw_map_ajax_update_counties_list(this.value, '{$county_name|id}', '{$county_value}', '{$city_name|id}', '{$city_value}');{if $is_checkout} cw_check_address('{$is_main}');{/if}"{/if}>
    <option value="">{$lng.lbl_please_select}</option>
</select>
