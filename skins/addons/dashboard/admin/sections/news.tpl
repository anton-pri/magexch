<ul class='dashboard_news list list-timeline pull-t'>
{foreach from=$rss item='rss_news'}
  <li class="news_item">
    <i class="fa fa-cog list-timeline-icon bg-primary-dark"></i>
    <div class="list-timeline-content">
	 <span class="a_title"><a href='{$rss_news->link}'>{$rss_news->title}</a></span> -
	{$rss_news->description}
    </div> 
  </li>
{/foreach}
</ul>
