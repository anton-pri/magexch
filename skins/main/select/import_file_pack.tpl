<select name="{$name}">
    <option value=''{if $value eq ''} selected="selected"{/if}>{$lng.lbl_file_format_plain}</option>
    <option value='zip'{if $value eq 'zip'} selected="selected"{/if}>{$lng.lbl_file_format_zip}</option>
    <option value='tgz'{if $value eq 'tgz'} selected="selected"{/if}>{$lng.lbl_file_format_tgz}</option>
    <option value='gzip'{if $value eq 'gzip'} selected="selected"{/if}>{$lng.lbl_file_format_gzip}</option>
</select>
