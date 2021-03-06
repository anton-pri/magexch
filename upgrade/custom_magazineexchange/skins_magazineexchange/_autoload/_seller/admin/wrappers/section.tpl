<section class="section{if $style} {$style}{/if}" {if $section_id}id="{$section_id}"{/if}>
    <div class="content bg-yellow">
      <div class="row items-push">
        <div class="col-sm-7">
          <h1 class="h2 text-red animated zoomIn"{if $title_id} id="{$title_id}"{/if}>
             {if $hidden}{include file='main/visiblebox_link.tpl' mark=$title|id title=''}{/if}
            {if $current_target ne 'products' and $current_target ne 'digital_products'}  {$title}{/if}
{if $current_target eq 'products'}Print Magazines For Sale{/if}
{if $current_target eq 'digital_products'}Digital Magazines For Sale{/if}

              <!-- {if $local_config}<a href='index.php?target=settings&cat={$local_config}'><img src="{$ImagesDir}/admin/class_icon.png" alt="Settings" /></a>{/if} -->
              <small>&nbsp;{include file='admin/products/category/location.tpl'}</small>
          </h1>
          
        </div>
        <div class="col-sm-5 text-right hidden-xs">
          {if $main ne "main"}{include file='admin/main/location.tpl'}{/if}
        </div>
      </div>
    </div>
    <div class="content"{if $hidden} style="display: none;" id="{$title|id}"{/if}>{$content}</div>
    {if $alt_bottom and $bottom_button_href}
      <div class="bottom">{include file='buttons/button.tpl' button_title=$button_title href=$bottom_button_href style='rma'}</div>
    {/if}
</section>
