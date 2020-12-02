{literal}
    <script type="text/javascript">
    //<![CDATA[
        $(document).ready(function() {
            $('body').css('overflow-y', 'hidden').css('background-color', '#FCCB05');
            $("div.content").css('padding', 0).css('position', 'fixed').addClass('transp');
            $("div.block").css('margin-bottom', 0).addClass('transp');
            $('#page-container').addClass('transp');
        });
    //]]>
    </script>
{/literal}


<div id="category_add" class="transp" style="
    margin-bottom: 10px;
    width: 470px;
    height: 196px;
">
    <div class="add_note">
        {$lng.lbl_clone_category_note}
       
    </div>
    <div class="master_category">
        {$lng.lbl_master_category}:<span> {$master_category.category}</span>
    </div>

    <div class="add_field">
        <input type="text" id="new_name" value="" title="" />
    </div>
    <input type="hidden" id="master_category_id" value="{$master_category.category_id}" />
</div>


<div class="category_add_footer transp">
    <div class="buttons">
        {include 
            file='buttons/button.tpl' 
            button_title=$lng.lbl_category_add_save|default:"Save"
            class="btn-green btn" 
            href="javascript: window.parent.add_new_category($('#new_name').val(), $('#master_category_id').val());"
        }
    </div>
</div>