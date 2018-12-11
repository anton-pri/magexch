<?php
require_once('header.php');
ksort($update_only_tables);
//$post_tables = $update_only_tables;
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
						acme_feed::update_only();					
						break;
					case 'angels_share_feed':
						angels_share_feed::update();		
						break;
					case 'bear_feed':
						bear_feed::update_only();									
						break;		
					case 'BevAccessFeeds':
						BevAccessFeeds::daily_update_only();					
						break;								
					case 'bowler_feed':
						bowler_feed::update_only();										
						break;									
					case 'BWL_feed':
						BWL_feed::update_only();									
						break;
					case 'cavatappi_feed':
						cavatappi_feed::update_only();								
						break;						
					case 'cellar_feed':
						cellar_feed::update_only();								
						break;		
					case 'Cordon_feed':
						Cordon_feed::update_only();											
						break;	
					case 'cru_feed':
						cru_feed::update_only();											
						break;							
					case 'Domaine_feed':						
						Domaine_feed::update_only();						
					break;			
					case 'DWB_store_feed':						
					//none				
					break;					
					case 'EBD_feed':
						EBD_feed::update_only();											
						break;	
					case 'grape_feed':
						grape_feed::update_only();								
						break;						
					case 'noble_feed':
						noble_feed::update_only();								
						break;			
					case 'Polaner_feed':
						Polaner_feed::update_only();									
						break;
					case 'Skurnik_feed':
						Skurnik_feed::update_only();								
						break;		
					case 'SWE_store_feed':
					//	SWE_store_feed::SWE_update_store_feed();								
						break;													
					case 'Touton_feed':
						Touton_feed::update_only();						
						break;	
					case 'triage_feed':
						triage_feed::update_only();						 				
						break;											
					case 'vehr_feed':
						vehr_feed::update_only();							
						break;	
					case 'verity_feed':
						verity_feed::update_only();							
						break;									
					case 'vias_feed':
						vias_feed::update_only();										
						break;	
					case 'vinum_feed':
						vinum_feed::update_only();											
						break;												
					case 'vision_feed':
						vision_feed::update_only();									
						break;
					case 'wildman_feed':
						wildman_feed::update_only();									
						break;
                                        case 'cw_import_feed':
                                                cw_import_feed::update_only();
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


foreach ($update_only_tables as $k => $v) {
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
