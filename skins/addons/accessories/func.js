var currentTaxes     = [];
var productPrice     = 0.00;
var productOrigPrice = 0.00;
var productAvail     = 0;
var productMinAmount = 1;
var initDelayId      = null;

/*
  Delayed page objects initialization
*/
function accessoriesInitPageOptionsOnLastObjectLoaded(delay) {
  delay     = parseInt(delay);
  if (isNaN(delay) || delay == 0) return false;
  if (initDelayId) clearTimeout(initDelayId);
  initDelayId = setTimeout(
    function() {
      if (accessoriesInitOptions.length > 0) {
        var productId, productListName;
        for (var i in accessoriesInitOptions) {
          productId = parseInt(accessoriesInitOptions[i].productId);
          if (isNaN(productId)) continue;
          productListName = accessoriesInitOptions[i].productListName;
          accessoriesCheckOptions(productListName, productId);
        }
      }
    },
    delay
  );
}

/*
  Rebuild page if some options is changed
*/
function accessoriesCheckOptions(productListName, productId) {
  var localTaxes = [];
  var isRebuildWholesale = false;
  var variantId = false;

  productId = parseInt(productId);
  if (isNaN(productId)) return false;
  if (productListName == '') return false;
  if (!accessoriesProductSettings[productId]) return false;
  if (!accessoriesProductSettings[productId][productListName]) return false;

  accessoriesInitProductSettings(productListName, productId);

  if (accessoriesProductTaxes[productId]) {
    for (var taxId in accessoriesProductTaxes[productId]) {
      localTaxes[taxId] = accessoriesProductTaxes[productId][taxId][0];
    }
  }
  productPrice     = parseFloat(accessoriesProductSettings[productId][productListName].defaultPrice);
  productAvail     = parseInt(accessoriesProductSettings[productId][productListName].avail);
  productMinAmount = parseInt(accessoriesProductSettings[productId][productListName].minAmount);

  /* Find variant */
  if (accessoriesProductVariants[productId]) {
    for (var Id in accessoriesProductVariants[productId]) {
      if (accessoriesProductVariants[productId][Id][1].length == 0) continue;
      variantId = parseInt(Id);
      for (var classId in accessoriesProductVariants[productId][Id][1]) {
        optionId = parseInt(accessoriesProductVariants[productId][Id][1][classId]);
        if ((accessoriesGetProductOptionId(productListName, productId, classId) != optionId) || isNaN(optionId)) {
          variantId = false;
          break;
        }
      }
      if (variantId) break;
    }
  }

  /* If variant found ... */
  if (variantId) {
    productPrice     = parseFloat(accessoriesProductVariants[productId][variantId][0][0]);
    productOrigPrice = parseFloat(accessoriesProductVariants[productId][variantId][0][4]);
    productAvail     = parseInt(accessoriesProductVariantAvails[productId][variantId]);

    /* Get variant wholesale prices */
    if (accessoriesProductVariants[productId][variantId][3]) {
      accessoriesProductWholesale = [];
      for (var i in accessoriesProductVariants[productId][variantId][3]) {
        var tmp = accessoriesReCalcProductPrice(
          productListName,
          productId,                                                              // product ID
          accessoriesProductVariants[productId][variantId][3][i][2],              // product price
          cloneObject(accessoriesProductVariants[productId][variantId][3][i][3]), // taxes
          accessoriesProductVariants[productId][variantId][3][i][4]               // original product price
        );
        accessoriesProductWholesale[i] = [
          accessoriesProductVariants[productId][variantId][3][i][0], // wholesale quantity
          accessoriesProductVariants[productId][variantId][3][i][1], // wholesale next quantity
          tmp[0],                                                    // modified product price
          []
        ];

        /* Get variant wholesale tax values */
        if (tmp[1]) {
          for (var taxId in tmp[1]) {
            accessoriesProductWholesale[i][3][taxId] = tmp[1][taxId];
          }
        }
      }
      isRebuildWholesale = true;
    }

    /* Get variant taxes */
    for (var taxId in localTaxes) {
      if (accessoriesProductVariants[productId][variantId][2][taxId]) {
        localTaxes[taxId] = parseFloat(accessoriesProductVariants[productId][variantId][2][taxId]);
      }
    }

    /* Change product thumbnail */
    if (accessoriesProductSettings[productId][productListName].productSmallImage) {
      var productImageSource = accessoriesProductSettings[productId][productListName].productSmallImage.src;
      var productImageWidth  = parseInt(accessoriesProductSettings[productId][productListName].productSmallImage.width);
      var productImageHeight = parseInt(accessoriesProductSettings[productId][productListName].productSmallImage.height);
      var variantImageSource = accessoriesProductVariants[productId][variantId][0][2].src;
      var variantImageWidth  = parseInt(accessoriesProductVariants[productId][variantId][0][2].width);
      var variantImageHeight = parseInt(accessoriesProductVariants[productId][variantId][0][2].height);
      if (productImageSource.length > 0 && productImageWidth > 0 && productImageHeight > 0 && variantImageSource.length > 0 && variantImageWidth > 0 && variantImageHeight > 0) {
        var productImageFormFactor = productImageWidth / productImageHeight;
        var variantImageFormFactor = variantImageWidth / variantImageHeight;
        var scaleFactor = 1;
        if (productImageFormFactor >= variantImageFormFactor) {
          scaleFactor = productImageHeight / variantImageHeight;
        }
        else {
          scaleFactor = productImageWidth / variantImageWidth;
        }
        variantImageWidth  = Math.floor(scaleFactor * variantImageWidth);
        variantImageHeight = Math.floor(scaleFactor * variantImageHeight);
        accessoriesProductSettings[productId][productListName].productSmallImage.src = variantImageSource;
        if (productImageFormFactor > variantImageFormFactor) {
          accessoriesProductSettings[productId][productListName].productSmallImage.height = Math.max(variantImageHeight, productImageHeight);
          accessoriesProductSettings[productId][productListName].productSmallImage.width  = Math.min(variantImageWidth, productImageWidth);
        }
        else {
          accessoriesProductSettings[productId][productListName].productSmallImage.height = Math.min(variantImageHeight, productImageHeight);
          accessoriesProductSettings[productId][productListName].productSmallImage.width  = Math.max(variantImageWidth, productImageWidth);
        }
      }
    }

    /* Change product weight */
    if (accessoriesProductSettings[productId][productListName].weightContainer) {
      accessoriesProductSettings[productId][productListName].weightContainer.innerHTML = price_format(accessoriesProductVariants[productId][variantId][0][3]);
      if (accessoriesProductSettings[productId][productListName].weightLineContainer) {
        accessoriesProductSettings[productId][productListName].weightLineContainer.style.display = parseFloat(accessoriesProductVariants[productId][variantId][0][3]) ? '' : 'none';
      }
    }

    /* Change product code */
    if (accessoriesProductSettings[productId][productListName].skuContainer) {
      accessoriesProductSettings[productId][productListName].skuContainer.innerHTML = accessoriesProductVariants[productId][variantId][0][5];
    }
  }

  /* Find modifiers */
  var tmp = accessoriesReCalcProductPrice(productListName, productId, productPrice, localTaxes, productOrigPrice);
  productPrice = tmp[0];
  localTaxes = tmp[1];
  if (!variantId && accessoriesAuxiliaryProductWholesale[productId]) {
    accessoriesProductWholesale = [];
    for (var i in accessoriesAuxiliaryProductWholesale[productId]) {
      tmp = accessoriesReCalcProductPrice(
        productListName,
        productId,
        accessoriesAuxiliaryProductWholesale[productId][i][2],
        accessoriesAuxiliaryProductWholesale[productId][i][3].slice(0),
        accessoriesAuxiliaryProductWholesale[productId][i][4]
      );
      accessoriesProductWholesale[i] = [
        parseInt(accessoriesAuxiliaryProductWholesale[productId][i][0]), // quantity
        parseInt(accessoriesAuxiliaryProductWholesale[productId][i][1]), // next quantity
        parseFloat(tmp[0]),                                              // modified product price
        tmp[1]                                                           // tax values
      ];
    }
    isRebuildWholesale = true;
  }

  /* Update taxes */
  for (var taxId in localTaxes) {
    var productTaxContainer = document.getElementById(productListName + '_product_tax_' + productId + '_' + taxId);
    if (productTaxContainer) {
      productTaxContainer.innerHTML = currencySymbol + price_format(parseFloat(localTaxes[taxId]) < 0 ? 0.00 : localTaxes[taxId]);
    }
    currentTaxes[taxId] = localTaxes[taxId];
  }

  if (isRebuildWholesale) accessoriesRebuildWholesale(productListName, productId);

  /* Update price */
  if (accessoriesProductSettings[productId][productListName].priceContainer) {
    accessoriesProductSettings[productId][productListName].priceContainer.innerHTML = currencySymbol + price_format(productPrice < 0 ? 0.00 : productPrice);
  }

  /* Update alternative price */
  if (alterCurrencyRate > 0 && alterCurrencySymbol != '' && accessoriesProductSettings[productId][productListName].altPriceContainer) {
    var altProductPrice = productPrice * alterCurrencyRate;
    accessoriesProductSettings[productId][productListName].altPriceContainer.innerHTML = '(' + alterCurrencySymbol + ' ' + price_format(altProductPrice < 0 ? 0.00 : altProductPrice) + ')';
  }

  /* Update product quantity */
  if (accessoriesProductSettings[productId][productListName].quantityInStockContainer) {
    if (productAvail > 0) {
      accessoriesProductSettings[productId][productListName].quantityInStockContainer.innerHTML = substitute(txtItemsAvailable, 'items', productAvail)
    } else {
      accessoriesProductSettings[productId][productListName].quantityInStockContainer.innerHTML = lblNoItemsAvailable;
    }
  }

  maxSelectorQuantity = parseInt(maxSelectorQuantity);
  var maxProductAvail = productMinAmount + 20;
  if (maxSelectorQuantity > 0) {
    maxProductAvail = Math.min((maxSelectorQuantity + productMinAmount - 1), productAvail);
  }

  var selectedProductAvail = productMinAmount;
  /* Update product quantity selector */
  if (accessoriesProductSettings[productId][productListName].quantitySelector && accessoriesProductSettings[productId][productListName].quantitySelector.tagName.toUpperCase() == 'SELECT') {
    var quantitySelector = accessoriesProductSettings[productId][productListName].quantitySelector;
    var firstOptionValue = false;
    if (quantitySelector.options[0]) firstOptionValue = quantitySelector.options[0].value;
    if (firstOptionValue == productMinAmount) {
      /* New and old first value in quantities list are equal */
      if ((maxProductAvail - productMinAmount + 1) != quantitySelector.options.length) {
        if (quantitySelector.options.length > (maxProductAvail - productMinAmount + 1)) {
          var counter = quantitySelector.options.length;
          for (var x = (maxProductAvail - productMinAmount + 1); x < counter; x++) {
            quantitySelector.options[(quantitySelector.options.length - 1)] = null;
          }
        }
        else {
          var counter = quantitySelector.options.length;
          var quantity = 1;
          for (var x = counter; x <= (maxProductAvail - productMinAmount + 1); x++) {
            quantity = productMinAmount + x;
            quantitySelector.options[counter] = new Option(quantity, quantity);
          }
        }
      }
    }
    else {
      /* New and old first value in quantities list are different */
      while (quantitySelector.options.length > 0) {
        quantitySelector.options[0] = null;
      }
      var counter = 0;
      for (var x = productMinAmount; x <= (maxProductAvail - productMinAmount + 1); x++)
        quantitySelector.options[counter++] = new Option(x, x);
    }
    if (quantitySelector.options.length == 0) quantitySelector.options[0] = new Option(txt_out_of_stock, 0);
    selectedProductAvail = parseInt(quantitySelector.options[quantitySelector.selectedIndex].value);
  }

  /* Check product availability */
  if ((useAlertMessages == 'Y') && (productMinAmount > productAvail)) alert(txtOutOfStock);

  /* Check exceptions */
  var exceptionFlag = accessoriesCheckExceptions(productListName, productId);
  if ((useAlertMessages == 'Y') && !exceptionFlag) alert(exceptionMsg);

  if (accessoriesProductSettings[productId][productListName].optionsExceptionContainer) {
    accessoriesProductSettings[productId][productListName].optionsExceptionContainer.innerHTML = exceptionFlag ? '' : exceptionMsgHtml;
  }

  return true;
}

