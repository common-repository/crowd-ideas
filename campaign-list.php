<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_campaign_list () {
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
<?php
add_action( 'init', 'crowdideas_ci_script_enqueuer' );
function crowdideas_ci_script_enqueuer() {
   wp_register_style( "crowd_ideas_single_campaign_list", plugins_url( 'css/single-campaign-list.css', __FILE__ ));
   wp_enqueue_style( 'crowd_ideas_single_campaign_list' );
} 
crowdideas_ci_script_enqueuer();
?>


<div class="wrap">
<h1>Campaign <a class="page-title-action" href="<?php echo admin_url('admin.php?page=campaign_create'); ?>">Add New</a></h1>
<?php

global $wpdb;
$today = strtotime(date('d M Y'));
$today_mod = date('Y-m-d');

/*$rows = $wpdb->get_results("SELECT * from wp_campaign order by id desc ");
 echo "<table class='wp-list-table widefat fixed striped pages'>";
echo "<thead>
		<tr>
			<th class='manage-column column-title column-primary' scope='col'>ID</th>
			<th class='manage-column column-title column-primary' id='title' scope='col'>Title</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Status</th>
			<th>Publish</th>
			<th>Edit</th>
		</tr>
	  </thead>
	  <tbody id='the-list'>";
$i = 1;
foreach ($rows as $row ){
	echo "<tr>";
	echo "<td>".$i++."</td>";
	echo "<td class='campne-list'><a href='#'> $row->title </a></td>";	
	//echo "<td>$row->description</td>";	
	echo "<td>$row->start_date</td>";	
	echo "<td>$row->end_date</td>";	
	if($row->end_date<$today){
		echo "<td>Running</td>";
	}else{
		echo "<td>Expired</td>";
	}
    echo "<td>$row->status</td>";		
	echo "<td><a href='".admin_url('admin.php?page=campaign_update&id='.$row->id)."'>Edit</a></td>";
	echo "</tr>";}
echo "</tbody>
</table>";
*/
//echo "<table class='wp-list-table widefat fixed striped pages'>";
echo "<div class='responsive-table'>";
echo "<table class='wp-list-table1 widefat striped  '>";
echo "<thead>
		<tr>
			<th class='' scope='col'>ID</th>
			<th class='' id='title' scope='col'>Title</th>
			<th>Image</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Status</th>
			<th>Publish</th>
			<th>Edit</th>
		</tr>
	  </thead>
	  <tbody id='the-list'>";

$customPagHTML     = "";
$query             = "SELECT * FROM ".$wpdb->prefix."campaign";
$total_query       = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var($wpdb->prepare(($total_query), $option_prepare));
$items_per_page    = 25;
$page              = intval(isset( $_GET['cpage'] )) ? intval(abs( (int) $_GET['cpage'] )) : 1;
$offset            = ( $page * $items_per_page ) - $items_per_page;
$result            = $wpdb->get_results( $wpdb->prepare($query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}", $option_prepare) );
$totalPage         = ceil($total / $items_per_page);
$uploads = wp_upload_dir();
echo '<div style="float: right;">'.$total . " items" . '</div>';
if($totalPage == 1){
$i = 1;
foreach ($result as $row ){
	echo "<tr>";
	echo "<td>".$i++."</td>";
	
	if(trim($row->title) == ""){
	echo "<td class='campne-list'><a href='".admin_url('admin.php?page=show_idea&id='.$row->id)."'>No Title</a></td>";
	}
	if(trim($row->title) != ""){
	echo "<td class='campne-list'><a href='".admin_url('admin.php?page=show_idea&id='.$row->id)."'> ".substr(stripslashes(esc_html($row->title)), 0, 20). "</a></td>";
	}
	
	if($row->image == '_') { ?>
	
	<td><img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>" width="100" height="100"></td>
	<?php } ?>
	
	<?php if($row->image != '_') { ?>
	
	<td><img src="<?php echo esc_url( $uploads['baseurl']."/crowdideas/campaigns/100x100_".$row->image);?>"></td>
	<?php } 
	//echo "<td>$row->description</td>";	
	echo "<td>".esc_html($row->start_date)."</td>";	
	echo "<td>".esc_html($row->end_date)."</td>";
	
	/* if($row->end_date<$today){
		echo "<td><font color=\"red\">Expired</font></td>";
	}elseif{
			if($row->start_date>$today){
				echo "<td>Coming</td>";
		}	
		else echo "<td>Running</td>";
	} */
	$start = strtotime(esc_html($row->start_date));
	$end = strtotime(esc_html($row->end_date));
	if(esc_html($row->end_date)<$today_mod){
		echo "<td><font color=\"red\">Expired</font></td>";
	}else{
			if(esc_html($row->start_date)>$today_mod){
				echo "<td>Coming</td>";
		}	
		else echo "<td>Running</td>";
	} 
    echo "<td>".esc_html($row->status)."</td>";		
	echo "<td><a href='".admin_url('admin.php?page=campaign_update&id='.$row->id)."'>Edit</a></td>";
	echo "</tr>";}
	echo "</tbody>
	</table> </div>";
	
}

if($totalPage > 1){
if($page == 1) {$i = 1;	}
if($page > 1) {$i = (($page-1)*$items_per_page)+($i+1);	}
	foreach ($result as $row ){
	echo "<tr>";
	echo "<td>".$i++."</td>";
	
	if(trim($row->title) == ""){
	echo "<td class='campne-list'><a href='".admin_url('admin.php?page=show_idea&id='.$row->id)."'>No Title</a></td>";
	}
	if(trim($row->title) != ""){
	echo "<td class='campne-list'><a href='".admin_url('admin.php?page=show_idea&id='.$row->id)."'> ".substr(stripslashes(esc_html($row->title)), 0, 20). "</a></td>";
	}
	
	if($row->image == '_') { ?>
	<td><img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>" width="100" height="100"></td>
	<?php } ?>
	
	<?php if($row->image != '_') { ?>
	<td><img src="<?php echo esc_url( $uploads['baseurl']."/crowdideas/campaigns/100x100_".$row->image);?>"></td>
	<?php } 
	
	echo "<td>".esc_html($row->start_date)."</td>";	
	echo "<td>".esc_html($row->end_date)."</td>";	
	if(esc_html($row->end_date)<$today_mod){
		echo "<td><font color=\"red\">Expired</font></td>";
	}else{
			if(esc_html($row->start_date)>$today_mod){
				echo "<td>Coming</td>";
		}	
		else echo "<td>Running</td>";
	}
    echo "<td>".esc_html($row->status)."</td>";		
	echo "<td><a href='".admin_url('admin.php?page=campaign_update&id='.$row->id)."'>Edit</a></td>";
	echo "</tr>";}
	echo "</tbody>
	</table> </div>";
	echo "<br><br>";
	
$customPagHTML     =  '<div><span>Page '.$page.' of '.$totalPage.'</span>&nbsp;&nbsp;'.paginate_links( array(
'base' => add_query_arg( 'cpage', '%#%' ),
'format' => '',
'prev_text' => __('&laquo;'),
'next_text' => __('&raquo;'),
'total' => $totalPage,
'current' => $page
)).'</div>';
}

echo $customPagHTML;


?>
</div>
<?php
}