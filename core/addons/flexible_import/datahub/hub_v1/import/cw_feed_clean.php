<?php
require_once('header.php');

//$tst_ext = "_old";
$tst_ext = "";
ksort($feed_tables);
if(!empty($_POST['posted'])) {
    foreach ($_POST as $k => $v) {
        if (preg_match("/include::/i", $k)) {
            $temp = explode('::', $k);	
            $post_tables[$temp[1]] = $temp[1];			    		   
        }
    }
    if(isset($post_tables) && count($post_tables) > 0) {
        mysql_query("DELETE FROM cw_import_feed$tst_ext WHERE feed_short_name IN ('".implode("', '", array_keys($post_tables))."')");
    } else {
        echo '<br />no feeds selected<br />';
    }

    $_POST = array();
}

?>
<br><br>
<form action="<?=$_SERVER['PHP_SELF']?>" method="POST" style="margin:0px;" name="form1">
<input type="button" name="checkall" value="Check ALL" onClick="masterCheck(this, 'Check ALL', 'Uncheck ALL')" />
<br><table border="1">
<tr>
	<td>Feed Name</td>
	<td>Items count</td>	
        <td>Clean items</td>
</tr>
<?php
$result = mysql_query("select feed_short_name as name, count(*) as count from cw_import_feed$tst_ext group by feed_short_name order by feed_short_name asc");
$cw_feeds = array();
while ($row = mysql_fetch_assoc($result)) {
$cw_feeds[] = array('name'=>$row['name'],'count'=>$row['count']);
}
if ($cw_feeds) {
    foreach ($cw_feeds as $k => $v) {
        echo "<tr><td>$v[name]</td><td>$v[count]</td><td><input type=\"checkbox\" name=\"include::$v[name]\" value=\"1\" /></td>";
    }
} else {
    echo "<td colspan=\"3\"><b>cw_import_feed table is empty</b></td>";    
}
?>
<tr>
	<td colspan="3"><input type="submit" name="submit" value="Submit" /></td>
</tr>
</table>
<input type="hidden" name="posted" value="1" />
</form>
