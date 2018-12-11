<?php
//error_reporting(E_ALL ^ E_NOTICE);

function fout ($msg='.') {
static $i=0;
if ($i%5==2 || $msg!='.') {
echo $msg; echo str_repeat(' ',256-strlen($msg));
@ob_flush(); @flush();
}
$i++;
}

// Create table cw_categories_parents
// (fixing categories bugs)
function cw_cat_level_calc ($id,&$tree) {
	extract ($tree[$id]);
	if (!isset($level)) {
		if (!isset($tree[$parent_id]['level'])) cw_cat_level_calc ($parent_id,$tree);
		$tree[$id]['level']=$tree[$parent_id]['level']+1;
	}
}


function cw_cat_tree_building () {
	db_query ("TRUNCATE TABLE cw_categories_parents");
	$_tree=cw_query("Select category_id,parent_id from cw_categories");
	foreach ($_tree as $v) {
		extract ($v); $id=$category_id;
		if ($parent_id==0) { $tree[$id]['parent_id']=$id; $tree[$id]['level']=0; $tree[$id]['category_id']=$id; }
		else $tree[$id]=compact('category_id','parent_id');
	}
	foreach ($tree as $id => $arr) {
		if (!isset($arr['level'])) cw_cat_level_calc ($id,$tree);
		cw_array2insert ('cw_categories_parents',$tree[$id]);
	}
}
// --------------------------------



// Get XCART MySql Authorization from config.php
// Get XCART blowfish_key to decript passwords & convert to hash using cw_user_get_hashed_password 
// Establish new connection for XCART database, without destroying default CW connection
// And returns connection id...
function cw_xcart_get_conn ($path,&$err_msg,&$xcart_conf) {
	$err_msgs[0]=str_replace ('%path%',$path, "Error: Given path to XCART directory [%path%] is broken");
	$err_msgs[1]=str_replace ('%path%',$path, "Error: XCART config.php file were not found by given path [%path%]");
	$err_msgs[2]=str_replace ('%path%',$path, "Error: XCART installation by given path [%path%] is misconfigured");

	if (!is_dir($path)) {
		$err_msg=$err_msgs[0];
		return false;
	}
	if (!file_exists("$path/config.php")) {
		$err_msg=$err_msgs[1];
		return false;
	}
	define ("XCART_START",1);
	include "$path/config.php";
	if (file_exists("$path/config.local.php")) include "$path/config.local.php";

	if (!$conn=mysql_connect($sql_host, $sql_user, $sql_password)) {
		$err_msg=$err_msgs[2];
		return false;
	}
	if (!mysql_select_db ($sql_db,$conn)) {
		$err_msg=$err_msgs[2];
		return false;
	}

	$xcart_conf=compact('sql_host','sql_user','sql_password','sql_db','conn','path','blowfish_key');
	return $conn;
}

// Escaped array2insert
function cw_array2insert_esc ($tab,$arr) {
foreach ($arr as $k => $v) {
if (preg_match("'\''",$v)) $arr[$k]=addslashes($v);
}
print("i ");
/*
print("<br>");
print_r(array($tab,$arr)); 
print("<br>\n");
return;*/
cw_log_add("import_xcart",array("import", $tab,$arr));
return cw_array2insert ($tab,$arr);
}

function cw_array2update_esc ($tab,$arr,$where='') {
foreach ($arr as $k => $v) {
if (preg_match("'\''",$v)) $arr[$k]=addslashes($v);
}
print("u ");
/*
print("<br>");
print_r(array($tab,$arr,$where)); 
print("<br>\n");
return;*/
cw_log_add("import_xcart",array("update", $tab,$arr,$where));
return cw_array2update ($tab,$arr,$where);
}

// get full lists of tables and fields inside them
function cw_tables_fields_lists ($conn) {
	static $table_list=array();
	static $fields_list=array();
	if (empty($table_list)) $table_list=cw_query_column ("show tables",0,$conn);
	if (empty($fields_list)) foreach ($table_list as $v) $fields_list[$v]=cw_query_column ("desc $v",0,$conn);
	return compact('table_list','fields_list');
}

// check if table exists or not
function cw_table_exists ($conn, $tab) {
	static $tabs_existance=array();
	if (empty($tabs_existance) || !isset($tabs_existance[$tab])) {
		  extract (cw_tables_fields_lists ($conn));
		  if (in_array($tab, $table_list)) $tabs_existance[$tab]=true; else $tabs_existance[$tab]=false;
	}
	return ($tabs_existance[$tab]);
}

// check if field in table exists or not
function cw_field_exists ($conn, $tab, $fld) {
	extract (cw_tables_fields_lists ($conn));
	if (in_array($tab, $table_list) && in_array($fld, $fields_list[$tab])) return true; else return false;
}

// version difference attributes
function cw_vers_diff_attr ($conn) {
	static $attrs=array();
	if (empty($attrs)) {
		if (cw_field_exists ($conn,'xcart_customers','id')) $attrs['uwithid']=true; else $attrs['uwithid']=false;
		if (cw_table_exists ($conn,'xcart_address_book')) $attrs['withab']=true; else $attrs['withab']=false;
		if (cw_table_exists ($conn,'xcart_clean_urls')) $attrs['withcurls']=true; else $attrs['withcurls']=false;
		if (cw_field_exists ($conn,'xcart_categories','meta_description')) $attrs['cwithmeta']=true; else $attrs['cwithmeta']=false;
		if (cw_field_exists ($conn,'xcart_products','meta_description')) $attrs['pwithmeta']=true; else $attrs['pwithmeta']=false;
		if (cw_field_exists ($conn,'xcart_manufacturers','meta_description')) $attrs['mwithmeta']=true; else $attrs['mwithmeta']=false;
	}
	return $attrs;
}



