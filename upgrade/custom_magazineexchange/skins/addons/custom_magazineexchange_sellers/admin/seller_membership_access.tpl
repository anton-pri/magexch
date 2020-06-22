{if $current_category.parent_id ne 0}
    {tunnel 
        func='cw\custom_magazineexchange_sellers\mag_category_allowed_by_seller_memberships' 
        via='cw_call'
        param1=$current_category.category_id 
        assign='sellers_access'
    }
    {if $sellers_access ne ''}
        <div class='cat-sellers-memberships-access'>
            <div class="title">Sellers' access to the category</div>
                {foreach from=$sellers_access item=sa}
                    <div class='access-data'>
                        <span class='membership-name'>{$sa.membership_name}:</span>
                        <span class='access-status' title="{$sa.access_note}">{if $sa.allowed}Has access{else}No access{/if}</span>
                    </div>
                {/foreach}
        </div>    
    {/if}
{/if}