/*
  Calculate product price with price modificators 
*/
function accessoriesReCalcProductPrice(productListName, productId, price, taxes, origPrice) {
  price = parseFloat(price);
  origPrice = parseFloat(origPrice);
  productId = parseInt(productId);
  if (isNaN(price)) return false;
  if (isNaN(origPrice)) return false;
  if (isNaN(productId)) return false;
  if (productListName == '') return false;
  var returnPrice = parseFloat(round(price, 2));

  /* List modificators */
  if (accessoriesProductModifiers[productId]) {
    for (var classId in accessoriesProductModifiers[productId]) {
      classId = parseInt(classId);
      if (isNaN(classId)) continue;
      var optionId = accessoriesGetProductOptionId(productListName, productId, classId);
      if (!optionId || !accessoriesProductModifiers[productId][classId][optionId]) continue;

      /* Get selected option */
      var modifierData = accessoriesProductModifiers[productId][classId][optionId];
      returnPrice += parseFloat(modifierData[1] == '$' ? modifierData[0] : (price * (modifierData[0] / 100)));

      /* Get tax extra charge */
      if (taxes) {
        for (var taxId in taxes) {
          if (modifierData[2][taxId]) {
            taxes[taxId] += parseFloat(modifierData[1] == '$' ? modifierData[2][taxId] : (origPrice * modifierData[2][taxId] / 100));
          }
        }
      }
    }
  }

  return [returnPrice, taxes];
}

