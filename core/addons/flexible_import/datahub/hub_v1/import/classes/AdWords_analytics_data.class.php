<?php
/**
import note:  file must be saved as a tab delimited text file and named with .txt
the originial format is unicode text and won't work
*/
class AdWords_analytics_data extends feed {
	public static $table = 'AdWords_analytics_data';
	public static $feed_file = 'analytics_data.csv';
	
	public static function table_name() {
		return self::$table;
	}
//Equivalent:  hub_delete_adwords_PP_keydata_AE	
	public static function delete_table() {
		$sql = 'DELETE FROM ' . self::$table;
		mysql_query($sql) or sql_error($sql);
	}

//Equivalent:  AdWords import AE data only (macro)	
	public static function import_and_update() {
		if(self::feed_file(FEED_FILE . self::$feed_file, self::$table)) {
			self::delete_table();
			self::transfer_text(self::$feed_file, self::$table, ',');
			echo __CLASS__  . ' ' . __FUNCTION__ . ' complete<br />';			
		}	
	}
	
public static function transfer_text($feed_file, $table, $delimiter = "\t", $sanitize = array()) {
		$visits_flag = 0;
		$total_flag = 0;
		$gone_flag = 0;		
		$conversion_flag = 0;
		$revenue_flag = 0;
		$row = 1;
		$fields = '';	
		$sanitize_count = count($sanitize);
		
		$indices = array();
		
		$array = array('Total Goal Completions');//,'Per Visit Goal Value'
		
		if (($handle = fopen(FEED_FILE . $feed_file, "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
					$values = '';    
		      $num = count($data);
		
		      for ($c = 0; $c < $num; $c++) {
						$data[$c] = trim($data[$c]);		      
						if($row == 1) {
								$data[$c] = str_replace('.', '', $data[$c]);
								if($data[$c] == 'Visits' && empty($visits_flag)) {
									$visits_flag++;
								}
								elseif($data[$c] == 'Visits' && $visits_flag == 1) {
									$data[$c] = 'Field8';
									$visits_flag++;
								}	
								elseif($data[$c] == 'Visits' && $visits_flag == 2) {
									$indices[] = $c;
									continue;
								}				
													
								if($data[$c] == 'Total Goal Completions' && empty($total_flag)) {
									$total_flag++;
									$data[$c] = 'Field13';									
								}				
								elseif($data[$c] == 'Total Goal Completions' && !empty($total_flag)) {
									$indices[] = $c;
									continue;								
								}		
								
								if($data[$c] == 'Per Visit Goal Value' && empty($gone_flag)) {
									$gone_flag++;						
								}				
								elseif($data[$c] == 'Per Visit Goal Value' && !empty($gone_flag)) {
									$indices[] = $c;
									continue;								
								}				
								
								if($data[$c] == 'Goal Conversion Rate' && empty($conversion_flag)) {
									$conversion_flag++;						
								}				
								elseif($data[$c] == 'Goal Conversion Rate' && !empty($conversion_flag)) {
									$indices[] = $c;
									continue;								
								}												
								
								if($data[$c] == 'Revenue' && empty($revenue_flag)) {
									$revenue_flag++;						
								}				
								elseif($data[$c] == 'Revenue' && !empty($revenue_flag)) {
									$indices[] = $c;
									continue;								
								}												
																
											
													
														
								if($data[$c] == 'Email Signup') {
									$data[$c] = 'Field17';
								}

								if (in_array($data[$c], $array)) {
									$indices[] = $c;
									continue;
								}
//								print_r($array);
//								die;
								//$data[$c] = str_replace('Total Goal Completions', 'Field13', $data[$c]);
								$fields .= "`$data[$c]`,";								
						}  
						else {
							if(in_array($c, $indices)) {
								continue;
							}
							if($sanitize_count > 0) {
								$data[$c] = sanitizer::strip($data[$c], $sanitize);							
							}

							$data[$c] = mysql_real_escape_string($data[$c]);
							//acme_add_vintage			
							$values .= "'$data[$c]',";
						}     
		
					}      
					$fields = rtrim($fields, ',');    
					$values = rtrim($values, ',');  		
//		print_r($fields);
//		die;
					if($row > 1) {
						$sql = "INSERT INTO " . $table . " ($fields)
										VALUES ($values)";
						//echo $sql . '<br>';
						mysql_query($sql) or sql_error($sql);
					}
		      $row++;        
		    }
		    fclose($handle);
		}
		return $row;	
	}	
}//end class


//'Average Value',
//'Ecommerce Conversion Rate',
//'Per Visit Value',
//'Field8',
//'Pages/Visit',
//'Avg Time on Site',
//'% New Visits',
//'Bounce Rate',
//'Field13',
//'Sale',
//'Goal Conversion Rate', 
//'Per Visit Goal Value',
//'Field17',
//'Impressions',
//'Clicks',
//'Cost',
//'CTR', 
//'CPC',
//'RPC', 
//'ROI', 
//'Margin'
//
//
//`Keyword`,
//`Visits`,
//`Revenue`,
//`Transactions`,
//`Average Value`,
//`Ecommerce Conversion Rate`,
//`Per Visit Value`,
//`Visits`, -->Field8
//`Pages/Visit`,
//`Avg Time on Site`,
//`% New Visits`,
//`Bounce Rate`,
//`Total Goal Completions`, -->Field13
//`Revenue`, --> gone
//`Visits`, --> gone
//`Sale`,
//`Goal Conversion Rate`,
//`Total Goal Completions`, -->gone
//`Per Visit Goal Value`,
//`Visits`,
//`Email Signup`,  -->Field17
//`Goal Conversion Rate`,
//`Total Goal Completions`,
//`Per Visit Goal Value`,
//`Visits`,
//`Impressions`,
//`Clicks`,
//`Cost`,
//`CTR`,
//`CPC`,
//`RPC`,
//`ROI`,
//`Margin`