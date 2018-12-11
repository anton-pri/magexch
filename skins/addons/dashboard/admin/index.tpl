<div class="content bg-image overflow-hidden" style="background-image: url('{$ImagesDir}/photo3@2x.jpg');">
  <div class="push-30-t push-15">
    <h1 class="h2 text-white animated zoomIn">Dashboard</h1>
    <h2 class="h5 text-white-op animated zoomIn">{$smarty.now|date_format:"%A, %B %e, %Y"}</h2>
    <h2 class="h5 text-white-op animated zoomIn">Welcome Administrator</h2>
  </div>
</div>
 {include file='addons/dashboard/admin/sections/statistics.tpl'}

 {include file='addons/dashboard/admin/sections/sections.tpl'}
