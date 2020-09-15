{if $order.status eq 'F' || $doc.status eq 'F'}
    <div>
        <a 
            href='#preloaded_staticpopup_999999' 
            rel="cms_link_staticpopup_preload" 
            title="Attempt payment again" 
            custom-data="{$doc.doc_id|default:$order.doc_id}"
        >
            Attempt again>
        </a>
    </div>    
{/if}