<?php
 //Set no caching
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
 header("Cache-Control: no-store, no-cache, must-revalidate"); 
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache");
 ?>
<?php
require_once('constants.php');

?>
<a href="http://www.saratogawine.com/xc/mysql_access/import/">Home</a><br /><br />

<table border="1">
	<tr>
		<td valign="top">
			1) <a href="import.php">Import</a><br />
		</td>		
		<td valign="top">
			1b) <a href="bev_monthly.php">Import Bev Monthly</a><br />
		</td>		
		<td valign="top">
				2) <a href="feeds_build_compare.php?randseed=<?php echo rand(); ?>">Feeds Build Compare</a><br />
				
		</td>				
	</tr>
	
	<tr>
		<td valign="top">
				3) Login into shop comp and run the macro "import_feeds_item_compare_edit"<br />
		</td>		
		<td valign="top">
			 4) Edit feeds_item_compare1 locally<br />
		</td>		
		<td valign="top">
			5) On shop comp, run the following macros 
			<br />(push local edit feed items table back to web)
			<ul style="margin:0px;">
				<li>move_feeds_item_1 (wait until window closes before going to next step)</li> 
				<li>move_feeds_item_2 (wait until window closes before going to next step)</li>
				<li>move_feeds_item_3 (when prompted, hit 'q' and enter to complete process)</li>
			</ul>				
		</td>			
	</tr>
	<tr>
		<td valign="top">
			6) <a href="Feeds_AddUpdate_from_Compare.php?randseed=<?php echo rand(); ?>">Feeds_AddUpdate_from_Compare</a><br />			
		</td>		
		<td valign="top">
			7) <a href="update_only.php">Update Only</a><br />		
		</td>	
		<td valign="top">
			8) <a href="update_prices.php">Update Prices</a><br />			
		</td>				
	</tr>
	<tr>
		<td valign="top">
			9) <a href="prepare_site_update_transfer_tables.php">prepare_site_update_transfer_tables</a><br />			
		</td>		
		<td valign="top">
				<!--10) Go to <a href="http://www.discountwinebuys.com/mysql_access/form/dwb_manual_price.php" target="_blank">dwb_manual_price</a> and edit<br />-->
<div style="display:none;">	10) Go to shop comp and edit linked table dwb_manual_price<br /> </div>
			
		</td>		
		<td valign="top">
<div style="display:none;">	11) <a href="update_dwb_price.php">Update DWB Price</a><br /> </div>
		
		</td>				
	</tr>
	<tr>
		<td valign="top">
	12a) <a href="pos_update.php?randseed=<?php echo rand(); ?>">Update POS</a> 
			 
		</td>
		<td valign="top">
				12b) <a href="../../xfer.php?randseed=<?php echo rand(); ?>">transfer tables (SWE)</a>			 	
		</td>					
		<td valign="top">
<div style="display: none">			12c) <a href="http://www.discountwinebuys.com/mysql_access/import/dwb.php">transfer tables (DWB)</a></div>		
		</td>					
	</tr>		
	<tr>
		<td valign="top">
			13) <a href="export_new_pos.php?randseed=<?php echo rand(); ?>">export new items</a>
			
		</td>		
		<td valign="top">
				14)  <a href="export_change_pos.php?randseed=<?php echo rand(); ?>">export changed items</a>
			 
		</td>		
		<td valign="top">
				<?php //<a href="pos_stock_import.php">POS Stock Import</a> ?>
		         <a href="export_orphaned.php">Export Orphaned</a>	 
		</td>					
	</tr>		
<tr>
		<td valign="top">
		 	15) <a href="../../xferfull.php?randseed=<?php echo rand(); ?>"> FULL UPDATE transfer tables (SWE)</a>
		</td>					


<!--
<td valign="top">
		 <a href="optimize_xcart.php?randseed=<?php //echo rand(); ?>">Optimize x-cart tables (run this before running the import script)</a>
		</td>	-->	
		<td valign="top">
                	16) <a href="../../xfer_shw.php?randseed=<?php echo rand(); ?>">transfer data to site updates on shop.wines.com</a>
		</td>
		<td valign="top">
		        17) process import on <a href="http://shop.wines.com/import/import.php">shop.wines.com</a> 	
		</td>						
	</tr>		 
	
<!--	<tr>
		<td valign="top">
			&nbsp;
		</td>		
		<td valign="top">
			&nbsp;
		</td>
		<td valign="top">
			&nbsp;
		</td>						
	</tr>		-->
		
</table>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function masterCheck( btn, ca, ua )
{
 var elems = btn.form.elements, state = (btn.value == ca);

 for( var i = 0, len = elems.length; i < len; i++ )
  if( elems[ i ].type && elems[ i ].type=='checkbox' )
   elems[ i ].checked = state;
   
 btn.value = state ? ua : ca;  
}
//  End -->
</script>


