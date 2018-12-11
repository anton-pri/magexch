{if $products}
    <script type="text/javascript">
        var variants = [];
        var variant_avails = [];
        var modifiers = [];
        var names = [];
        var taxes = [];
        var current_taxes = [];
        var exceptions = [];
        var product_wholesale = [];
        var _product_wholesale = [];
        var product_image = [];
        var product_thumbnail = [];
        var default_price = [];
        var list_price = [];
        var price = [];
        var orig_price = [];
        var avail = [];
        var min_avail = [];
        var store_options = [];

        var exception_msg = "{$lng.txt_exception_warning|strip_tags|escape:javascript}!";
        var exception_msg_html = "{$lng.txt_exception_warning|escape:javascript}!";
        var txt_out_of_stock = "{$lng.txt_out_of_stock|strip_tags|escape:javascript}";
        var lbl_in_stock = "{$lng.lbl_in_stock|strip_tags|escape:javascript}";
        var currency_symbol = "{$config.General.currency_symbol|escape:"javascript"}";
        var alter_currency_symbol = "{$config.General.alter_currency_symbol|escape:"javascript"}";
        var alter_currency_rate = {$config.General.alter_currency_rate|default:"0"};
        var lbl_no_items_available = "{$lng.lbl_no_items_available|escape:javascript}";
        var txt_items_available = "{$lng.txt_items_available|escape:javascript}";
        var mq = {$config.Appearance.max_select_quantity|default:0};
        var dynamic_save_money_enabled = {if $config.Product_Options.dynamic_save_money_enabled eq 'Y'}true{else}false{/if};
        var is_unlimit = false;

        var lbl_item = "{$lng.lbl_item|escape:javascript}";
        var lbl_items = "{$lng.lbl_items|escape:javascript}";
        var lbl_quantity = "{$lng.lbl_quantity|escape:javascript}";
        var lbl_price = "{$lng.lbl_price_per_item|escape:javascript}";
        var txt_note = "{$lng.txt_note|escape:javascript}";
        var lbl_including_tax = "{$lng.lbl_including_tax|escape:javascript}";
        var lbl_buy= "{$lng.lbl_buy|escape:javascript}";
        var lbl_or_more= "{$lng.lbl_or_more|escape:javascript}";
        var lbl_pay_only="{$lng.lbl_pay_only|escape:javascript}";
        var lbl_per_item="{$lng.lbl_per_item|escape:javascript}";
        var lbl_you_save="{$lng.lbl_you_save|escape:javascript}";
        var lbl_or="{$lng.lbl_or|escape:javascript}";
    </script>
{/if}
