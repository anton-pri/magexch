{capture name=testimonials}
<div class="estore_container">
    {if $testimonials}
        {foreach from=$testimonials item=t}
            <div class="estore_container_item">
                <b>{$t.email}</b><br><br>
                {$t.message}
            </div>
        {/foreach}
    {else}
        <div class="estore_container_item">
            {$lng.txt_no_testimonials}
        </div>
    {/if}
</div>
{/capture}
{include file='common/section.tpl' is_dialog=1 content=$smarty.capture.testimonials title=$lng.lbl_testimonials style='testimonials '}