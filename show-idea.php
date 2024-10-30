<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_show_idea () {
add_action( 'init', 'crowdideas_idea_bkend_script_enqueuer' );
function crowdideas_idea_bkend_script_enqueuer() {
   wp_register_style( "crowd_ideas_single_idea_bkend_list", plugins_url( 'css/single-idea-bkend-list.css', __FILE__ ));
   wp_enqueue_style( 'crowd_ideas_single_idea_bkend_list' );
} 
crowdideas_idea_bkend_script_enqueuer();
?>

<div class="wrap">
<?php

global $wpdb;
$uploads = wp_upload_dir();
$result_campaign        = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."campaign Where id = ". intval($_GET['id']), $option_prepare));
//print_r($result_campaign);

foreach ($result_campaign as $row_campaign ){
	echo '<div><h2>Campaign: '.stripslashes(esc_html($row_campaign->title)). '</h2><div>';
}


$customPagHTML     = "";
$query             = "SELECT * FROM ".$wpdb->prefix."ideas_crowdideas Where campaign_id = ". intval($_GET['id']);
$total_query       = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var($wpdb->prepare(($total_query), $option_prepare));
$items_per_page    = 25;
$page              = intval(isset( $_GET['cpage'] )) ? intval(abs( (int) $_GET['cpage'] )) : 1;
$offset            = ( $page * $items_per_page ) - $items_per_page;
$result            = $wpdb->get_results( $wpdb->prepare($query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}", $option_prepare) );
$totalPage         = ceil($total / $items_per_page);

echo "<h3>Ideas ( $total )</h3>";
echo "<div style=\"float: right; margin-bottom:10px; \"><a href='".admin_url('admin.php?page=campaign_list')."'> << Back to Campaign List</a></div>";
echo "<div class='responsive-table' style=\"\width:100%;\">";
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
	if($row->image == '_') {
		$image_show = '<img src="'.plugins_url( 'img/default.jpg', __FILE__ ).'" width="100" height="100">';
		}
	if($row->image != '_') {
		//echo "<td><img src=".get_site_url()."/wp-content/plugins/crowd-ideas/uploads/ideas/100x100_".$row->image."></td>";
		$image_show = '<img src="' . esc_url( $uploads['baseurl'] . '/crowdideas/ideas/100x100_'.$row->image ) . '" href="#">';
		}
	
	
	$user = get_user_by( 'ID', esc_html($row->created_by) );
	echo "<tr>";
	echo "<td>".$i++."</td>";
	
	if(trim($row->title) == ""){
		echo "<td class='campne-list'><a href='#'> No Title </a></td>";
	}
	
	if(trim($row->title) != ""){
		echo "<td class='campne-list'><a href='#'> ".substr(stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($row->title))), 0, 20). "</a></td>";
	}
	
	if($row->image == '_') { ?>
	<td><img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>" width="100" height="100"></td>
	<?php } ?>
	
	<?php if($row->image != '_') { ?>
	<td><img src="<?php echo esc_url( $uploads['baseurl'] . '/crowdideas/ideas/100x100_'.$row->image );?>"></td>
	<?php } 
	//echo "<td>" .$image_show. "</td>";

    echo "<td><a href='".admin_url('user-edit.php?user_id='.$row->created_by)."'>";
	echo esc_html($user->user_login);
	echo "</a><br>[Created: ";
	echo esc_html($row->created_date)."]";
	if(esc_html($row->modified_date) != '0000-00-00'){ echo "<br>[Last modified: ";
	echo esc_html($row->modified_date)."]";
	echo "</td>"; 
	}	
    echo "<td>". esc_html($row->status). "</td>";		
	echo "<td><a href='".admin_url('admin.php?page=idea_update&id='.$row->id)."'>Edit</a></td>";
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
		echo "<td class='campne-list'><a href='#'> ".substr(stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($row->title))), 0, 20). "</a></td>";
	}
	
	if($row->image == '_') {?>
	<td><img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>" width="100" height="100"></td>
	<?php 
	} 
	if($row->image != '_') {?>
	<td><img src="<?php echo esc_url( $uploads['baseurl'] . '/crowdideas/ideas/100x100_'.$row->image );?>">
	<?php 
	}
    echo "<td><a href='".admin_url('user-edit.php?user_id='.$row->created_by)."'>";
	echo esc_html($user->user_login);
	echo "</a><br>[Created: ";
	echo esc_html($row->created_date)."]";
	if(esc_html($row->modified_date) != '0000-00-00'){ echo "<br>[Last modified: ";
	echo esc_html($row->modified_date)."]";
	echo "</td>"; 
	}	
    echo "<td>". esc_html($row->status). "</td>";		
	echo "<td><a href='".admin_url('admin.php?page=idea_update&id='.$row->id)."'>Edit</a></td>";
	echo "</tr>";}
	echo "</tbody>
	</table></div>";
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