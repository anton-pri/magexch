<select name="{$name}">
<option value="M"{if $value eq 'M'} selected{/if}>{$lng.lbl_banner_type_media}</option>
<option value="T"{if $value eq 'T'} selected{/if}>{$lng.lbl_banner_type_text}</option>
<option value="P"{if $value eq 'P'} selected{/if}>{$lng.lbl_banner_type_image}</option>
<option value="G"{if $value eq 'G'} selected{/if}>{$lng.lbl_banner_type_group}</option>
</select>
