<select name="{$name}" multiple size="2">
{if $addons.EStore}
<option value="1"{if $value & 1} selected{/if}>{$lng.lbl_area_eshop}</option>
{/if}
{if  $addons.pos}
<option value="2"{if $value & 2} selected{/if}>{$lng.lbl_area_pos}</option>
{/if}
</select>
