<form action="index.php?target=ds&mode=update" method="post">
<table>
{if $sections}
{foreach from=$sections item=section}
<tr class="TableHead">
    <td>{$lng.lbl_del}</td>
    <td>{$lng.lbl_section}</td>
    <td>{$lng.lbl_title}</td>
</tr>
<tr>
    <td><input type="checkbox" name="upd[{$section.section_id}][del]" value="Y"></td>
    <td><input type="text" name="upd[{$section.section_id}][name]" value="{$section.name}"></td>
    <td><input type="text" name="upd[{$section.section_id}][title]" value="{$section.title}"></td>
</tr>
<tr>
    <td colspan="6">
<table>
<tr class="TableHead">
    <td>&nbsp;</td>
    <td>{$lng.lbl_title}</td>
    <td>{$lng.lbl_field}</td>
</tr>
{if $section.fields}
{foreach from=$section.fields item=field}
<tr>
    <td><input type="checkbox" name="upd_fields[{$section.section_id}][{$field.field_id}][del]" value="Y"></td>
    <td><input type="text" name="upd_fields[{$section.section_id}][{$field.field_id}][title]" value="{$field.title|escape}"></td>
    <td><input type="text" name="upd_fields[{$section.section_id}][{$field.field_id}][field]" value="{$field.field|escape}"></td>
</tr>
{/foreach}
{/if}
<tr>
    <td>&nbsp;</td>
    <td><input type="text" name="upd_fields[{$section.section_id}][0][title]" value=""></td>
    <td><input type="text" name="upd_fields[{$section.section_id}][0][field]" value=""></td>
</tr>
</table>
    </td>
</tr>
<tr>
    <td colspan="6"><hr/></td>
</tr>
{/foreach}
{/if}
<tr class="TableHead">
    <td colspan="6">{$lng.lbl_add_new}</td>
</tr>
<tr>
    <td colspan="6">
<table width="100%">
<tr class="TableHead">
    <td>{$lng.lbl_title}</td>
</tr>
<tr>
    <td>
<input type="text" name="upd[0][title]" value="">
    </td>
</tr>

</table>
    </td>
</tr>
</table>

<input type="submit" value="{$lng.lbl_update}">
</form>
