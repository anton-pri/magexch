<div class="pull-right">
  {$lng.lbl_version} 1.0 {include file='admin/elements/bottom_links.tpl'}
</div>
<div class="pull-left">
<!--  &copy; {$lng.lbl_copyright} {*$config.Company.start_year|date_format:"%Y"}{if $config.Company.start_year lt $config.Company.end_year}-{$smarty.now|date_format:"%Y"}{/if*} {$config.Company.company_name}
  &nbsp;2018 <a href="" class="font-w600">CartWorks, eCommerce software</a>. All rights reserved. -->
{$lng.lbl_copyright} &copy; {$config.Company.start_year}{if $config.Company.start_year lt $config.Company.end_year}-{$smarty.now|date_format:"%Y"} {/if}{$config.Company.company_name}. {$lng.lbl_all_right_reserved}. 
&nbsp;{$lng.lbl_copyright_links}

</div>
