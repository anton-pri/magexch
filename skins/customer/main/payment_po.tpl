<script type="text/javascript">
<!--
requiredFields[0] = ["PO_Number", "{$lng.lbl_po_number}"];
requiredFields[1] = ["Company_name", "{$lng.lbl_company_name}"];
requiredFields[2] = ["Name_of_purchaser", "{$lng.lbl_name_of_purchaser}"];
requiredFields[3] = ["Position", "{$lng.lbl_position}"];
-->
</script>
<table cellspacing="0" cellpadding="2">

{if $hide_header ne "Y"}
<tr valign="middle">
<td height="20" colspan="3"><b>{$lng.lbl_po_information}</b><hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr valign="middle">
<td align="right">{$lng.lbl_po_number}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" size="32" id="PO_Number" name="PO_Number" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_company_name}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" size="32" id="Company_name" name="Company_name" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_name_of_purchaser}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" size="32" id="Name_of_purchaser" name="Name_of_purchaser" />
</td>
</tr>

<tr valign="middle">
<td align="right">{$lng.lbl_position}</td>
<td><font class="Star">*</font></td>
<td nowrap="nowrap">
<input type="text" size="32" id="Position" name="Position" />
</td>
</tr>

</table>
