{literal}
<script type="text/javascript">
/*pagespeed_no_defer*/
    var google_tag_params = {
     {/literal}
            pagetype: {if isset($google_remarketing.ptype)} {$google_remarketing.ptype}{else}''{/if},
            prodid: {if isset($google_remarketing.prod_id)} {$google_remarketing.prod_id}{else}''{/if},
            pname: {if isset($google_remarketing.prod_name)} {$google_remarketing.prod_name}{else}''{/if},
            pvalue: {if isset($google_remarketing.prod_value)} {$google_remarketing.prod_value}{else}''{/if},
            pcat: {if isset($google_remarketing.prod_cat)} {$google_remarketing.prod_cat}{else}''{/if},
            use_case: 'retail'
    {literal}
    };
</script>
{/literal}{literal}
<script type="text/javascript">
    /* <![CDATA[ */
/*pagespeed_no_defer*/
    var google_conversion_id = '{/literal}{$config.google_remarketing.gr_conversion_id}{literal}';
    var google_conversion_label = "{/literal}{$config.google_remarketing.gr_conversion_label}{literal}";
    var google_custom_params = '{/literal}{$config.google_remarketing.gr_custom_params}{literal}';
    var google_remarketing_only = '{/literal}{$config.google_remarketing.gr_only}{literal}';
    /* ]]> */
</script>
{/literal}
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt=""
             src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/{$config.google_remarketing.gr_conversion_id}/?value=0&
amp;label={$config.google_remarketing.gr_conversion_label}&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
