<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_category_list () {
	
	add_action( 'init', 'crowdideas_category_list_script_enqueuer' );
	function crowdideas_category_list_script_enqueuer() {
	   wp_register_style( "crowd_ideas_category_list_css", plugins_url( 'css/category_list.css', __FILE__ ));
	   wp_enqueue_style( 'crowd_ideas_category_list_css' );
	} 
	crowdideas_category_list_script_enqueuer();
?>

<div class="wrap">
<h1>Category <a class="page-title-action" href="<?php echo admin_url('admin.php?page=category_create'); ?>">Add New</a></h1>
<?php
global $wpdb;

$result = $wpdb->get_results($wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "category", $option_prepare)); 
$query             = "SELECT * FROM " . $wpdb->prefix . "category";
$total_query       = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total             = $wpdb->get_var( $wpdb->prepare(($total_query ), $option_prepare));
//echo "<pre>"; print_r($result); echo "</pre>";
echo "<div class='responsive-table'>";
echo "<table class='wp-list-table1 widefat striped'>";
echo "<thead>
		<tr>
			<th class=''>ID</th>
			<th class='' scope='col'>Name</th>
			<th scope='col' class='' id='author'>Status</th>
			<th scope='col' class='' id='author'>Edit</th>
		</tr>
	  </thead>";
		echo '<div style="float: right;">'. $total . " items </div>";
		echo "<tbody id='the-list'>";
		$i = 1;
		foreach ($result as $row ){
		echo "<tr>";
		echo "<td>".$i++."</td>";
		echo "<td>".esc_html(substr(stripslashes($row->title), 0, 20))."</td>";
		echo "<td>".esc_html($row->status)."</td>";	
		echo "<td><a href='".admin_url('admin.php?page=category_update&id='.$row->id)."'>Edit</a></td>";	
		echo "</tr>";}
		echo "</tbody>
</table> </div>";
?>
</div>
<?php
}