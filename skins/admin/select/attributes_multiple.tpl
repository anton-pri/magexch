    <div id='select_attributes' class="form-inline">
    <div class="form-group">
       <select id='new_attribute_cond' class="form-control">
       {tunnel func='cw_attributes_get_all_for_products' via='cw_call' param1=1 assign='selector_attr'} 
       {foreach from=$selector_attr item='attributes'}
         {if $attributes.type ne 'rating'}  
         <option value="{$attributes.attribute_id}">{$attributes.name}</option>
         {/if}
       {/foreach}
       </select>
    </div>
    <div class="form-group">
       <a onclick="javascript: ajaxGet('index.php?target=select_attr&posted_name={$name}&no_extra_cmp={$no_extra_cmp}&attribute_id='+$('#new_attribute_cond').val());" href="javascript: void(0);"> 
         <img src="{$ImagesDir}/admin/plus.png">
       </a>
    </div>
       <script type="text/javascript">
           $i = 0;
           {foreach from=$value item=_elem key=_key}
             {foreach from=$_elem.value item=_elem_value}
               setTimeout("ajaxGet('index.php?target=select_attr&posted_name={$name}&attribute_id={$_elem.attribute_id}&value={$_elem_value}&operation={$_elem.operation}&cd_id={$_key}&no_extra_cmp={$no_extra_cmp}')",100*$i);$i++;
             {/foreach}
           {/foreach}
       </script>

     </div>
