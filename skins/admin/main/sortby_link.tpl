{if $_search.sortby eq $fname}
{if $_search.sortdir eq 1}
<img height="6" width="7" alt="" src="{$ImagesDir}/r_bottom.gif">
{else}
<img height="6" width="7" alt="" src="{$ImagesDir}/r_top.gif">
{/if}
{/if}
<a href="index.php?target=logging&sortby={$fname}&sortdir={if $_search.sortby eq $fname}{if $_search.sortdir eq 1}0{else}1{/if}{else}0{/if}">{$title}</a>
