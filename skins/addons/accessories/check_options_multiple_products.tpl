<script type="text/javascript">

  var initObjectsDelay = 200;

/*
  variants array:
    product ID: array:
      0 - array:
        0 - taxed price
        1 - quantity
        2 - variant thumbnail
        3 - weight
        4 - original price (without taxes)
        5 - productcode
      1 - options object:
        class ID: option ID
      2 - taxes object:
        tax ID: tax amount
      3 - wholesale prices array:
        0 - quantity
        1 - next quantity
        2 - taxed price
        3 - taxes object:
          tax ID: tax amount,
        4 - original price (without taxes)
*/

if (typeof(accessoriesProductVariants) != 'object') var accessoriesProductVariants = [];
if (typeof(accessoriesProductVariantAvails) != 'object') var accessoriesProductVariantAvails = [];

{if $variants && $product_id}
  accessoriesProductVariants[{$product_id}] = [];
  accessoriesProductVariantAvails[{$product_id}] = [];
  {foreach from=$variants item="variant" key="variant_id"}
    accessoriesProductVariantAvails[{$product_id}][{$variant_id}] = parseInt('{$variant.avail}');
    accessoriesProductVariants[{$product_id}][{$variant_id}] = [{strip}
      [{strip}
        {$variant.taxed_price|default:$variant.price|default:$product.taxed_price|default:$product.price},
        {$variant.avail|default:0},
        new Image(),
        '{$variant.weight|default:0}',
        {$variant.price|default:$product.price|default:0},
        '{$variant.productcode|escape:javascript}'
      {/strip}],
      {ldelim}
        {foreach from=$variant.options item="option" key="option_id" name="options"}
          {if $option}
            {$option.class_id|default:0}: {$option.option_id|default:0}
            {if not $smarty.foreach.options.last}, {/if}
          {/if}
        {/foreach}
      {rdelim},
      {ldelim}
        {foreach from=$variant.taxes item="tax" key="taxId" name="taxes"}
          {$taxId}: {$tax|default:0}
          {if not $smarty.foreach.taxes.last}, {/if}
        {/foreach}
      {rdelim},
      [{foreach from=$variant.wholesale item="wholesale" name="wholesales"}
        [{strip}
          {$wholesale.quantity|default:0},
          {if $wholesale.next_quantity gt 2}{math equation="x-1" x=$wholesale.next_quantity}{else}0{/if},
          {$wholesale.taxed_price|default:$product.taxed_price},
          {ldelim}
            {foreach from=$wholesale.taxes item="tax" key="taxId" name="wholesale_taxes"}
              {$taxId}: {$tax|default:0}
              {if not $smarty.foreach.wholesale_taxes.last}, {/if}
            {/foreach}
          {rdelim},
          {$wholesale.price|default:$product.price}
        {/strip}]{if not $smarty.foreach.wholesales.last}, {/if}
      {/foreach}]
    {/strip}];

    {if $variant.is_image}
      accessoriesProductVariants[{$product_id}][{$variant_id}][0][2].src = "{if $v.image_url ne ""}{$v.image_url}{else}{if $full_url}{$catalogs.customer}{else}{$app_web_dir}{/if}/index.php?target=image&id={$variant_id}&type=products_images_var{/if}"; 
      accessoriesProductVariants[{$product_id}][{$variant_id}][0][2].onload = function () {ldelim}
        accessoriesInitPageOptionsOnLastObjectLoaded(initObjectsDelay);
      {rdelim}
    {/if}
  {/foreach}
{/if}

/*
  modifiers array: array
    product ID: array:
      class ID: object:
        option ID: array:
          0 - modifier value
          1 - modifier type
          2 - taxes object:
            tax ID: tax value

*/
if (typeof(accessoriesProductModifiers) != 'object') var accessoriesProductModifiers = [];

/*
  names array: array:
    product ID: array:
      class ID: object: 
        class_name: class name
        options: array:
          option ID: option name
*/
if (typeof(accessoriesProductOptionsNames) != 'object') var accessoriesProductOptionsNames = [];

{if $product_options && $product_id}
  accessoriesProductOptionsNames[{$product_id}] = [];
  {foreach from=$product_options item="options_class"}
    accessoriesProductOptionsNames[{$product_id}][{$options_class.class_id}] = {ldelim}class_name: '', options: []{rdelim};
    accessoriesProductOptionsNames[{$product_id}][{$options_class.class_id}].class_name = "{$options_class.class_orig|default:$options_class.class|escape:javascript}";
    {foreach from=$options_class.options item="option"}
      accessoriesProductOptionsNames[{$product_id}][{$options_class.class_id}].options[{$option.option_id}] = "{$option.name|escape:javascript}";
    {/foreach}
    {if $options_class.is_modifier eq "Y"}
      accessoriesProductModifiers[{$product_id}] = [];
      accessoriesProductModifiers[{$product_id}][{$options_class.class_id}] = {ldelim}{strip}
        {foreach from=$options_class.options item="option" name="options"}
          {$option.option_id}: [{strip}
            {$option.price_modifier|default:"0.00"}, 
            '{$option.modifier_type|default:"$"}',
            {ldelim}
              {foreach from=$option.taxes item="tax" key="taxId" name="option_taxes"}
                {$taxId}: {$tax|default:"0"}
                {if not $smarty.foreach.option_taxes.last}, {/if}
              {/foreach}
            {rdelim}
          {/strip}]{if not $smarty.foreach.options.last}, {/if}
        {/foreach}
      {/strip}{rdelim};
    {/if}
  {/foreach}
{/if}

