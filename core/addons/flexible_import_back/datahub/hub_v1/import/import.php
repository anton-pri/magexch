<?php
require_once('header.php');

//ini_set('memory_limit', '1024M');

set_time_limit(15*60);

//foreach ($feed_tables as $k=>$v) echo $v."<br/>";
ksort($feed_tables);
//$post_tables = $feed_tables;
	//print_r($post_tables);
	if(!empty($_POST['posted'])) {
		foreach ($_POST as $k => $v) {
			if (preg_match("/include::/i", $k)) {
			    $temp = explode('::', $k);	
					$post_tables[$temp[1]] = $temp[1];			    		   
			    //unset($post_tables[$temp[1]]);
			}
		}
		//print_r($post_tables);
		if(isset($post_tables) && count($post_tables) > 0) {
			foreach ($post_tables as $k => $v) {
				switch ($v) {
					case 'acme_feed':
						acme_feed::import_and_update();					
						break;
					case 'angels_share_feed':
						angels_share_feed::update();		
						break;
					case 'bear_feed':
						bear_feed::import_and_update();										
						break;		
					case 'BevAccessFeeds':
						BevAccessFeeds::daily_import_and_update();					
						break;			
					case 'bevaccess_supplement':
						bevaccess_supplement::import_and_update();	
						break;											
					case 'bowler_feed':
						bowler_feed::import_and_update();										
						break;									
					case 'BWL_feed':
						BWL_feed::import_and_update();									
						break;
					case 'cavatappi_feed':
						cavatappi_feed::import_and_update();									
						break;							
					case 'cellar_feed':
						cellar_feed::import_and_update();									
						break;		
					case 'Cordon_feed':
						Cordon_feed::import_and_update();											
						break;	
					case 'cru_feed':
						cru_feed::import_and_update();										
						break;								
					case 'Domaine_feed':						
						Domaine_feed::import_and_update();
					break;			
					case 'DWB_store_feed':						
						DWB_store_feed::store_import();
					break;					
					case 'EBD_feed':
						EBD_feed::import_and_update();											
						break;	
					case 'grape_feed':
						grape_feed::import_and_update();								
						break;						
					case 'noble_feed':
						noble_feed::import_and_update();								
						break;			
					case 'Polaner_feed':
						Polaner_feed::import_and_update();									
						break;
					case 'Skurnik_feed':
						Skurnik_feed::import_and_update();									
						break;		
					case 'SWE_store_feed':
						SWE_store_feed::SWE_store_import_and_update(true);//on first import and update, bring in the pos table									
						break;													
					case 'Touton_feed':
						Touton_feed::import_and_update();										
						break;	
					case 'triage_feed':
						triage_feed::import_and_update();						 				
						break;											
					case 'vehr_feed':
						vehr_feed::import_and_update();								
						break;	
					case 'verity_feed':
						verity_feed::import_and_update();								
						break;								
					case 'vias_feed':
						vias_feed::import_and_update();										
						break;	
					case 'vinum_feed':
						vinum_feed::import_and_update();										
						break;												
					case 'vision_feed':
						vision_feed::import_and_update();										
						break;
					case 'wildman_feed':
						wildman_feed::import_and_update();					
						break;					
                                        case 'cw_import_feed':
                                                cw_import_feed::update();
	                                        break; 
					default:
						break;
				}
				echo '<br />';	
			}		
		}
		else {
			echo '<br />no feeds selected<br />';
		}

		$_POST = array();
	}


	
	//angels_share_feed::update();
	//bear_feed::import_and_update();
	
//	BevAccessFeeds::delete_UP_prod();
//	BevAccessFeeds::transfer_text('UP_prod.CSV', 'UP_prod', ',', array('rose'));

//BevAccessFeeds::update_up_prod_wildman();
?>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" style="margin:0px;" name="form1">
<input type="button" name="checkall" value="Check ALL" onClick="masterCheck(this, 'Check ALL', 'Uncheck ALL')" />




<table border="1">
<tr>
	<td>Feed Name</td>
	<td>Include</td>	
</tr>
<?php
$hidden_options = Array (
'BWL_feed' => 'BWL_feed',
'Cordon_feed' => 'Cordon_feed',
'EBD_feed' => 'EBD_feed',
'DWB_store_feed' => 'DWB_store_feed',
'acme_feed' => 'acme_feed',
'bear_feed' => 'bear_feed',
'bowler_feed' => 'bowler_feed',
'cavatappi_feed' => 'cavatappi_feed',
'cellar_feed' => 'cellar_feed',
'cru_feed' => 'cru_feed',
'grape_feed' => 'grape_feed',
'noble_feed' => 'noble_feed',
'triage_feed' => 'triage_feed',
'vehr_feed' => 'vehr_feed',
'vinum_feed' => 'vinum_feed',
'vision_feed' => 'vision_feed');

foreach ($feed_tables as $k => $v) {
    if (in_array($v, $hidden_options)) continue;
    echo "<tr><td>$v</td><td><input type=\"checkbox\" name=\"include::$v\" value=\"1\" /></td>";
}
?>
<tr>
	<td colspan="2"><input type="submit" name="submit" value="Submit" /></td>
</tr>
</table>
<input type="hidden" name="posted" value="1" />
</form>
