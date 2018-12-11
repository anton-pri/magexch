{if $type eq 'iframe'}
<iframe marginwidth="0" marginheight="0" frameborder="0" scrolling="no" style="border-width: 0px; border-style: none;" width="{$banner.banner_x}px" height="{$banner.banner_y}px" src="{$catalogs.customer}/index.php?target=banner&bid={$banner.banner_id}&salesman={$salesman}&type=iframe{if $product_id > 0}&product_id={$product_id}{/if}"></iframe>
{else}
{if $banner.banner_type eq 'G'}
{if $banner.legend ne ''}
<table>
<tr>
{if $banner.direction eq 'U'}<td align="center">{$banner.legend|escape}</td></tr><tr>{/if}
{if $banner.direction eq 'L'}<td valign="middle">{$banner.legend|escape}</td>{/if}
<td>
{/if}
<a href="{$catalogs.customer}/index.php?target=banner&bid={$banner.banner_id}{if $salesman}&salesman={$salesman}{/if}"{if $banner.open_balnk eq 'Y'} target="_blank"{/if} border="0"{if $banner.alt ne ''} alt="{$banner.alt|escape}"{/if} />{$catalogs.customer}/index.php?target=banner&bid={$banner.banner_id}{if $salesman}&salesman={$salesman}{/if}</a>
{if $banner.legend ne ''}
</td>
{if $banner.direction eq 'D'}</tr><tr><td align="center">{$banner.legend|escape}</td>{/if}
{if $banner.direction eq 'R'}<td valign="middle">{$banner.legend|escape}</td>{/if}
</tr>
</table>
{/if}
{elseif ($banner.banner_type eq 'M' || $banner.banner_type eq 'T' || $banner.banner_type eq 'P')}
{if $type ne 'ssi'}
<script type="text/javascript" language="JavaScript 1.2" src="{$catalogs.customer}/banner.php?bid={$banner.banner_id}{if $salesman ne ''}&salesman={$salesman}{/if}{if $product_id > 0}&product_id={$product_id}{/if}"></script>
{else}
{/if}
{/if}
{/if}
