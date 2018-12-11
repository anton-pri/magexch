<header id="header-navbar" class="content-mini content-mini-full cl_{$main}">
<ul class="nav-header pull-left">
  <li class="menu-button-mobile">
    <button class="btn btn-default" data-toggle="layout" data-action="sidebar_toggle" type="button">
      <i class="fa fa-navicon"></i>
    </button>
  </li>
  <li class="menu-button-desktop">
    <button class="btn btn-default" data-toggle="layout" data-action="sidebar_mini_toggle" type="button">
      <i class="fa fa-ellipsis-v"></i>
    </button>
  </li>

  <li class="visible-xs">
    <button class="btn btn-default" data-toggle="class-toggle" data-target=".js-header-search" data-class="header-search-xs-visible" type="button">
      <i class="fa fa-search"></i>
    </button>
  </li>

    {*<div class="logo auth"><a href="{$catalogs.customer}/index.php" targrt="_blank">{$lng.lbl_open_storefront}</a></div>*}
  <li class="js-header-search header-search">
    {include file="addons/dashboard/admin/sections/search.tpl"}
  </li>

</ul>

<ul class="nav-header pull-right">
  <li>
    {include file='common/top-filters.tpl'}
  </li>
  <li>
    <div class="btn-group">
      <button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false">
        <img src="{$ImagesDir}/avatar.png" alt="Avatar">
        <span class="caret"></span>
      </button>
      {include file='elements/authbox_top_admin.tpl'}
    </div>
  </li>
  {include file='admin/bookmarks_menu_button.tpl'} 
  <li id="fa-eye-show-all" style="display: none;">
    <button class="btn btn-default" type="button" id="fa-eye-show-all-button">
      <i class="fa fa-eye"></i>
    </button>
  </li>
</ul>
</header>

