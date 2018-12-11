{if $mode eq 'unsubscribed'}
    {include file='addons/news/unsubscribe_confirmation.tpl'}
{/if}
{if $mode eq 'subscribed'}
    {include file='addons/news/subscribe_confirmation.tpl'}
{/if}
{if !$news_messages}
{$lng.txt_no_news_available}
{else}
{section name=idx loop=$news_messages}
{capture name=section}
<b>{$news_messages[idx].subject}</b>
<br /><br />
{if $news_messages[idx].allow_html eq "N"}
{$news_messages[idx].body|replace:"\n":"<br />"}
{else}
{$news_messages[idx].body}
{/if}
{/capture}
{include file='common/section.tpl' title=$news_messages[idx].send_date|date_format:$config.Appearance.date_format content=$smarty.capture.section}
{/section}
{/if}
