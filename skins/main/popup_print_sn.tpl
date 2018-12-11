{if $serial_numbers}
<table cellpadding="10">
{foreach from=$serial_numbers item=sn}
<tr>
    <td style="border: 1px solid black">{$sn.sn}</td>
</tr>
{/foreach}
</table>
{/if}
