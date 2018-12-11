            <label>{$lng.lbl_price}:</label>
            <span id="product_price_{$product.product_id}">
            {include file='common/currency.tpl' value=$product.display_price}
            {include file='common/alter_currency_value.tpl' alter_currency_value=$product.display_price}
            </span>