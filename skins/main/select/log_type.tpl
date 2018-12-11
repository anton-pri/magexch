<select name="{$name}"{if $is_disabled} disabled{/if}>
<option value="N"{if $value eq "N"} selected="selected"{/if}>{$lng.lbl_log_act_nothing}</option>
<option value="L"{if $value eq "L"} selected="selected"{/if}>{$lng.lbl_log_act_log}</option>
<option value="E"{if $value eq "E"} selected="selected"{/if}>{$lng.lbl_log_act_email}</option>
<option value="LE"{if $value eq "LE"} selected="selected"{/if}>{$lng.lbl_log_act_log_n_email}</option>
</select>
