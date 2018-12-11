<select name="{$name}" class="form-control">
    {if $type eq 'integer' || $type eq 'decimal'}
    <option value="S"{if $value eq 'S'} selected{/if}>{$lng.lbl_pfdp_slider}</option>
    <option value="R"{if $value eq 'R'} selected{/if}>{$lng.lbl_pfdp_text_range}</option>
    {*<option value="T"{if $value eq 'T'} selected{/if}>{$lng.lbl_pfdp_text}</option> *}
    <optgroup label="{$lng.lbl_pfdp_predefined_ranges}">
        <option value="P"{if $value eq 'P'} selected{/if}>{$lng.lbl_pfdp_text}</option>
        <option value="W"{if $value eq 'W'} selected{/if}>{$lng.lbl_pfdp_swatch}</option>
        <option value="E"{if $value eq 'E'} selected{/if}>{$lng.lbl_pfdp_text_swatch}</option>  
        <option value="G"{if $value eq 'G'} selected{/if}>Greed</option>
    </optgroup>
    {elseif $type eq 'selectbox' || $type eq 'multiple_selectbox'}
    <option value="T"{if $value eq 'T'} selected{/if}>{$lng.lbl_pfdp_text}</option>
    <option value="W"{if $value eq 'W'} selected{/if}>{$lng.lbl_pfdp_swatch}</option>
    <option value="E"{if $value eq 'E'} selected{/if}>{$lng.lbl_pfdp_text_swatch}</option>
    <option value="G"{if $value eq 'G'} selected{/if}>Greed</option>
    {elseif $type eq 'textarea' || $type eq 'text'}
    <option value="T"{if $value eq 'T'} selected{/if}>{$lng.lbl_pfdp_text}</option>
    <option value="R"{if $value eq 'R'} selected{/if}>{$lng.lbl_pfdp_text_range}</option>
    {else}
    <option value="T"{if $value eq 'T'} selected{/if}>{$lng.lbl_pfdp_text}</option>
    {/if}
</select>
