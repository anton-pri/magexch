<p />
{capture name=section}
{$lng.txt_publicity_msg}
{/capture}
{include file="common/section.tpl" title=$lng.lbl_publicity content=$smarty.capture.section extra='width="100%"'}
