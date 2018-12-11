<link rel="stylesheet" href="{$SkinDir}/css/fonts.css" type="text/css" />
{*load_defer file="print.css" type="css" media="print"*}
{load_defer file="css/bootstrap.css" type="css" media="screen"}
{load_defer file="css/oneui.min-1.2.css" type="css" media="screen"}
{load_defer file="css/jquery.dataTables.min.css" type="css" media="screen"}
{load_defer file="css/admin.css" type="css" media="screen"}


{*
{load_defer file="general.css" type="css" media="screen"}

{if $home_style eq 'iframe' || $home_style eq 'popup'}
{load_defer file="iframe.css" type="css" media="screen"}
{/if}

{load_defer file="admin.css" type="css" media="screen"}
{load_defer file="ezmark.css" type="css" media="screen"}

{if $home_style eq 'iframe'}
{load_defer file="admin_iframe.css" type="css" media="screen"}
{/if}
*}
{if $include.css}
{foreach from=$include.css item=file}
{load_defer file="`$file`" type="css" media="all"}
{/foreach}
{/if}

{load_defer file="jquery/css/ui-lightness/jquery-ui.admin.css" type="css" media="all"}
{load_defer file="jquery/tagsinput/jquery.tagsinput.css" type="css" media="all"}
{load_defer file="tooltip.css" type="css"}
{load_defer file="jquery/file_upload/css/jquery.fileupload.css" type="css" media="screen"}
{load_defer file="jquery/colorpicker/spectrum.css" type="css" media="screen"}

{load_defer_code type="css"}

{if $all_languages_cnt eq 1}
    <style>.multilan {ldelim}
        background: none;
    {rdelim}
    </style>
{/if}
