{if $attribute.type eq 'google_product_category_selector'}
    {foreach from=$attribute.default_value key=key item=option}
        {if $option.attribute_value_id eq $attribute.value}{assign var="category_name" value=$option.value}{/if}
    {/foreach}

    <input type= "button" id="gb_choose_category" class="green_but btn btn-green push-5" value="{$lng.lbl_choose_category}"  />
    <input type= "hidden" id="gb_attribute_selected" name="attributes[g:google_product_category]" value="{$attribute.value}" />
    <div id="gb_selected_category"><strong>{$lng.lbl_selected}</strong> : {$category_name} </div>
    <div id="gb_dialog_choose" title="{$attribute.name}">

        <div id="gb_tree">
        </div>


    </div>
    <script type="text/javascript">
        {literal}
        $(document).ready(function() {
            $( "#gb_dialog_choose" ).dialog({
                height :400, width : 600, autoOpen: false, modal: true,
                buttons: {
                    "{/literal}{$lng.lbl_select}{literal}": function() {
                        tree = $("#gb_tree").dynatree("getTree");
                        if(tree.activeNode.data.full_path){
                            $('#gb_attribute_selected').val(tree.activeNode.data.key);
                            $('#gb_selected_category').html('<strong>{/literal}{$lng.lbl_selected}{literal}</strong> : ' + tree.activeNode.data.full_path);
                        }
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            $( "#gb_choose_category" ).click(function() {
                $( "#gb_dialog_choose" ).dialog( "open" );
            });

            $("#gb_tree").dynatree({
                debugLevel: 0,
                autoFocus: false,
                children: [{
                        title: '{/literal}{$attribute.name}{literal}',
                        key: 'root',
                        isFolder: true,
                        isLazy: true
//                      icon: null,
//                      addClass: null,
                    }],
                onLazyRead: function(node){
                        node.appendAjax({
                                         url: "index.php?target=ajax_gb_category_select",
                                        data: {key: node.data.key, title: node.data.title, path: node.data.path, node:node.data.node}
                                        });
                                     }
                });

});
        {/literal}
    </script>

{/if}
