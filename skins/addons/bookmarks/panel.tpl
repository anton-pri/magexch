    <div id='favorites'>
       <ul class="list list-bookmarks">
        {foreach from=$bookmarks item=bm}
            <li class="{cycle values='cycle,'}">
              <a href='index.php?target=bookmarks&action=delete&id={$bm.url|md5}' class='control btn btn-xs btn-danger' rel='nofollow'><i class="fa fa-times"></i></a>&nbsp;<a href='{$bm.url}' rel='bookmark' class="font-w600 text-gray-darker">{$bm.name}</a>
            </li>
        {/foreach}
        </ul>
    </div>

{if $recents}
    <div id='recent'>
        <h4>Recent pages</h4>
        <ul>
        {foreach from=$recents item=rec}
            <li class="{cycle values='cycle,'}"><a href='{$rec.url}' rel='bookmark'>{$rec.name}</a></li>
        {/foreach}
        </ul>
    </div>
{/if}
