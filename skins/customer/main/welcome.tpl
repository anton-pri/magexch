<!-- cw@home_slider [ -->
<aside class="home_banner">
  {cms service_code="home_slider"}
</aside>
<!-- cw@home_slider ] -->

<!-- cw@home_offers -->

<!-- cw@featured_categories [ -->
<div class="welcome_area_left featured_categories">
{include file='customer/main/featured_categories.tpl'}
</div>
<!-- cw@featured_categories ] -->

<!-- cw@featured_products [ -->
{include file='customer/products/featured.tpl'}
<!-- cw@featured_products ] -->

{if $addons.EStore and $addons.news}
{include file='addons/news/customer/news.tpl'}
{/if}

{include file='customer/special_sections/hot_deals_week.tpl'}

<!-- cw@hot_deal [ -->
<div class="welcome_area_right hot_deal">
{include file='customer/special_sections/hot_deal.tpl' product=$hot_deals_hot}
</div>
<!-- cw@hot_deal ] -->

<!-- cw@hot_deals_home [ -->
<div class="welcome_area_left hot_deals">
{include file='customer/special_sections/hot_deals_home.tpl'}
</div>
<!-- cw@hot_deals_home ] -->


<!-- cw@clearance [ -->
<div class="welcome_area_right clearance">
{include file='customer/special_sections/clearance_home.tpl'}
</div>
<!-- cw@clearance ] -->

{include file='customer/tags/tags.tpl' tags=$tags}

<div class="clear"></div>

<!-- cw@bottom_products [ -->
<div class="welcome_area">
{include file='customer/special_sections/bottom.tpl'}
</div>
<!-- cw@bottom_products ] -->

<!-- cw@offers [ -->
<div class="offers">
  <div class="small_offer">
    {cms service_code="offer1"}
  </div>
  <div class="small_offer">
    {cms service_code="offer2"}
  </div>
  <div class="small_offer">
    {cms service_code="offer3"}
  </div>
</div>
<!-- cw@offers ] -->
