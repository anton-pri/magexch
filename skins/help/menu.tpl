{capture name=menu}
<a href="{pages_url var="help" section="contactus"}">{$lng.lbl_contact_us}</a><br />
<a href="{pages_url var="help" section="business"}">{$lng.lbl_privacy_statement}</a><br />
<a href="{pages_url var="help" section="conditions"}">{$lng.lbl_terms_n_conditions}</a><br />
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_help content=$smarty.capture.menu link_href='index.php?target=help'}
