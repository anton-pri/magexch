<select name="{$name|default:"delimiter"}"{if $id} id="{$id}"{/if}>
	<option value=";"{if $value eq ";"} selected="selected"{/if}>{$lng.lbl_semicolon}</option>
	<option value=","{if $value eq ","} selected="selected"{/if}>{$lng.lbl_comma}</option>
	<option value="\t"{if $value eq "\t"} selected="selected"{/if}>{$lng.lbl_tab}</option>
</select>
