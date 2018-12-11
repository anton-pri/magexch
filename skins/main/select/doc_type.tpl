<select name="{$name}" id="{$name|id}"{if $multiple} multiple size="{$multiple}"{/if}{if $size} size="{$size}"{/if}{if $read_only} disabled{/if}>
{*
<option value="A"{if $value eq 'A'} selected{/if}>{$lng.lbl_doc_info_A}</option>
*}
{if $accl.21}
<option value="C"{if $value eq 'C'} selected{/if}>{$lng.lbl_doc_info_C}</option>
{/if}
{if $accl.0700}
<option value="G"{if $value eq 'G'} selected{/if}>{$lng.lbl_doc_info_G}</option>
{/if}
{if $accl.20}
<option value="I"{if $value eq 'I'} selected{/if}>{$lng.lbl_doc_info_I}</option>
{/if}
{if $accl.18}
<option value="O"{if $value eq '0'} selected{/if}>{$lng.lbl_doc_info_O}</option>
{/if}
{if $accl.19}
<option value="S"{if $value eq 'S'} selected{/if}>{$lng.lbl_doc_info_S}</option>
{/if}
{if $accl.2300}
<option value="P"{if $value eq 'P'} selected{/if}>{$lng.lbl_doc_info_P}</option>
{/if}
{if $accl.32}
<option value="D"{if $value eq 'D'} selected{/if}>{$lng.lbl_doc_info_D}</option>
{/if}
</select>