// Copy any table from XCART to CARTWORKS literally (as it is)
function cw_table_copy_from_xcart_lit ($conn, $xc_tab, $cw_tab, $flds='*', $ro=false) {
	fout();
	db_query ("TRUNCATE TABLE $cw_tab");
	$table=cw_query ("select $flds from $xc_tab",$conn);
	if (!$ro) foreach ($table as $arr)  cw_array2insert_esc ($cw_tab,$arr);
	return $table;
}

// Copy any table from XCART to CARTWORKS literally (as it is) -- Short Notation
// Single (unified) table name parameter for both tables -- without prefixes
function cw_table_copy_from_xcart_lit_sn ($conn, $tab, $flds='*', $ro=false) {
	return cw_table_copy_from_xcart_lit ($conn, "xcart_$tab", "cw_$tab", $flds, $ro);
}

// Copy any table from XCART to CARTWORKS, only fields with the same names plus fields defined explicitly ($flds)
// (Even common fields could be defined explicitly with different values)
function cw_table_copy_from_xcart_comm ($conn, $xc_tab, $cw_tab, $flds='', $ro=false) {
	fout();
	$xc_tab_flds=cw_query_column ("desc $xc_tab",'Field', $conn);
	$cw_tab_flds=cw_query_column ("desc $cw_tab",'Field');
	$flds_arr=explode(',',$flds);
	foreach ($flds_arr as $k => $v) if (preg_match("'\s's",trim($v)))
		$flds_arr[$k]=preg_replace("'^.*\s+([^\s]+)$'s","$1",trim($v)); else $flds_arr[$k]=trim($v);
	$common=array_intersect($cw_tab_flds, $xc_tab_flds);
	$common=array_diff($common, $flds_arr);
	$common=implode (',',$common);
	if ($common!='') if ($flds=='') $flds=$common; else $flds.=",$common";
	if ($flds!='') return cw_table_copy_from_xcart_lit ($conn, $xc_tab, $cw_tab, $flds, $ro);
	else return array();
}

// Copy any table from XCART to CARTWORKS, only fields with the same names plus fields defined explicitly ($flds)
// (Even common fields could be defined explicitly with different values)
// Short Notation -- Single (unified) table name parameter for both tables -- without prefixes
function cw_table_copy_from_xcart_comm_sn ($conn, $tab, $flds='', $ro=false) {
	$xc_tab="xcart_$tab"; $cw_tab="cw_$tab";
	return cw_table_copy_from_xcart_comm ($conn, $xc_tab, $cw_tab, $flds, $ro);
}

// remove old images from a specified folder
function cw_rm_old_files ($dir) {
	if (!is_dir($dir)) mkdir($dir);
	if($dh = opendir($dir)) {
		while(($file = readdir($dh))!== false) if (is_file("$dir/$file")) @unlink("$dir/$file"); closedir($dh);
	}
}

// Scale image
function cw_img_scale ($fn, $w) {
$image = imagecreatefromstring(file_get_contents($fn));
$size=getimagesize($fn);
$width=$size[0]; $height=$size[1]; $image_type=$type=$size[2];
$image_y=$h=$w*$height/$width; $image_x=$w;
$im = imagecreatetruecolor($w, $h);
imagecopyresized($im, $image, 0, 0, 0, 0, $w, $h, $width, $height);
    switch ($type) {
        case 'image/jpeg': imagejpeg($im, $fn, 100); break;
        case 'image/gif': imagegif($im, $fn); break;
        case 'image/png': imagepng($im, $fn, 9); break;
	default: imagejpeg($im, $fn, 100);
    }    
return compact('image_x','image_y','image_type');
}

