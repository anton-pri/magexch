{* TODO: move target names to PHP code so every addon could add own page *}
{* Hidden domains select see MAG-92
{if $user_account.customer_id && in_array($current_target, array('categories', 'products', 'manufacturers', 'pages', 'speed_bar', 'shipping', 'payments','cms','taxes'))}
{tunnel func='cw_md_get_domains' assign='domains'}
<div class="domain-selection">
    <form action="{$catalogs.$app_area}/index.php" method="post" name="domains_form">
    <input type="hidden" name="action" value="set-domains-filter" />
    <input type="hidden" name="l_redirect" value="{$request_uri}" />
        <select class="form-control" name="domain_selection" onchange="cw_submit_form('domains_form');">
        <option value="-1"{if $current_domain eq -1} selected{/if}>{$lng.lbl_any_domains}</option>
        <option value="0"{if $current_domain eq 0} selected{/if}>{$lng.lbl_assigned_to_all_domains}</option>
        {foreach from=$domains item=domain}
        <option value="{$domain.domain_id}"{if $current_domain eq $domain.domain_id} selected{/if}>{$domain.name}</option>
        {/foreach}
        </select>
    </form>
</div>
{if $current_domain neq -1}{capture name=current_domain_warning}1{/capture}{/if}
{/if}
*}
