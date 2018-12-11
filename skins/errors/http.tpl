{if $error_code eq '404'}
{capture name=section}
<p />
{$lng.txt_error_http_404_upper_message}<br />
<p />
<script type="text/javascript">
{literal}
function run_search_404(suggested_search_string) {
  $('[name="posted_data[substring]"]').val(suggested_search_string);
  $('[name="posted_data[substring]"]').parent().submit();
}
{/literal}
</script>

<div class="suggested_search_404">{$lng.lbl_please_try_find_products}</div>
<p />
{capture assign="ss_string"}
{foreach from=$suggested_searches item=ssi name=sstr}{$ssi}{if !$smarty.foreach.sstr.last} {/if}{/foreach}
{/capture}
{foreach from=$suggested_searches item=ssi}
<a class="suggested_search_404" href="javascript: run_search_404('{$ssi|escape}'); void(0);">{$ssi}</a><br />
{if $ssi eq $ss_string}{assign var="ss_string" value=""}{/if}
{/foreach}
{if $ss_string ne ''}
<a class="suggested_search_404" href="javascript: run_search_404('{$ss_string|escape}'); void(0);">{$ss_string}</a><br />
{/if}
<p />
{$lng.txt_error_http_404_lower_message}<br />
{/capture}
{include file='common/section.tpl' title=$lng.lbl_page_not_found content=$smarty.capture.section style="class404"}

{else}
<h3>{$lng.lbl_server_http_error}</h3>
{lng name="txt_server_http_error_`$error_code`"}
<br />
{$lng.lbl_code_404_page} {$error_code}
{/if}
