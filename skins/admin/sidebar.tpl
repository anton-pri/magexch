<nav id="sidebar">
  <div id="sidebar-scroll" data-toggle="slimscroll">
    <div class="sidebar-scroll">
      <div class="sidebar-content">
        <div class="side-header side-content bg-white-op">
           <div class="logo"><a href="{$catalogs.admin}/index.php"><img src="{$ImagesDir}/admin/logo.png"></a></div>
        	<div class="logo-short"><a href="{$catalogs.admin}/index.php"><img src="{$ImagesDir}/admin/logo-short.png"></a></div>

        </div>

        <div class="side-content">
          <ul class="nav-main">
            <li>
            </li>
              {include file="menu/items.tpl" items=$menu}
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>



