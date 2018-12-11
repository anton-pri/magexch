<select name="{$name}"{if $multiple} multiple="multiple" size="5"{/if}>
<option value="1"{if ($multiple && $selected.1) || (!$multiple && $value eq 1)} selected="selected"{/if}>{$lng.lbl_avail_type_avail}</option>
<option value="2"{if ($multiple && $selected.2) || (!$multiple && $value eq 2)} selected="selected"{/if}>{$lng.lbl_avail_type_ordered}</option>
<option value="3"{if ($multiple && $selected.3) || (!$multiple && $value eq 3)} selected="selected"{/if}>{$lng.lbl_avail_type_sold}</option>
<option value="4"{if ($multiple && $selected.4) || (!$multiple && $value eq 4)} selected="selected"{/if}>{$lng.lbl_avail_type_reserved}</option>
<option value="5"{if ($multiple && $selected.5) || (!$multiple && $value eq 5)} selected="selected"{/if}>{$lng.lbl_avail_type_negative}</option>
<option value="6"{if ($multiple && $selected.6) || (!$multiple && $value eq 6)} selected="selected"{/if}>Sold out</option>
</select>
