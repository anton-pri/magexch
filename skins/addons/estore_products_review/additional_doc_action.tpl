{if $current_area eq 'A' && $doc.type eq 'O'}
    <li id="additional_doc_action">
        <a class="button" href="javascript: void(0);" onclick="blockElements('additional_doc_action',true);ajaxGet('index.php?target=estore_execute_doc_action&doc_id={$doc_id}');">{$lng.lbl_resend_reminder_email}</a>
    </li>

    {if $estore_customer_review}
        <li>
            <a class="button" href="index.php?target=estore_reviews_management&action=search&review_data[ids]={$estore_customer_review}">{$lng.lbl_customer_reviews}</a>
        </li>
    {/if}
{/if}