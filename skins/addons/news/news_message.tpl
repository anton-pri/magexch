<div class="news">
    <div class="title">{$news.send_date|date_format:$config.Appearance.date_format} - {$news.subject}</div>
    <div class="body">{$news.body|strip_tags|replace:"\n":"<br />"|truncate:95:"...":true}</div>
    <a href="{$current_location}/index.php?target=news" class="NewsLink">{$lng.lbl_read_more}</a>
</div>
