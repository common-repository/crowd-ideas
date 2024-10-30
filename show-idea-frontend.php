<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_show_idea_self_submitted () {
	
add_action( 'init', 'crowdideas_show_idea_self_submitted_page' );
function crowdideas_show_idea_self_submitted_page() {
  wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
   wp_register_style( "crowdideas_show_idea_self_submitted_style", plugins_url( 'css/crowdideas_show_idea_self_submitted_style.css', __FILE__ ));
   wp_enqueue_style( 'crowdideas_show_idea_self_submitted_style' );
} 
crowdideas_show_idea_self_submitted_page();
?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
<div class="wrap">

<?php
global $wpdb;
global $current_user;
/*
$result_campaign        = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."campaign Where id = ". intval($_GET['id']);


foreach ($result_campaign as $row_campaign ){
	echo '<div><h2>Campaign: '.stripslashes($row_campaign->title). '</h2><div>';
}
*/

$customPagHTML     = "";
$query             = "SELECT * FROM ".$wpdb->prefix."ideas_crowdideas Where created_by = ".$current_user->id;
$total_query       = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var( $wpdb->prepare($total_query, $option_prepare));
$items_per_page    = 25;
$page              = intval(isset( $_GET['cpage'] )) ? intval(abs( (int) $_GET['cpage'] )) : 1;
$offset            = ( $page * $items_per_page ) - $items_per_page;
$result            = $wpdb->get_results( $wpdb->prepare($query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}", $option_prepare) );
$totalPage         = ceil($total / $items_per_page);
$uploads = wp_upload_dir();

echo "<h3>You have submitted total $total ideas till date</h3>";
echo "<div class='responsive-table'>";
echo "<table class='wp-list-table1 widefat striped'>";
echo "<thead>
		<tr>
			<th class='' scope='col'>ID</th>
			<th class='' id='title' scope='col'>Title</th>
			<th>Image</th>
			<th>Author</th>
			<th>Status</th>
			<th>Edit</th>
		</tr>
	  </thead>
	  <tbody id='the-list'>";

if($totalPage == 1){ 
$i = 1;
foreach ($result as $row ){ 
	$user = get_user_by( 'ID', esc_html($row->created_by) );
	echo "<tr>";
	echo "<td>".$i++."</td>";
	
	if(trim($row->title) == ""){
		echo "<td class='campne-list'><a href='#'> No Title </a></td>";
	}
	
	if(trim($row->title) != ""){
		echo "<td class='campne-list'><a href='#'> ".substr(wp_strip_all_tags(stripslashes(esc_html($row->title))), 0, 20). "</a></td>";		
	}
	
	//echo "<td><img src=".get_site_url()."/wp-content/plugins/crowd-ideas/uploads/ideas/100x100_".$row->image."></td>";
	if(esc_html($row->image) == '_') {echo "<td><img src=".plugins_url( 'img/default.jpg', __FILE__ )." width=\"100\" height=\"100\"></td>";}
	if(esc_html($row->image) != '_') {echo "<td><img class=\"co-img\" src='".$uploads['baseurl']."/crowdideas/ideas/100x100_".esc_html($row->image)."'></td>";}
	
    echo "<td>$user->user_login</td>";	
    echo "<td>$row->status</td>";		
	echo "<td><a href='".admin_url('admin.php?page=idea_update_frontend&id='.$row->id)."'>Edit</a></td>";
	echo "</tr>";}
	echo "</tbody>
	</table></div>";
	
}

if($totalPage > 1){
if($page == 1) {$i = 1;	}
if($page > 1) {$i = (($page-1)*$items_per_page)+($i+1);	}
	foreach ($result as $row ){
	$user = get_user_by( 'ID', $row->created_by );
	echo "<tr>";
	echo "<td>".$i++."</td>";
	
	if(trim($row->title) == ""){
		echo "<td class='campne-list'><a href='#'> No Title </a></td>";
	}
	
	if(trim($row->title) != ""){
		echo "<td class='campne-list'><a href='#'> ".substr(wp_strip_all_tags(stripslashes(esc_html($row->title))), 0, 20). "</a></td>";	
	}
	
	//echo "<td><img src=".get_site_url()."/wp-content/plugins/crowd-ideas/uploads/ideas/100x100_".$row->image."></td>";
	if(esc_html($row->image) == '_') {echo "<td><img src=".plugins_url( 'img/default.jpg', __FILE__ )." width=\"100\" height=\"100\"></td>";}
	if(esc_html($row->image) != '_') {echo "<td><img class=\"co-img\" src='".$uploads['baseurl']."/crowdideas/ideas/100x100_".esc_html($row->image)."'></td>";}
    echo "<td>$user->user_login</td>";	
    echo "<td>$row->status</td>";		
	echo "<td><a href='".admin_url('admin.php?page=idea_update_frontend&id='.$row->id)."'>Edit</a></td>";
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