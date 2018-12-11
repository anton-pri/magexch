<select name="{$name}">
{if $is_please_select}
<option value="">{$lng.lbl_please_select}</option>
{/if}
<option value="FIFO" {if $value eq 'FIFO'}selected{/if}>{$lng.lbl_fifo}</option>
<option value="FILO" {if $value eq 'FILO'}selected{/if}>{$lng.lbl_filo}</option>
<option value="MPC" {if $value eq 'MPC'}selected{/if}>{$lng.lbl_medium_ponderated_cost}</option>
</select>
