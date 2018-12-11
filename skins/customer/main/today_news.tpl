{if !$news_message}
{$lng.txt_no_news_available}
{else}
<b>{$news_message.send_date|date_format:$config.Appearance.date_format}</b><br />
{$news_message.body}
<br /><br />
<a href="index.php?target=news">{$lng.lbl_previous_news}</a>
{/if}
<hr />
