
<div class="subcategories">
<div class="categories_list">

<div class="sub_box">
  <div class="nav_categories">
    <a class="navLeft navDisabled" href="javascript: void(0);">{$lng.lbl_previous}</a>
    <a class="navRight" href="">{$lng.lbl_next}</a>
    <div class="clear"></div>
  </div>
  <div class="instr">{$lng.lbl_subcategories_instruction}</div>

  {foreach from=$subcategories item=subcat}
    <div class="magazine-category" id="mc_{$subcat.category_id}" onmouseover="document.getElementById('mc_{$subcat.category_id}').style.zIndex= 9000+0; " style="z-index: 8000;" onmouseout="this.style.zIndex=8000+0;" onmouseleave="this.style.zIndex=8000+0;" onmouseenter="document.getElementById('mc_{$subcat.category_id}').style.zIndex= 9000+0; ">
      <div class="magazine-category1" onclick="self.location='{pages_url var="index" cat=$subcat.category_id}';">
        <div class="magazine-category-table">
          <div class="magazine-category2">
          <div style="#position: absolute; #top: 50%;">
          <div style="#position: relative; #top: -50%; #left: -50%;">
            {include file='common/product_image.tpl' image=$subcat.image html_width=149 html_height=45}
          </div>
          </div>
          </div>
        </div>
        <div class="magazine-title">{$subcat.category}</div>
        <div class="magazine-descr">{$subcat.description|strip_tags}</div>
        <div class="magazine-rollover incomplete"><img src="{$AltImagesDir}/Logo_Rollover_Euro_Cars.png" alt=""></div>
      </div>
    </div>
  {/foreach}
  <div class="clear"></div>
  <div class="nav_categories bottom">
    <a class="navLeft navDisabled" href="javascript: void(0);">{$lng.lbl_previous}</a>
    <a class="navRight" href="">{$lng.lbl_next}</a>
    <div class="clear"></div>
  </div>
</div>

</div>
</div>