// Copy Images 
function cw_imgs_copy ($xcart_conf, $xc_tab, $cw_tab, $img_fld, $maxwidth=170) {
	static $imgs1=array();
	static $imgs2=array();
	static $imgs3=array();

	extract($xcart_conf);
	@ini_set('user_agent','BOT');

	if (empty($imgs1) && empty($imgs2) && empty($imgs3)) {
		$imgs1=cw_query ("select * from xcart_images_D",$conn);
		$imgs2=cw_query ("select * from xcart_images_P",$conn);
		$imgs3=cw_query ("select * from xcart_images_T",$conn);
	}

	db_query ("TRUNCATE TABLE $cw_tab");
	$target="./files/images/$img_fld"; cw_rm_old_files ($target);
	// If prod. det. img. doesn't exist, thumbnail will be taken instead of it
	// However, if it exists, detailed prod. image will be taken
	if ($xc_tab=='xcart_images_D' || $xc_tab=='xcart_images_P' || $xc_tab=='xcart_images_T') {
		if ($xc_tab=='xcart_images_D') $imgs=array_merge($imgs3,$imgs2,$imgs1);
		if ($xc_tab=='xcart_images_P') $imgs=array_merge($imgs3,$imgs1,$imgs2);
		if ($xc_tab=='xcart_images_T') $imgs=array_merge($imgs1,$imgs2,$imgs3);
		if (!empty($imgs)) {
		      $im=array(); foreach ($imgs as $v) {
				$pth=$v['image_path']; $pth=preg_replace("'^\./'","",trim($pth));
				if (!preg_match("'^[a-z]+\://'",$pth)) $pth="$path/$pth";
				if (@fopen($pth,'r')) $im[$v['id']]=$v;
		      }
		      $imgs=$im; unset($im); $i=1; foreach ($imgs as $k => $v) $imgs[$k]['imageid']=$i++;
		} 
	} else 	$imgs=cw_query ("select * from $xc_tab",$conn); $ids=array();
	foreach ($imgs as $v) {
		fout();
		extract ($v); $from=preg_replace("'^\./'","",trim($image_path)); $to="$target/$filename";
		if (!preg_match("'^[a-z]+\://'",$from)) $from="$path/$from";
		$image_path=$to; $avail=($avail=='Y')?1:0;
		$image_id=$imageid; 
        if (@copy($from,$to)) {
			$image_path=$to;
			if (0 && $image_x>$maxwidth) {
				extract(cw_img_scale ($to, $maxwidth));
				$image_size=filesize($to); $md5=md5_file($to);
			}
			$arr=compact('image_id','id','image_path','image_type','image_x','image_y','image_size','filename',
			    'date','alt','avail','orderby','md5');
			if ($img_fld=='products_detailed_images' && $image_x<200) @unlink ($to); else
			if (!in_array($id,$ids)) cw_array2insert_esc ($cw_tab, $arr); $ids[]=$id; // id have to be unique in cw
		}
	}
}

// Set attributes
function cw_set_attr ($type,$attr,$item_id,$value) {
	global $config;
	static $attr_ids=array();
	static $first_time=true;

	// Delete attributes values and save available atributes in stayic array only once (during first call)
	if ($first_time) {
		db_query ("Delete from cw_attributes_values where item_type='P' or item_type='C' or item_type='M'");
		$attrs=cw_query ("select attribute_id,field,item_type from cw_attributes");
		foreach ($attrs as $v) {
			extract($v); $attr_ids[$item_type][$field]=intval($attribute_id);
		}
		$first_time=false;
	}

	if (isset($attr_ids[$type]) && is_array($attr_ids[$type]) && isset ($attr_ids[$type][$attr]) && $attr_ids[$type][$attr]>0) {
			$attribute_id=$attr_ids[$type][$attr]; $item_type=$type;
			if ($attr=='domains') $code=''; else $code=$config['default_customer_language'];
			cw_array2insert_esc ('cw_attributes_values', compact ('item_id','attribute_id','value','code','item_type'));
	}
	fout();
}

// Import config entities by keywords like '*currency*, ...
function cw_import_config ($conn, $keys='') {
if ($keys=='') $keys='*currency*, *tax*';

$keys=str_replace("*",'%',$keys);
$keys=preg_replace("'\s*\,\s*'", ",", $keys);
$keys=trim($keys);
$keys=explode(',',$keys); $where='';
foreach ($keys as $v) $where.="name like '$v'\t\t";
$where="where ".str_replace ("\t\t", " or ", trim($where));
$xc_nms=cw_query_column("select name from xcart_config $where", 0, $conn);
$sw_nms=cw_query_column("select name from cw_config $where", 0);
$names=array_intersect($sw_nms,$xc_nms);
foreach ($names as $k => $v) $names[$k]="'$v'";
$names=implode(', ',$names);
$conf=cw_query("Select * from xcart_config where name in ($names)",$conn);
foreach ($conf as $v) {
extract ($v);
$arr=compact('comment','value','orderby','type','defvalue', 'variants');
cw_array2update_esc ('cw_config',$arr,"name='$name'");
}
}



