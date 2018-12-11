<select name="{$name}"{if $multiple} multiple{/if}>
<option value='1'{if $value & 1} selected{/if}>{$lng.lbl_commercial_law_trans_1}</option>
<option value='2'{if $value & 2} selected{/if}>{$lng.lbl_commercial_law_trans_2}</option>
<option value='4'{if $value & 4} selected{/if}>{$lng.lbl_commercial_law_trans_3}</option>
</select>
