{tunnel func='dashboard_section_statistic'}
<div class="content bg-white border-b">
  <div class="row items-push text-uppercase" style="text-align: center; margin-left: 40px; margin-right: 40px">
    <div class="col-xs-3 col-sm-3" style="background-color:#5EA9F9;">
      <div class="font-w700 text-white animated fadeIn">Product Sales</div>
      <div class="text-muted animated fadeIn"><a href='index.php?target=docs_O&mode=search&data[created]=today'><small><i class="si si-calendar"></i> Today</small></a></div>
      <a class="h2 font-w300 text-white text-primary animated flipInX">{$config.General.currency_symbol} {$product_sales_today|formatprice:' ':null:0}</a>
    </div>
    <div class="col-xs-3 col-sm-3" style="background-color:#F4950D; border-width:20px;">
      <div class="font-w700 text-white  animated fadeIn">Product Sales</div>
      <div class="text-muted animated fadeIn"><a href='index.php?target=docs_O&mode=search&data[created]=this_month'><small><i class="si si-calendar" style="color:white;"></i> This Month</small></a></div>
      <a class="h2 font-w300 text-white text-primary animated flipInX">{$config.General.currency_symbol} {$product_sales_month|formatprice:' ':null:0}</a>
    </div>
    <div class="col-xs-3 col-sm-3" style="background-color:#29A744;">
      <div class="font-w700 text-white  animated fadeIn">Total Earnings</div>
      <div class="text-muted animated fadeIn"><a href='index.php?target=docs_O&mode=search&data[created]=all_time'><small><i class="si si-calendar"></i> All Time</small></a></div>
      <a class="h2 font-w300 text-white text-primary animated flipInX">{$config.General.currency_symbol} {$product_sales_overall|formatprice:' ':null:0}</a>
    </div>
    <div class="col-xs-3 col-sm-3" style="background-color:#E15FED;">
      <div class="font-w700 text-white  animated fadeIn">Average Sale</div>
      <div class="text-muted animated fadeIn"><a href='index.php?target=docs_O&mode=search&data[created]=all_time'><small><i class="si si-calendar"></i> All Time</small></a></div>
      <a class="h2 font-w300 text-white text-primary animated flipInX">{$config.General.currency_symbol} {$product_sales_average|formatprice:' ':null:0}</a>
    </div>
  </div>
</div>