// Import of customers & address book tables
// Keep users autorization by old XCART logins/passwords
// Does not remove admin role users from CARTWORKS
// (Actually rewrite them under another IDs)
function cw_import_users($xcart_conf) {
	cw_load ('crypt', 'user');
	extract ($xcart_conf);
	extract (cw_vers_diff_attr ($conn));
	fout ("<br /><br />Import of Users...<br />");

if ($clean_users) {
	$cw_admins=cw_query ("select * from cw_customers u left join cw_customers_addresses a
			on u.customer_id=a.customer_id and a.main=1 where u.usertype='A'");
	db_query ("TRUNCATE TABLE cw_customers");
	db_query ("TRUNCATE TABLE cw_customers_system_info");
	db_query ("TRUNCATE TABLE cw_customers_customer_info");
}

        $users_per_run = 500;

        global $page;
        if (!isset($page))
            $page = 1;

        $users_offset = $users_per_run*($page-1);
        $users_count = $users_per_run; 
 
        $where_ids_range = "WHERE id > 58965";
	$users=cw_query ("select * from xcart_customers $where_ids_range order by usertype in ('P','A') desc, login=email desc limit $users_offset, $users_count",$conn);
        $users_count = count($users); 

        $processed_customers = array();
	$i=1; $em=array(); $user_ind=array();
	foreach ($users as $k => $v) {
		$v['password']= cw_user_get_hashed_password(text_decrypt($v['password'],$blowfish_key)); 
                extract ($v);
		$membership_id=$membershipid; //$email=$login;
		if ($uwithid) $customer_id=$id; else $customer_id=$i++;
                $processed_customers[] = $id;
		$language=strtoupper($language);
		if (in_array($email,$em)) { $log=str_replace(" ","-",$login); $email="$log-$email"; $users[$k]['email']="$log-$email"; }
		$em[]=$email; if ($usertype=='A' || $usertype=='P') $usertype='A'; else $usertype='C';

		$arr=compact('customer_id','usertype','password','email','status','membership_id','language');
		$usrs[$customer_id]=$arr;
		cw_array2insert_esc ('cw_customers',$arr);
		$creation_customer_id=$customer_id; 
                $creation_date=$first_login;//time();
		$modification_customer_id=$customer_id; 
                $modification_date=time(); 
                //$last_login=time(); - already extracted
		$arr=compact('customer_id','creation_customer_id','creation_date','modification_customer_id','modification_date','last_login');
		cw_array2insert_esc ('cw_customers_system_info',$arr);
		$web_user=1; 
                cw_array2insert_esc ('cw_customers_customer_info',compact('customer_id','web_user'));
		$ul[]=$email; $companies[$customer_id]=$company; $user_ind[$login]=$customer_id; $users[$k]['userid']=$customer_id;
		fout();
	}

	fout ("<br /><br />Import of an Address Book...<br />");

if ($clean_users) { 
	db_query ("TRUNCATE TABLE cw_customers_addresses"); 
}
        //$i=10000; //address book id start
	if ($withab) $addresses=cw_query("select * from xcart_address_book where userid in ('".implode("','", $processed_customers)."')",$conn);
	  else foreach ($users as $v) {
		extract($v);
		if ($b_firstname!='' || 1) {
			//$id=$i++; 
                        $firstname=$b_firstname; $lastname=$b_lastname; $address=$b_address; $city=$b_city;
			$county=$b_county; $state=$b_state; $country=$b_country; $zipcode=$b_zipcode; $default_b='Y'; $default_s='N';
			$addresses[]=compact('id','userid','firstname','lastname','address','city','county','state','country',
				'zipcode','default_b','default_s','phone','fax');
		}
		if ($s_firstname!='' || 1) {
			//$id=$i++; 
                        $firstname=$s_firstname; $lastname=$s_lastname; $address=$s_address; $city=$s_city;
			$county=$s_county; $state=$s_state; $country=$s_country; $zipcode=$s_zipcode; $default_b='N'; $default_s='Y';
			$addresses[]=compact('id','userid','firstname','lastname','address','city','county','state','country',
				'zipcode','default_b','default_s','phone','fax');
		}
	}

	unset($users);
        $cust_addr_ids = array();
	foreach ($addresses as $v) {
		extract ($v);
		$address_id=$id; $customer_id=$userid; $region=$county;
		$main=($default_b=='Y')?1:0; $current=($default_s=='Y')?1:0;
		if (isset($companies[$customer_id])) $company=$companies[$customer_id]; else $company='';
		$arr=compact('address_id','customer_id','main','current','firstname','lastname',
			'address','city','state','country','region','zipcode','phone','fax','company');
		cw_array2insert_esc ('cw_customers_addresses',$arr);
                $cust_addr_ids[$customer_id] = array();
		if ($main) $cust_addr_ids[$customer_id]['main']=$address_id;
		if ($current) $cust_addr_ids[$customer_id]['current']=$address_id;
		fout();
	}

	unset($addresses);

	if (isset($cw_admins) && is_array($cw_admins) && !empty($cw_admins)) {
            $uid=intval(cw_query_first_cell("select customer_id from cw_customers order by customer_id desc limit 1"))+1;
            foreach ($cw_admins as $v) {
		if (!in_array($v['email'],$ul)) {
			$v['customer_id']=$uid; 
                        extract ($v);
			compact('customer_id','usertype','password','email','status','membership_id','language');
			cw_array2insert_esc('cw_customers',$v);
			$ul[]=$v['email']; 
                        $creation_date=time(); 
                        $creation_customer_id=$customer_id;
			$modification_customer_id=$customer_id; 
                        $modification_date=time(); 
                        $last_login=time();
			$arr=compact('customer_id','creation_customer_id','creation_date',
				'modification_customer_id','modification_date','last_login');
			cw_array2insert_esc ('cw_customers_system_info',$arr);
			$web_user=1; 
                        cw_array2insert_esc ('cw_customers_customer_info',compact('customer_id','web_user'));

			if (!isset($firstname) || $firstname='') $firstname='Admin';
			if (!isset($lastname) || $lastname='') $lastname='Admin';
			$main=1; 
                        $arr=compact('customer_id','main','current','firstname','lastname',
				'address','state','country','region','zipcode','phone','fax','company');
			cw_array2insert_esc ('cw_customers_addresses',$arr);
			$uid++;
		}
            }   
        } 

	// wishlists
/*
	$wishlist=cw_query("select * from xcart_wishlist",$conn);
	db_query ("TRUNCATE TABLE cw_wishlist");
	foreach ($wishlist as $v) {
		extract($v);
		if ($uwithid) $customer_id=$userid; else $customer_id=$user_ind[$login];
		$wishlist_id=$wishlistid; $product_id=$productid;
		$arr=compact('wishlist_id','customer_id','product_id','amount','amount_purchased','options','event_id','object');
		cw_array2insert_esc ('cw_wishlist',$arr);
		fout();
	}
*/
	fout ("<br /><br />Import of Orders & Invoices...<br />");
//print_r($cust_addr_ids);
	// orders, invoices
if ($clean_docs) { 
	db_query ("TRUNCATE TABLE cw_docs");
	db_query ("TRUNCATE TABLE cw_docs_info");
	db_query ("TRUNCATE TABLE cw_docs_user_info");
}

        $orders_per_run = 250;
        $orders_offset = $orders_per_run*($page-1);
        $orders_count = $orders_per_run;

        $where_orderids_range = "WHERE orderid > 63375";

	$orders=cw_query("select * from xcart_orders $where_orderids_range limit $orders_offset, $orders_count",$conn);
        $orders_count = count($orders);
	$doc_ids=array();
        $processed_orders = array();
	//$doc_id=$doc_info_id=1; 
        $type="O"; 
        //$_display_id=$_display_doc_id=1;
	foreach ($orders as $v) {
		extract ($v);
		//$doc_ids[$orderid]=$doc_id;
                $doc_id = $orderid;
                $processed_orders[] = $orderid;
                $doc_info_id = $doc_id;   
                $doc_ids[$orderid]=$orderid;
		$year=date("Y",$date);
                $display_id = "SW ".$doc_id;
                $display_doc_id = $doc_id;   
		$arr=compact('doc_id','doc_info_id','type','display_id','display_doc_id','year','date','status');
		cw_array2insert_esc('cw_docs',$arr);
		$payment_id=$paymentid; $display_total=$total; $display_subtotal=$subtotal; $shipping_id=$shippingid;
		$details=cw_crypt_text(text_decrypt($details,$blowfish_key));
		$applied_taxes=$taxes_applied;
		$shipping_id=$shippingid; $display_shipping_cost=$shipping_cost;
		$shipping_label=(isset($shipping))?$shipping:'';
		$payment_label=$payment_method;
		$discount_value=$discount;
		$arr=compact('doc_info_id','total','display_total','subtotal','display_subtotal','extra','details',
			'payment_id','shipping_id','shipping_cost','notes','tax','applied_taxes','customer_notes',
			'payment_label','payment_surcharge','shipping_id','shipping_cost','display_shipping_cost','shipping_label',
			'giftcert_discount','coupon','coupon_discount','discount','discount_value');
		cw_array2insert_esc('cw_docs_info',$arr);
		if ($uwithid) $customer_id=$userid; else $customer_id=$user_ind[$login];
                $cust_inf = cw_query_first("select c.usertype, c.email from cw_customers where customer_id='$customer_id'");
                $usertype = $cust_inf['usertype'];
                $email= $cust_inf['email'];
                $main_address_id = cw_query_first_cell("select address_id from cw_customers_addresses where customer_id='$customer_id' and main=1");
                $current_address_id = cw_query_first_cell("select address_id from cw_customers_addresses where customer_id='$customer_id' and current=1");
/*
		$usertype=$usrs[$customer_id]['usertype'];
		if (isset($cust_addr_ids[$customer_id]['main']))
			$main_address_id=$cust_addr_ids[$customer_id]['main']; else $main_address_id=0;
		if (isset($cust_addr_ids[$customer_id]['current']))
			$current_address_id=$cust_addr_ids[$customer_id]['current']; else $current_address_id=0;
		$email=$usrs[$customer_id]['email'];
*/
		$arr=compact('doc_info_id','customer_id','usertype','main_address_id','current_address_id','email','tax_number','tax_exempt');
		cw_array2insert_esc('cw_docs_user_info',$arr);
		$display_id++; $display_doc_id++; $doc_id++; $doc_info_id++;
		fout();
	}
	unset($orders); unset($usrs); 

        if ($clean_docs) { 
            db_query ("TRUNCATE TABLE cw_docs_items");
        }

	$details=cw_query("select * from xcart_order_details where orderid in ('".implode("','", $processed_orders)."')",$conn);
	foreach ($details as $v) {
		extract ($v);
		$item_id=$itemid; $doc_id=$doc_ids[$orderid]; $product_id=$productid;
		if ($uwithid) $warehouse_customer_id=$provider; else $warehouse_customer_id=$user_ind[$provider];
		$arr=compact('item_id','doc_id','product_id','productcode','product','product_options','price','amount',
			'extra_data','warehouse_customer_id');
		cw_array2insert_esc('cw_docs_items',$arr);
	}
        if ($users_count || $orders_count) {
            return $page+1;
        } else {
            return 0; 
        }  

}


// Import of manufacturers, products' categories & products tables
// And tables directly related with them
// As well as images have any relation with them
function import_products ($xcart_conf) {
	extract ($xcart_conf);
	extract (cw_vers_diff_attr ($conn));
	fout ("<br /><br />Import of manufacturers, Products, Categories...<br />");

	$cats=cw_table_copy_from_xcart_lit_sn ($conn, 'categories',
		"categoryid as category_id, parentid as parent_id, if(avail='Y',1,0) as status, category, description, order_by");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_categories', 'cw_categories_stats', "categoryid as category_id, views_stats");
	cw_table_copy_from_xcart_lit_sn ($conn, 'categories_lng', "UPPER(code) as code, categoryid as category_id, category, description");
	cw_table_copy_from_xcart_lit_sn ($conn, 'products_categories', "categoryid as category_id, productid as product_id,
		  if(main='Y',1,0) as main, orderby");

	$prods=cw_table_copy_from_xcart_comm_sn ($conn, 'products', "productid as product_id, if(forsale='Y',1,0) as status");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_products', 'cw_products_stats',
		"productid as product_id, views_stats, sales_stats, del_stats");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_products', 'cw_products_warehouses_amount', "productid as product_id, avail");

	cw_table_copy_from_xcart_lit_sn ($conn, 'products_lng',
		"UPPER(code) as code, productid as product_id, product,descr,fulldescr,keywords");

	db_query ("TRUNCATE TABLE cw_products_prices");
	$sql="select priceid as price_id, prc.productid as product_id, quantity, price, list_price,
		variantid as variant_id, membershipid as membership_id from xcart_pricing prc
		left join xcart_products prd on prc.productid=prd.productid order by product_id, variant_id";
	$prices=cw_query($sql,$conn); foreach ($prices as $arr) cw_array2insert_esc ('cw_products_prices',$arr);

	// Memberships
	db_query ("Delete from cw_memberships where membership_id!=0");
	$mbrsh=cw_query ("select * from xcart_memberships  where membershipid!=0",$conn);
	$level=cw_query_first_cell ("select level from cw_access_levels  where membership_id=0 and area='A'");
	db_query ("Delete from cw_access_levels where membership_id!=0");
	foreach ($mbrsh as $v) {
		extract($v);
		if ($area=='A' || $area=='P') $area='A'; else $area='C';
		$membership_id=$membershipid; $show_prices=1;
		cw_array2insert_esc ('cw_memberships', compact('membership_id','area','membership','active','orderby','flag','show_prices'));
		if ($area=='A') cw_array2insert_esc('cw_access_levels',compact('membership_id','area','level'));
	}
	db_query ("Delete from cw_memberships_lng where membership_id!=0");
	$mbrshl=cw_query ("select * from xcart_memberships_lng  where membershipid!=0",$conn);
	foreach ($mbrshl as $v) {
		extract($v);
		$membership_id=$membershipid; $code=strtoupper($code);
		cw_array2insert_esc ('cw_memberships_lng', compact('membership_id','code','membership'));
	}
	cw_table_copy_from_xcart_lit ($conn, 'xcart_products', 'cw_products_memberships', "productid as product_id, 0 as memberships_id");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_categories', 'cw_categories_memberships', "categoryid as category_id, 0 as memberships_id");
	$catmem=cw_query ("select * from xcart_category_memberships",$conn);
	$prodmem=cw_query ("select * from xcart_product_memberships",$conn);
	foreach ($catmem as $v) {
		extract($v); $membership_id=$membershipid; $category_id=$categoryid;
		cw_array2insert ('cw_categories_memberships', compact('category_id', 'membership_id'));
	}
	foreach ($prodmem as $v) {
		extract($v); $membership_id=$membershipid; $product_id=$productid;
		cw_array2insert ('cw_products_memberships', compact('product_id', 'membership_id'));
	}

	// manufacturers
	$mans=cw_table_copy_from_xcart_comm_sn ($conn, 'manufacturers', "manufacturerid as manufacturer_id,
		provider as warehouse_customer_id, if(avail='Y',1,0) as avail");
	cw_table_copy_from_xcart_comm_sn ($conn, 'manufacturers_lng', 'manufacturerid as manufacturer_id, UPPER(code) as code');

	// Set Attributes: 0 for domains; Meta Tags, manufacturer_id for products, etc.
	if ($withcurls) {
		$tmp=cw_query("SELECT * from xcart_clean_urls",$conn); $clean_urls=array();
		foreach ($tmp as $v) {
			extract($v); $clean_urls[$resource_type][$resource_id]=str_replace('.','-',$clean_url);
		}
	} else {
		$tmp=cw_query("Select 'C' as type, categoryid as id, LCASE(category) as name from xcart_categories
			union Select 'P' as type, productid as id, LCASE(product) as name from xcart_products
			union Select 'M' as type, manufacturerid as id, LCASE(manufacturer) as name from xcart_manufacturers",$conn);
		$clean_urls=array(); $curls=array();
		foreach ($tmp as $v) {
			extract($v); $cu=preg_replace("'[^a-z0-9]+'s",' ',$name);
			$cu=str_replace(' ','-',$cu); $i=1; $curl=$cu;
			while (in_array($curl,$curls)) $curl=$cu.'-'.($i++);
			$clean_urls[$type][$id]=$curl; $curls[]=$curl;
		}
		unset($curls);
	}
	unset($tmp);

	if ($pwithmeta) $prodmeta=cw_query ("Select productid as id, 'P' as type, product, manufacturerid,
		  meta_description, meta_keywords, title_tag, add_date from xcart_products", $conn);
	else {
		$prodmeta=cw_query ("Select productid as id, 'P' as type, product, descr,
			manufacturerid, add_date from xcart_products", $conn);
		foreach ($prodmeta as $k => $v) {
			extract ($v);
			$prodmeta[$k]['title_tag']=trim($product);
			$descr=preg_replace ("'</?[a-z]+.*?>'is"," ",$descr);
			$descr=trim(preg_replace("'\s+'", " ", $descr));
			$prodmeta[$k]['meta_description']=$descr;
			$mk=preg_replace ("'[^a-z]+'is", " ", $product);
			$prodmeta[$k]['meta_keywords']=str_replace (' ',', ',trim($mk));
		}
	}

	db_query ("TRUNCATE TABLE cw_products_system_info");
	$admin=cw_query_first_cell("select customer_id from cw_customers where usertype='A' limit 1");
	foreach ($prodmeta as $v) {
		extract($v);
		cw_set_attr ($type,'domains',$id,0);
		cw_set_attr ($type,'meta_title',$id,$title_tag);
		cw_set_attr ($type,'meta_keywords',$id,$meta_keywords);
		cw_set_attr ($type,'meta_description',$id,$meta_description);
		if (isset($clean_urls[$type]) && isset($clean_urls[$type][$id]))
			cw_set_attr ($type,'clean_url',$id,$clean_urls[$type][$id]);
		cw_set_attr ($type,'manufacturer_id',$id,$manufacturerid);
      
		$product_id=$id; $creation_customer_id=$modification_customer_id=$admin;
		$creation_date=$modification_date=$add_date;
		cw_array2insert ('cw_products_system_info',compact('product_id','creation_customer_id','creation_date',
			'modification_customer_id','modification_date'));
	}

	if ($cwithmeta) $catmeta=cw_query ("Select categoryid as id, 'C' as type, category,
		  meta_description, meta_keywords, title_tag from xcart_categories", $conn);
	else foreach ($cats as $k => $v) {
			extract ($v);
			$catmeta[$k]['title_tag']=trim($category);
			$descr=preg_replace ("'</?[a-z]+.*?>'is"," ",$description);
			$descr=trim(preg_replace("'\s+'", " ", $descr));
			$catmeta[$k]['meta_description']=$descr;
			$mk=preg_replace ("'[^a-z]+'is", " ", $category);
			$catmeta[$k]['meta_keywords']=str_replace (' ',', ',trim($mk));
			$catmeta[$k]['type']='C';
			$catmeta[$k]['id']=$category_id;
	}


	foreach ($catmeta as $v) {
		extract($v);
		cw_set_attr ($type,'domains',$id,0);
		cw_set_attr ($type,'meta_title',$id,$title_tag);
		cw_set_attr ($type,'meta_keywords',$id,$meta_keywords);
		cw_set_attr ($type,'meta_description',$id,$meta_description);
		if (isset($clean_urls[$type]) && isset($clean_urls[$type][$id]))
			cw_set_attr ($type,'clean_url',$id,$clean_urls[$type][$id]);
	}

	if ($cwithmeta) $manmeta=cw_query ("Select manufacturerid as id, 'M' as type, manufacturer,
		  meta_description, meta_keywords, title_tag from xcart_manufacturers", $conn);
	else foreach ($mans as $k => $v) {
			extract ($v);
			$manmeta[$k]['title_tag']=trim($manufacturer);
			$descr=preg_replace ("'</?[a-z]+.*?>'is"," ",$descr);
			$descr=trim(preg_replace("'\s+'", " ", $descr));
			$manmeta[$k]['meta_description']=$descr;
			$mk=preg_replace ("'[^a-z]+'is", " ", $manufacturer);
			$manmeta[$k]['meta_keywords']=str_replace (' ',', ',trim($mk));
			$manmeta[$k]['type']='M';
			$manmeta[$k]['id']=$manufacturer_id;
	}

    if (is_array($manmeta))
	foreach ($manmeta as $v) {
		extract($v);
		cw_set_attr ($type,'domains',$id,0);
		cw_set_attr ($type,'meta_title',$id,$title_tag);
		cw_set_attr ($type,'meta_keywords',$id,$meta_keywords);
		cw_set_attr ($type,'meta_description',$id,$meta_description);
		if (isset($clean_urls[$type]) && isset($clean_urls[$type][$id]))
			cw_set_attr ($type,'clean_url',$id,$clean_urls[$type][$id]);
	}

	// Grab categories, products (Thumbnails & Detailed), and manufacturers related Images
	cw_imgs_copy ($xcart_conf, 'xcart_images_C', 'cw_categories_images_thumb', 'categories_images_thumb');
	cw_imgs_copy ($xcart_conf, 'xcart_images_T', 'cw_products_images_thumb', 'products_images_thumb', 70);
	cw_imgs_copy ($xcart_conf, 'xcart_images_P', 'cw_products_images_det', 'products_images_det');
	cw_imgs_copy ($xcart_conf, 'xcart_images_D', 'cw_products_detailed_images', 'products_detailed_images',800);
	cw_imgs_copy ($xcart_conf, 'xcart_images_M', 'cw_manufacturer_images', 'manufacturer_images', 150);

	// Variants -- Classes
	cw_table_copy_from_xcart_comm ($conn, 'xcart_variants', 'cw_product_variants',
		"variantid as variant_id, productid as product_id, concat(productcode,variantid) as eancode");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_variant_items', 'cw_product_variant_items',
		"optionid as option_id, variantid as variant_id");
	// Variants -- available in stock -- adding to cw_products_warehouses_amount,
	// where we alredy have 'avail' field for variants equal 0 (for prods without variants)
	$var_prod_avail=cw_query("Select variantid as variant_id, productid as product_id, avail from xcart_variants",$conn);
	foreach ($var_prod_avail as $v) cw_array2insert('cw_products_warehouses_amount',$v);

	cw_table_copy_from_xcart_comm ($conn, 'xcart_classes', 'cw_product_options', "classid as product_option_id, class as field,
		classtext as name, productid as product_id, if(avail='Y',1,0) as avail, is_modifier as type");
	cw_table_copy_from_xcart_comm ($conn, 'xcart_class_options', 'cw_product_options_values',
		"classid as product_option_id, option_name as name, if(avail='Y',1,0) as avail,
		    optionid as option_id, if(modifier_type='$',0,1) as modifier_type");
	cw_table_copy_from_xcart_lit ($conn, 'xcart_class_lng', 'cw_product_options_lng',
		"UPPER(code) as code, classid as product_option_id, classtext as name");
	db_query ("TRUNCATE TABLE cw_product_options_values_lng");
	db_query ("TRUNCATE TABLE cw_customers_warehouses"); 

	// Product Links ---
	cw_table_copy_from_xcart_comm ($conn, 'xcart_product_links', 'cw_linked_products',"productid1 as product_id, productid2 as linked_product_id, 1 as active");

	// Featured Products
	cw_table_copy_from_xcart_lit_sn ($conn, 'featured_products', "productid as product_id, 0 as category_id,
		product_order, if(avail='Y',1,0) as avail");

	// Product review, ratings
	cw_table_copy_from_xcart_comm ($conn, 'xcart_product_reviews', 'cw_products_reviews', "productid as product_id");
	cw_table_copy_from_xcart_comm ($conn, 'xcart_product_votes', 'cw_products_votes',
		  "productid as product_id, vote_value/20 as vote_value");

	// Taxes, tax rates
	cw_table_copy_from_xcart_comm_sn ($conn, 'taxes', "taxid as tax_id, if(active='Y',1,0) as active,
		  if(price_includes_tax='Y',1,0) as price_includes_tax, if(display_including_tax='Y',1,0) as display_including_tax");
	cw_table_copy_from_xcart_comm_sn ($conn, 'tax_rates', "rateid as rate_id, taxid as tax_id, zoneid as zone_id,
		  0 as wherehouse_customer_id");
	db_query ("DELETE from cw_languages_alt where name rlike '^tax\_[0-9]+$'");

	cw_import_config ($conn);
	

	// Menu -- Make top categories featured -- and add 8 top categories (with subcategories) to Top Menu
	db_query ("Update cw_categories set featured=1 where parent_id=0 and status=1");
	db_query ("Update cw_categories set tm_active=1 where parent_id!=0");
	$ids=cw_query_first_cell("select group_concat(category_id) as cids from
		(select category_id from cw_categories where parent_id=0 and status=1 order by order_by limit 8) cat");
	db_query ("Update cw_categories set tm_active=1 where category_id in ($ids)");

	cw_cat_tree_building ();
	db_query ("TRUNCATE TABLE cw_categories_subcount");
	cw_recalc_subcat_count(0);
}




// Functions bellow are only for debug purposes
// Should be removed from final version
function cw_compare_tables ($conn, $tab) {
$xc_tab="xcart_$tab"; $cw_tab="cw_$tab"; 
$xc_tab_flds=cw_query_column ("desc $xc_tab",'Field', $conn);
$cw_tab_flds=cw_query_column ("desc $cw_tab",'Field');
$common=implode (',',array_intersect($cw_tab_flds, $xc_tab_flds));
$only_in_xc=implode (',',array_diff($xc_tab_flds,$cw_tab_flds));
$only_in_cw=implode (',',array_diff($cw_tab_flds, $xc_tab_flds));
return "tables (xcart|cw)_$tab comparison result: ".print_r (compact('common','only_in_xc','only_in_cw'),true);
}

function cw_common_tables_names ($conn, $except_of='') {
	$except_of=explode(',',$except_of);
	foreach ($except_of as $k => $v) $except_of[$k]=trim($v);
	$xc_tabs=cw_query_column('show tables',0,$conn);
	$cw_tabs=cw_query_column('show tables');
	foreach ($xc_tabs as $v) {
		$cw_tab='cw_'.preg_replace("'^xcart_(.*)$'i","$1",$v);
		if (in_array($cw_tab,$cw_tabs) && !in_array($cw_tab,$except_of)) $res[$v]=$cw_tab;
	}
	return $res;
}

function cw_common_tables_diff ($conn, $except_of='') {
$comm_tabs=cw_common_tables_names ($conn, $except_of);
$cv=$xc='';
foreach ($comm_tabs as $k => $v) {
$c=cw_query_column ("show create table $v",1);
$x=cw_query_column ("show create table $k",1,$conn);
$cv.=$c[0]."\n\n";
$xc.=$x[0]."\n\n";
}
$h=fopen("./var/ctc_cv","w"); fwrite($h,$cv); fclose($h);
$h=fopen("./var/ctc_xc","w"); fwrite($h,$xc); fclose($h);
exec ("diff -rabEBpd -U 2 ./var/ctc_xc ./var/ctc_cv > ./var/ctc.diff");
}






?>
