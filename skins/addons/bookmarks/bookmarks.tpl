  <aside id="side-overlay">
      <div class="side-header side-content">
        <button class="btn btn-default pull-right" type="button" data-toggle="layout" data-action="side_overlay_close">
          <i class="fa fa-times"></i>
        </button>
        <span class="font-w600 push-10-l">{$lng.lbl_bookmarks}</span>
      </div>
{if $current_area eq 'C' || $customer_id}
    <div class="block-header bg-gray-lighter">
      <button class='add_bm btn btn-default' type="button"><i class="fa fa-bookmark"></i> {$lng.lbl_add_to_bookmark|default:'Add to bookmark'}</button>
    </div>
<div id='bookmarks_container' class="block-content">

    <div id='bm_content' class='bm_content' href='index.php?target=bookmarks&action=get'>
        <p>Please wait...</p>
    </div>
    <form name='current_page' action='index.php?target=bookmarks&action=add'>
        <input type='hidden' name='url' />
        <input type='hidden' name='name' />
    </form>
</div>
{/if}
  </aside>


