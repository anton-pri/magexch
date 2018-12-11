<select name="{$name}"{if $multiple} multiple{/if}>
<option value='1'{if $value eq 1} selected{/if}>{$lng.lbl_purchased}</option>
<option value='2'{if $value eq 2} selected{/if}>{$lng.lbl_sold}</option>
</select>
