{if $all_languages_cnt gt 1}
<div class="lng_flags" valign="top">
<span>{$lng.lbl_language}:&nbsp;</span>
{foreach from=$all_languages key=code item=language}
<a href="{$lng_urls.$code}"{if $code eq $shop_language} class="selected"{/if}><img src="{$ImagesDir}/flags/{$code}.png" title="{$language.language}" /></a>
{/foreach}
</div>
{/if}