/*
  Check product options exceptions
*/
function accessoriesCheckExceptions(productListName, productId) {

  productId = parseInt(productId);
  if (isNaN(productId)) return false;
  if (productListName == '') return false;
  if (!accessoriesProductOptionsExceptions[productId]) return true;

  /* List exceptions */
  for (var i in accessoriesProductOptionsExceptions[productId]) {
    i = parseInt(i);
    if (isNaN(i)) continue;
    var isExceptionFound = true;
    for (var classId in accessoriesProductOptionsExceptions[productId][i]) {
      var optionId = accessoriesGetProductOptionId(productListName, productId, classId);
      if (!optionId) return true;
      if (optionId != accessoriesProductOptionsExceptions[productId][i][classId]) {
        isExceptionFound = false;
        break;
      }
    }
    if (isExceptionFound) return false;
  }

  return true;
}

/*
  Rebuild wholesale tables
*/
function accessoriesRebuildWholesale(productListName, productId) {
	
	if (typeof (change_amount) == "function") {
		change_amount(productId, $('#product_recommended_product_quantity_selector_' + productId).val());
	}

  productId = parseInt(productId);
  if (isNaN(productId)) return false;
  if (productListName == '') return false;
  if (!accessoriesProductSettings[productId]) return false;
  if (!accessoriesProductSettings[productId][productListName]) return false;

  accessoriesInitProductSettings(productListName, productId);
  if (!accessoriesProductSettings[productId][productListName].wholesaleContainer) return false;

  var wholesaleContainer = accessoriesProductSettings[productId][productListName].wholesaleContainer;

  /* Clear wholesale span object if product wholesale prices service array is empty */
  if (!accessoriesProductWholesale || accessoriesProductWholesale.length == 0) {
    wholesaleContainer.innerHTML = '';
    return false;
  }

  /* Display headline */
  var str = '';
  var i = 0;
  var k = 0;
  for (var x in accessoriesProductWholesale) {
    if (accessoriesProductWholesale[x][0] == 0) continue;
    if (i == 0) str += '<table cellpadding="2" cellspacing="0" width="100%">';
    str += '<tr';
    if (k == 0) {
      str += ' class="TableSubHead"';
      k = 1;
    }
    str += '>';
    str += '<td width="33%">' + lblBuy + ' <font class="WholesalePrice">' + accessoriesProductWholesale[x][0] + '</font> ' + lblOrMore + '</td>';
    str += '<td width="33%">' + lblPayOnly + ' <font class="WholesalePrice">' + price_format(accessoriesProductWholesale[x][2] < 0 ? 0.00 : accessoriesProductWholesale[x][2])+'</font> ' + lblPerItem + '</td>';
    var savedAmount = productPrice - parseFloat(accessoriesProductWholesale[x][2]);
    var wholesaleDiscount = (productPrice / parseFloat(accessoriesProductWholesale[x][2])) * 100 - 100;
    str += '<td width="33%">' + lblYouSave + ' <font class="WholesalePrice">' + price_format(savedAmount) + ' ' + lblOr + ' ' + price_format(wholesaleDiscount) + '%</font></td>';
    str += '</tr>';
    i++;
  }
  if (i == 0) return false;
  str += '</table>';
  wholesaleContainer.innerHTML = str;
  return true;
}

