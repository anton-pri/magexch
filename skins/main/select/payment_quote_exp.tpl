<select name="{$name}">
<option value="1"{if $value eq 1} selected{/if}>{$lng.lbl_doc_date}</option>
<option value="2"{if $value eq 2} selected{/if}>{$lng.lbl_month_end}</option>
<option value="3"{if $value eq 3} selected{/if}>{$lng.lbl_custom_date}</option>
<option value="4"{if $value eq 4} selected{/if}>{$lng.lbl_fixed_day}</option>
<option value="5"{if $value eq 5} selected{/if}>{$lng.lbl_fixed_day_from_month_day}</option>
</select>
