{if $recommends}
{capture name=recommends}
{include file='customer/products/products_recommends.tpl' products=$recommends featured='Y'}
{/capture}
{include file='common/section.tpl' title=$lng.lbl_recommends_customer content=$smarty.capture.recommends}
{/if}
