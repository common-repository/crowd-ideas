<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_idea_show_details() {
global $wpdb;
global $current_user;
$uploads = wp_upload_dir(); 

if ( is_user_logged_in() ) {
	
add_action( 'init', 'crowdideas_idea_show_details_script_enqueuer' );
function crowdideas_idea_show_details_script_enqueuer() {
   wp_register_script( "crowdideas_idea_show_details_js", plugins_url( 'js/crowdideas_idea_show_details_js.js', __FILE__ ));
   wp_enqueue_script( 'crowdideas_idea_show_details_js' );
} 
crowdideas_idea_show_details_script_enqueuer();

$rows_settings = $wpdb->get_results($wpdb->prepare("SELECT id, select_ideas from ".$wpdb->prefix."settings_crowdideas", $option_prepare));
foreach ($rows_settings as $row_settings ){ 
	$row_settings_edit_ideas = esc_html($row_settings->select_ideas);
}

if ( $row_settings_edit_ideas == "N" ) {
		echo "This is feature has been disabled by administrator";
}

if( $row_settings_edit_ideas == "Y" ) {


//echo "ID = ". $_SERVER['REQUEST_URI'];
$segments = explode('/', $_SERVER['REQUEST_URI']);
//echo get_site_url();

$segments = array_reverse($segments);
#print_r($segments);
if ( get_option('permalink_structure') ) { 	
//echo $segments[3];
$id = explode("-", $segments[0]);
//print_r($id);
}
if ( !get_option('permalink_structure') ) { 	
$id = explode("-", $segments[0]);
$id = array_reverse(explode("?", $id[0]));
//print_r($id);
}

/* --------------------- Fetch the rating ------------------- */
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

#$query = mysql_query("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$id[0]'"); 
$query_wp_results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$id[0]'", $option_prepare));
#print_r($query_wp_results);

foreach($query_wp_results as $query_wp_result){
	
	$rate_db[] = $query_wp_result;
	$sum_rates[] = esc_html($query_wp_result->rate);

}

/*
while($data = mysql_fetch_assoc($query)){ echo "Okay";
	print_r($data);
	$rate_db[] = $data;
	$sum_rates[] = $data['rate'];
}
*/


if(@count($rate_db)){ 
	$rate_times = count($rate_db);
	$sum_rates = array_sum($sum_rates);
	$rate_value = $sum_rates/$rate_times;
	$rate_bg = (($rate_value)/5)*100;
}else{
	$rate_times = 0;
	$rate_value = 0;
	$rate_bg = 0;
}


/* --------------------- Fetch the rating ------------------- */
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	
	
$today_date = date('d M Y');
$rows = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas Where id = '$id[0]' And status = 'Y'", $option_prepare));
$show_error = 0; 

$count_array_result = count($rows);
if($count_array_result == 0){
		#echo "This idea is not exists. <br>"; $show_error = 1;
	}

foreach ($rows as $row ){
if(esc_html($row->status) == 'No') {echo "This idea is no more exists. <br>"; $show_error = 1; }
if($show_error == 0){
global $current_user;
global $post;
$pagename = $post->post_name;
$page_of_submit_idea = get_page_by_title( 'Submit Idea' );
$user = get_user_by( 'ID', esc_html($row->created_by) );


?>
<div class="box-result-cnt"><div class="rate-result-cnt right">
                <div class="rate-bg" style="width:<?php echo $rate_bg; ?>%"></div>
                <div class="rate-stars"></div>
</div></div>
<?php


/*************** Fetching Category **************** */
$page_of_show_idea_as_list = get_page_by_title( 'Categories' );

$select_idea_categories = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."category Where id = ".$row->cat_id, $option_prepare));
foreach ($select_idea_categories as $select_idea_category){
	$select_idea_category_title = esc_html($select_idea_category->title);
}
	// ============ Generate Category link ============== //
	// ****************************************************//
	if ( get_option('permalink_structure') ) {	
	$replace_cat_title = str_replace(' ', '-', stripslashes(esc_html($select_idea_category->title)));
	$cat_link_to_details = get_site_url().'/categories?/'.esc_html($select_idea_category->id).'-'.$replace_cat_title;
	}

	if ( !get_option('permalink_structure') ) {	
		$replace_cat_title = str_replace(' ', '-', stripslashes(esc_html($select_idea_category->title)));
		$cat_link_to_details = get_site_url().'?page_id='.$page_of_show_idea_as_list->ID.'/'.esc_html($select_idea_category->id).'-'.$replace_cat_title;
	}
	// ============ Generate Category link ============== //
	// ****************************************************//

$txt.= "<div>
        <h2>".nl2br(stripslashes(esc_html($row->title)))."</h2>";
$txt.= "<div class=\"post-image\">";	   

		
		
		if(esc_html($row->image) != '_') { 
$txt.=	"<img src='".esc_url( $uploads['baseurl']."/crowdideas/ideas/250x250_". $row->image)."'>";
		//"<img src=".get_site_url()."/wp-content/plugins/crowd-ideas/uploads/ideas/250x250_".$row->image.">";
		}
		
		if(esc_html($row->image) == '_') { 
$txt.=	'<img src="'.plugins_url( 'img/default.jpg', __FILE__ ).'"width="300" height="300">';		
		}
		$txt.= "</div>

		<div>
		
		
		
		<br>
		
		<div><h3>Describe the problem or opportunity</h3></div>
		<p style=\"text-align:justify\">".nl2br(wp_strip_all_tags(stripslashes($row->describe_problem)))."</p>
		<div><h3>Describe the idea</h3></div>
		<p style=\"text-align:justify\">".nl2br(wp_strip_all_tags(stripslashes($row->describe_idea)))."</p>
		<p style=\"text-align:justify\">".nl2br(wp_strip_all_tags(stripslashes($row->tags)))."</p>
		<p style=\"text-align:justify\">Category: <a href=$cat_link_to_details>".nl2br((stripslashes($select_idea_category_title)))."</a></p>
		<p style=\"text-align:justify\">By $user->user_login</p>
		</div>";
$txt.= 	"<div style=\"clear:both\"></div>
		</div>
		<div style=\"clear:both\"></div><br>";
		
	}
echo $txt;

$rows_settings_rate = $wpdb->get_results($wpdb->prepare("SELECT id, rate_ideas from ".$wpdb->prefix."settings_crowdideas", $option_prepare));
foreach ($rows_settings_rate as $row_settings_rate ){ 
	$row_settings_rate_ideas = esc_html($row_settings_rate->rate_ideas);
}

if ( $row_settings_rate_ideas == "N" ) {
		echo "<div>Rating has been disabled by administrator</div>";
}

if( $row_settings_rate_ideas == "Y" ) {

?>	


    <div class="tuto-cnt">Rate this idea:<br>
	<div id="normalp" style="display: none;"><?php echo $id[0];?></div>
	<div id="normaluserid" style="display: none;"><?php echo $current_user->id;?></div>
	<?php $campaign_id_from_page = get_page_by_title( 'Campaign' ); ?>
	<?php 
		if ( get_option('permalink_structure') ) { 
			$destination_url_ajax = get_site_url().'/campaign/?my_user_vote=1';
		}
		if ( !get_option('permalink_structure') ) { 
			$destination_url_ajax = get_site_url().'/?page_id='.$campaign_id_from_page->ID.'&my_user_vote=1';
		}
		
	?>
	<div id="destinationurl" style="display: none;"><?php echo $destination_url_ajax;?></div>
	
        <div class="rate-ex1-cnt">
            <div id="1" class="rate-btn-1 rate-btn" onclick="return clickfunction();"></div>
            <div id="2" class="rate-btn-2 rate-btn" onclick="return clickfunction();"></div>
            <div id="3" class="rate-btn-3 rate-btn" onclick="return clickfunction();"></div>
            <div id="4" class="rate-btn-4 rate-btn" onclick="return clickfunction();"></div>
            <div id="5" class="rate-btn-5 rate-btn" onclick="return clickfunction();"></div>
        </div>
		
		<div id="successfullrate" style="display:none;">
		Thanks for your rating.
		</div>
		
 </div><!-- /tuto-cnt -->
	<?//=$_SERVER['PHP_SELF']?>

<?php
} // bracket closed for rate ideas settings set to Yes
} // bracket closed for select ideas settings set to Yes
}
}// only logged in users
}
?>