{capture name=section}
<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
{$lng.lbl_giftreg_event_created_by} {$event_data.creator_title} {$event_data.firstname} {$event_data.lastname}
<hr noshade="noshade" size="1" width="50%" align="left" />
	</td>
</tr>

<tr>
	<td><h2>{$event_data.title}</h2></td>
</tr>

{if $event_data.description}
<tr>
	<td>{$event_data.description}</td>
</tr>
{/if}

<tr>
	<td>&nbsp;</td>
</tr>

<tr>
	<td>{include file="common/subheader.tpl" title=$lng.lbl_wish_list}</td>
</tr>

<tr>
	<td>{include file="addons/estore_gift/wl_products.tpl" wl_products=$wl_products script_name="giftreg" giftregistry="Y"}</td>
</tr>
</table>

{/capture}
{include file="common/section.tpl" title=$event_data.title content=$smarty.capture.section extra='width="100%"'}

{if $event_data.guestbook eq "Y"}
<p />
{include file="addons/estore_gift/event_guestbook.tpl"}
{/if}

{if $event_data.html_content ne ""}
<script type="text/javascript" language="JavaScript 1.2">
<!--
window.open("giftregs.php?eventid={$event_data.event_id}&mode=preview", "eventcard", "width=600,height=450,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no");
-->
</script>
{/if}