/*
  taxes array:
    product ID: array:
      tax ID: array:
        0 - calculated tax value for default product price
        1 - tax name
        2 - tax type ($ or %)
        3 - tax value
*/
if (typeof(accessoriesProductTaxes) != 'object') var accessoriesProductTaxes = [];

{if $product.taxes && $product_id}
  accessoriesProductTaxes[{$product_id}] = [];
  {foreach from=$product.taxes key="tax_name" item="tax"}
    {if $tax.display_including_tax eq "Y" && ($tax.display_info eq "A" || $tax.display_info eq "V")}
      accessoriesProductTaxes[{$product_id}][{$tax.tax_id}] = [{$tax.tax_value|default:0}, "{$tax.tax_display_name}", "{$tax.rate_type}", "{$tax.rate_value}"];
    {/if}
  {/foreach}
{/if}

/*
  exceptions array:
    product ID: array:
      array:
        class ID: option ID
*/
if (typeof(accessoriesProductOptionsExceptions) != 'object') var accessoriesProductOptionsExceptions = [];

{if $product_options_ex && $product_id}
  accessoriesProductOptionsExceptions[{$product_id}] = [];
  {foreach from=$product_options_ex item="exception" key="k"}
    accessoriesProductOptionsExceptions[{$product_id}][{$k}] = [];
    {foreach from=$exception item="option"}
      accessoriesProductOptionsExceptions[{$product_id}][{$k}][{$option.class_id}] = {$option.option_id};
    {/foreach} 
  {/foreach} 
{/if}

/*
  product wholesale array:
    product ID: array:
      0 - quantity
      1 - next quantity
      2 - taxed price
      3 - taxes: array:
        tax ID: tax value
      4 - original price (without taxes)
*/
if (typeof(accessoriesProductWholesale) != 'object') var accessoriesProductWholesale = [];
if (typeof(accessoriesAuxiliaryProductWholesale) != 'object') var accessoriesAuxiliaryProductWholesale = [];

{if $product_wholesale && $product_id}
  accessoriesAuxiliaryProductWholesale[{$product_id}] = [];
  {foreach from=$product_wholesale item="wholesale" key="k"}
    accessoriesAuxiliaryProductWholesale[{$product_id}][{$k}] = [{$wholesale.quantity|default:0}, {$wholesale.next_quantity|default:0}, {$wholesale.taxed_price|default:$product.taxed_price}, [], {$wholesale.price|default:$product.price}];
    {if $wholesale.taxes}
      {foreach from=$wholesale.taxes item="tax" key="kt"}
        accessoriesAuxiliaryProductWholesale[{$product_id}][{$k}][3][{$kt}] = {$tax|default:0};
      {/foreach}
    {/if}
  {/foreach}
{/if}

if (typeof(accessoriesInitOptions) != 'object') var accessoriesInitOptions = [];
accessoriesInitOptions[accessoriesInitOptions.length] = {ldelim}productListName: '{$product_list_name}', productId: {$product_id}{rdelim};

/*
  Product settings
*/
if (typeof(accessoriesProductSettings) != 'object') var accessoriesProductSettings = [];

{if $product && $product_id && $product_list_name ne ""}
  if (typeof(accessoriesProductSettings[{$product_id}]) != 'object') accessoriesProductSettings[{$product_id}] = {ldelim}{rdelim};
  accessoriesProductSettings[{$product_id}].{$product_list_name} = {ldelim}
    defaultPrice: {$product.taxed_price|default:"0.00"},
    listPrice: {$product.list_price|default:"0.00"},
    origPrice: {$product.price|default:$product.taxed_price|default:"0.00"},
    avail: {$product.avail|default:"0"},
    minAmount: {$product.min_amount|default:"1"},
    productSmallImage: document.getElementById('{$product_list_name}_product_image_small_{$product_id}'),
    weightContainer: document.getElementById('{$product_list_name}_product_weight_{$product_id}'),
    weightLineContainer: document.getElementById('{$product_list_name}_product_weight_line_{$product_id}'),
    priceContainer: document.getElementById('{$product_list_name}_product_price_{$product_id}'),
    altPriceContainer: document.getElementById('{$product_list_name}_product_alt_price_{$product_id}'),
    skuContainer: document.getElementById('{$product_list_name}_product_sku_{$product_id}'),
    quantityInStockContainer: document.getElementById('{$product_list_name}_product_quantity_in_stock_{$product_id}'),
    wholesaleContainer: document.getElementById('{$product_list_name}_product_wholesale_prices_{$product_id}'),
    optionsExceptionContainer: document.getElementById('{$product_list_name}_product_options_exception_message_{$product_id}'),
    quantitySelector: document.getElementById('{$product_list_name}_product_quantity_selector_{$product_id}')
  {rdelim};
  if (accessoriesProductSettings[{$product_id}].{$product_list_name}.productSmallImage) {ldelim}
    accessoriesProductSettings[{$product_id}].{$product_list_name}.productSmallImage.onload = function() {ldelim}
        accessoriesInitPageOptionsOnLastObjectLoaded(initObjectsDelay);
    {rdelim}
  {rdelim}
{/if}


</script>

{include_once_src file="main/include_js.tpl" src="addons/accessories/func.js"}
