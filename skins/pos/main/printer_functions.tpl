{if $accl.1100}
{include file='buttons/button.tpl' button_title=$lng.lbl_pos_close_day href="javascript: document.pos_device_applet.close_day()"}<br/>
{/if}
{include file='addons/pos/applet.tpl' params="`$catalogs.$app_area`/index.php?target=ajax&mode=aom&action=functions"}
