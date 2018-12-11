{load_defer file="jquery/css/ui-lightness/jquery-ui.custom.css" type="css" media="screen"}
{load_defer file="general.css" type="css" media="screen"}
<link rel="stylesheet" type="text/css" href="{$SkinDir}/import.css" />
{load_defer file="customer.css" type="css" media="screen"}
{load_defer file="owl.carousel.css" type="css" media="screen"}
{load_defer file="owl.theme.css" type="css" media="screen"}
{load_defer file="print.css" type="css" media="print"}

{load_defer file="tooltip.css" type="css"}
{load_defer file="jquery/file_upload/css/jquery.fileupload.css" type="css" media="screen"}

{if $addons.estore_category_tree}
{load_defer file="addons/estore_category_tree/styles.css" type="css" media="screen"}
{/if}
{if $addons.estore_gift}
{load_defer file="addons/estore_gift/styles.css" type="css" media="screen"}
{/if}
{if $home_style eq 'iframe' || $home_style eq 'popup'}
{load_defer file="iframe.css" type="css" media="screen"}
{/if}

{if $include.css}
{foreach from=$include.css item=file}
{load_defer file="`$file`" type="css" media="all"}
{/foreach}
{/if}
{load_defer file="uniform.default.css" type="css" media="screen"}


{load_defer_code type="css"}
