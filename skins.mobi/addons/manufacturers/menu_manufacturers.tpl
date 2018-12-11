{select_manufacturer_menu assign='manufacturers_menu'}
{if $manufacturers_menu}
{capture name=menu}
<script type="text/javascript">
var manuf_pages= new Array();
manuf_pages['0'] = '{pages_url var="manufacturers"}';
{section name=mid loop=$manufacturers_menu}
manuf_pages[{$manufacturers_menu[mid].manufacturer_id}] = '{pages_url var="manufacturers" manufacturer_id=$manufacturers_menu[mid].manufacturer_id}';
{/section}
</script>
{select_manufacturer_menu assign='manufacturers_menu_image' is_image=1}
{if $manufacturers_menu_image}
{foreach from=$manufacturers_menu_image item=manufacturer name=manufacturers}
{if $smarty.foreach.manufacturers.index < 3}
<a href="{pages_url var="manufacturers" manufacturer_id=$manufacturer.manufacturer_id}">{include file='common/thumbnail.tpl' image=$manufacturer.image}</a>
{/if}
{/foreach}
<br />
{/if}

<a href="{$customer.catalog}/index.php?target=manufacturers" class="more">{$lng.lbl_more_short}</a>

{*

<select onchange="javascript:document.location.href = manuf_pages[this.value];" class="w100">
<option value="0">{$lng.lbl_choose}</option>
{section name=mid loop=$manufacturers_menu}
<option value="{$manufacturers_menu[mid].manufacturer_id}">{$manufacturers_menu[mid].manufacturer}</option>
{/section}
<option value="0">{$lng.lbl_other}</option>
</select>

*}
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_manufacturers content=$smarty.capture.menu style='manuf'}
{/if}
