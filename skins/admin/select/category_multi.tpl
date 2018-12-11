{tunnel func='cw_get_all_categories_for_select' via='cw_call' assign='all_categories'}
    {load_head_resource file="jquery/pqselect/pqselect.min.css" type="css"}
    {load_head_resource file="jquery/pqselect/pqselect.min.js" type="js"}

    <script type="text/javascript">
      $(document).ready(function(){ldelim} 
        $("#{$id}").pqSelect({ldelim}
          multiplePlaceholder: '{$title|escape:javascript}',    
          checkbox: true //adds checkbox to options    
        {rdelim});
      {rdelim});
    </script>

      <select name="{$name}" multiple="multiple" id="{$id}" size=10>
        {foreach from=$all_categories item=c}
{if $c.category_path ne '' && $c.parent_id ne 0}
          <option value="{$c.category_id}" {if $value ne ''}{if in_array($c.category_id, $value)}selected="selected"{/if}{/if}>{$c.category_path} (#{$c.category_id})</option>
{/if}
        {/foreach}
      </select>
