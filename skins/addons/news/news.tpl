<table style="border: #000000 solid 1px" width="100%">
<tr><td>

<b>{$news.send_date|date_format:$config.Appearance.date_format} - {$news.subject}</b>
<br />
{if $news.allow_html eq "N"}
{$news.body|replace:"\n":"<br />"}
{else}
{$news.body}
{/if}

</td></tr>
</table>
