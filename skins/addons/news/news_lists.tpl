{capture name=section}
<form action="index.php?target=news" method="post">
<input type="hidden" name="action" value="subscribe" />
<input type="hidden" name="newsemail" value="{$newsemail|escape}" />
<table>
{foreach from=$lists item=list key=k}
<tr>
  <td>
     <input type="checkbox" name="lists[{$k}][listid]" id="lists_{$k}" value="{$list.listid}" checked="checked" />
  </td>
  <td>
     <label for="lists_{$k}">
       <b>{$list.name}</b>
     </label>
  </td>
</tr>
<tr>
  <td>&nbsp;</td>
  <td>
     <label for="lists_{$k}">
       <i>{$list.descr}</i>
     </label>
  </td>
</tr>
{/foreach}
<tr>
  <td colspan="2">
    <br />
    <input type="submit" value="{$lng.lbl_subscribe|strip_tags:false|escape}" />
  </td>
</tr>
</table>
</form>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_news_subscribe_to_newslists content=$smarty.capture.section extra='width="100%"'}