/*
  Get product option value
*/
function accessoriesGetProductOptionId(productListName, productId, classId) {
  classId = parseInt(classId);
  productId = parseInt(productId);
  if (isNaN(classId)) return false;
  if (isNaN(productId)) return false;
  if (productListName == '') return false;
  var optionsSelector = document.getElementById(productListName + '_' + productId + '_' + classId);
  if (!optionsSelector || optionsSelector.tagName.toUpperCase() != 'SELECT') return false;
  var optionId = parseInt(optionsSelector.options[optionsSelector.selectedIndex].value);
  if (isNaN(optionId)) return false;
  return optionId;
}

function accessoriesInitProductSettings(productListName, productId) {
  productId = parseInt(productId);
  if (isNaN(productId)) return false;
  if (!accessoriesProductSettings[productId]) return false;
  if (productListName == '') return false;
  if (!accessoriesProductSettings[productId][productListName]) return false;
  
  if (!accessoriesProductSettings[productId][productListName].productSmallImage) {
    accessoriesProductSettings[productId][productListName].productSmallImage = document.getElementById(productListName + '_product_image_small_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].weightContainer) {
    accessoriesProductSettings[productId][productListName].weightContainer = document.getElementById(productListName + '_product_weight_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].weightLineContainer) {
    accessoriesProductSettings[productId][productListName].weightLineContainer = document.getElementById(productListName + '_product_weight_line_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].priceContainer) {
    accessoriesProductSettings[productId][productListName].priceContainer = document.getElementById(productListName + '_product_price_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].altPriceContainer) {
    accessoriesProductSettings[productId][productListName].altPriceContainer = document.getElementById(productListName + '_product_alt_price_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].skuContainer) {
    accessoriesProductSettings[productId][productListName].skuContainer = document.getElementById(productListName + '_product_sku_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].quantityInStockContainer) {
    accessoriesProductSettings[productId][productListName].quantityInStockContainer = document.getElementById(productListName + '_product_quantity_in_stock_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].wholesaleContainer) {
    accessoriesProductSettings[productId][productListName].wholesaleContainer = document.getElementById(productListName + '_product_wholesale_prices_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].optionsExceptionContainer) {
    accessoriesProductSettings[productId][productListName].optionsExceptionContainer = document.getElementById(productListName + '_product_options_exception_message_' + productId);
  }
  if (!accessoriesProductSettings[productId][productListName].quantitySelector) {
    accessoriesProductSettings[productId][productListName].quantitySelector = document.getElementById(productListName + '_product_quantity_selector_' + productId);
  }

}

function accessoriesChangeFormActionField(form, action) {
  var formObj = form;
  if (typeof(formObj) != 'object') {
    formObj = document.forms[form];
  }
  if (!formObj) return false;
  var fieldObj = formObj.elements.action;
  if (!fieldObj) return false;
  fieldObj.value = action;
}


/* Align all product divs by height and width */
function align() {

    if ($(this).data('aligned')==1) { return true;}

    var tab_id = new String($(this).attr('id'));
    var content_id = tab_id.replace('tab_','contents_');
    var content = $('#'+content_id);
    var max_height = 0;
    $(content).find('.acc_product_main').each(function() {
        if ($(this).height() > max_height) max_height = $(this).height();
    });
    $(content).find('.acc_product_main').height(max_height);
    $(this).data('aligned',1);

    /*
    var width = $(content).find('.acc_row').width();
    $(content).find('.acc_product').width(Math.floor((width-columns*2*11)/columns));
    */
}

$(document).ready(function() {
    $('#tab_accessories').click(align);
    $('#tab_accessories_rec').click(align);
});