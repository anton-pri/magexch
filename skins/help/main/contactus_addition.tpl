<tr>
<td colspan="3" class="RegSectionTitle">{if $section_name}{$section_name}{else}{$lng.lbl_contactus}{/if}<hr size="1" noshade="noshade" /></td>
</tr>

{foreach from=$additional_fields item=v}
{if $v.avail eq "Y" && $addition_section eq $v.section}
<tr valign="middle">
<td class="FormButton">{$v.title|default:$v.field}</td>
<td>{if $v.required eq 'Y'}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
{if $v.type eq 'T'}
<input type="text" id="additional_values_{$v.field_id}" name="additional_values[{$v.field_id}]" id="additional_values_{$v.field_id}" size="32" value="{$v.value|escape}" />
{elseif $v.type eq 'C'}
<input type="checkbox" id="additional_values_{$v.field_id}" name="additional_values[{$v.field_id}]" id="additional_values_{$v.field_id}" value="Y"{if $v.value eq 'Y'} checked="checked"{/if} />
{elseif $v.type eq 'S'}
<select id="additional_values_{$v.field_id}" name="additional_values[{$v.field_id}]" id="additional_values_{$v.field_id}">
{foreach from=$v.variants item=o}
<option value='{$o|escape}'{if $v.value eq $o} selected="selected"{/if}>{$o|escape}</option>
{/foreach}
</select>
{/if}
{if $fillerror ne "" and $v.value eq "" && $v.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
{/foreach}
