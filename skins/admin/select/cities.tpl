<select name="{$name}" id="{$name|id}_select" disabled style='display:none' class="form-control">
    <option value="">{$lng.lbl_please_select}</option>
</select>
<input type="text" id="{$name|id}_text" name="{$name}" class='city {if $profile_fields.address.city.is_required}required{/if} form-control' maxlength="64" value="{$value|escape}"{if $readonly} disabled{/if} />