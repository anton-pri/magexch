<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- cw@content_lng [ -->
<meta http-equiv="content-language" content="{$shop_language}" />
<!-- cw@content_lng ] -->
{include file='elements/favicon.tpl'}
{if ($usertype eq 'P' or $usertype eq 'A')}
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
{else}
<meta name="REVISIT-AFTER" content="1 DAYS" />
<meta name="DOCUMENT-TYPE" content="PUBLIC" />
<meta name="DOCUMENT-DISTRIBUTION" content="GLOBAL" />
<meta name="COPYRIGHT" CONTENT="{$config.Company.company_name}" />
<meta name="DOCUMENT-STATE" content="DYNAMIC" />
<meta name="CLASSIFICATION" content="SHOPPING" />
<meta name="DISTRIBUTION" CONTENT="GLOBAL" />
<meta name="RATING" content="GENERAL" />
<meta name="description" content="{tunnel func='cw_core_get_meta' via='cw_call' param1='description'}" />
{if $config.General.enable_meta_kaywords eq 'Y'}
<meta name="keywords" content="{tunnel func='cw_core_get_meta' via='cw_call' param1='keywords'}" />
{/if}
<!-- current_target: {$current_target}; main: {$main} -->
{if $usertype eq 'A' || $noindex}
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
<meta name="GOOGLEBOT" content="NOINDEX,NOFOLLOW" />
{else}
<meta name="ROBOTS" content="ALL, INDEX, FOLLOW" />
<meta name="GOOGLEBOT" content="ALL, INDEX, FOLLOW" />
{/if}
{if $search_prefilled.sort_field eq 'title' && !$search_prefilled.sort_direction}
{if $usertype eq 'C' && $navigation.page gt 1 && ($main eq "subcategories" || $main eq "search")}
{assign var='navigation_script' value=$navigation.script|amp}
{math equation="page-step" page=$navigation.page step=1 assign='_page'}
<link rel="prev" href="{$current_host_location}{build_url url=$navigation_script page=$_page}" />
{/if}
{if $usertype eq 'C' && $navigation.page lt $navigation.total_pages_minus && ($main eq "subcategories" || $main eq "search")}
{assign var='navigation_script' value=$navigation.script|amp}
{math equation="page+step" page=$navigation.page step=1 assign='_page'}
<link rel="next" href="{$current_host_location}{build_url url=$navigation_script page=$_page}" />
{/if}
{/if}
{/if}
{include file='elements/canonical.tpl'}
{include file='js/presets_js.tpl'}
{include_once_src file='main/include_js.tpl' src='js/common.js'}
