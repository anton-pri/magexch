<select name="{$name}"{if $multiple} multiple size="4"{/if} class="form-control">
{if !$multiple}
    <option value="0">{$lng.lbl_no}</option>
{/if}
{foreach from=$cod_types item=cod_type}
{assign var="id" value=$cod_type.cod_type_id}
    <option value="{$id}"{if (!$multiple && $id eq $value) || ($multiple && $value.$id)} selected="selected"{/if}>{$cod_type.title}</option>
{/foreach}
</select>
