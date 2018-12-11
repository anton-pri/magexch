{if $section ne "login_customer"}{capture name="dialog"}{/if}
{*if $section eq "general"}
{include file='common/page_title.tpl' title=$lng.lbl_help_zone }
{elseif $section eq "password"}
{include file='common/page_title.tpl' title=$lng.lbl_forgot_password }
{elseif $section eq "contactus"}
{include file='common/page_title.tpl' title=$lng.lbl_contact_us }
{elseif $section eq "faq"}
{include file='common/page_title.tpl' title=$lng.lbl_faq }
{elseif $section eq "business"}
{include file='common/page_title.tpl' title=$lng.lbl_privacy_statement }
{elseif $section eq "conditions"}
{include file='common/page_title.tpl' title=$lng.lbl_terms_n_conditions }
{elseif $section eq "about"}
{include file='common/page_title.tpl' title=$lng.lbl_about_our_site }
{elseif $section eq "login_customer"}
{include file='common/page_title.tpl' title=$lng.lbl_log_in }
{/if*} 

{if $section eq "general"}
{assign var=page_title value=$lng.lbl_help_zone }
{elseif $section eq "password"}
{assign var=page_title value=$lng.lbl_forgot_password }
{elseif $section eq "contactus"}
{assign var=page_title value=$lng.lbl_contact_us }
{elseif $section eq "faq"}
{assign var=page_title value=$lng.lbl_faq }
{elseif $section eq "business"}
{assign var=page_title value=$lng.lbl_privacy_statement }
{elseif $section eq "conditions"}
{assign var=page_title value=$lng.lbl_terms_n_conditions }
{elseif $section eq "about"}
{assign var=page_title value=$lng.lbl_about_our_site }
{elseif $section eq "login_customer"}
{assign var=page_title value=$lng.lbl_log_in }
{/if} 


{include file="help/main/`$section`.tpl"}
{if $section ne "login_customer"}
{/capture}
{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.dialog additional_class="help" title="$page_title"}
{/if}
