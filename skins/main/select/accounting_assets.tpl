{if !$value}{assign var='value' value=3}{/if}
<select name="{$name}"{if $multiple} multiple{/if}>
<option value='1'{if $value eq 1} selected{/if}>{$lng.lbl_increment}</option>
<option value='2'{if $value eq 2} selected{/if}>{$lng.lbl_decrement}</option>
<option value='3'{if $value eq 3} selected{/if}>{$lng.lbl_unchanged}</option>
</select>
