<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="content-language" content="{$shop_language}" />
{if ($usertype eq 'P' or $usertype eq 'A') && !$app_config_file.demo.is_demo}
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
<meta name="viewport" content="width=device-width">
<meta name="description" content="{tunnel func='cw_core_get_meta'  via='cw_call' param1='description'}" />
<meta name="keywords" content="{tunnel func='cw_core_get_meta'  via='cw_call' param1='keywords'}" />
{if $usertype eq 'C' && ($main eq "subcategories" || $main eq "search") && $noindex}
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW" />
{else}
<meta name="ROBOTS" content="ALL, INDEX, FOLLOW" />
<meta name="GOOGLEBOT" content="ALL, INDEX, FOLLOW" />
{/if}
{if $usertype eq 'C' && $category_category_url ne ""}
<link rel="canonical" href="{$current_location}{$category_category_url}?view_all=all" />
{/if}
{if $usertype eq 'C' && $main eq "welcome"}
<link rel="canonical" href="{$current_location}/index.php" />
{/if}
{if $usertype eq 'C' && $navigation.page gt 1 && ($main eq "subcategories" || $main eq "search")}
{assign var='navigation_script' value=$navigation.script|amp}
<link rel="prev" href="{$current_host_location}{$navigation_script}{$navigation.page_prefix}&page={math equation="page-step" page=$navigation.page step=1}" />
{/if}
{if $usertype eq 'C' && $navigation.page lt $navigation.total_pages_minus && ($main eq "subcategories" || $main eq "search")}
{assign var='navigation_script' value=$navigation.script|amp}
<link rel="next" href="{$current_host_location}{$navigation_script}{$navigation.page_prefix}&page={math equation="page+step" page=$navigation.page step=1}" />
{/if}

{/if}

{include file='js/presets_js.tpl'}
{include_once_src file='main/include_js.tpl' src='js/common.js'}
