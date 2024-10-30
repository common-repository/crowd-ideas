<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_show_categories_frontend(){ 
global $wpdb;
$uploads = wp_upload_dir(); 

add_action( 'init', 'crowdideas_category_frontend_show_script_enqueuer' );
function crowdideas_category_frontend_show_script_enqueuer() {
   wp_register_script( "crowdideas_cat_frontend_show_details_js", plugins_url( 'js/show_idea_as_list.js', __FILE__ ));
   wp_enqueue_script( 'crowdideas_cat_frontend_show_details_js' );
} 
crowdideas_category_frontend_show_script_enqueuer();

if ( is_user_logged_in() ) {
//echo "ID = ". $_SERVER['REQUEST_URI'];
$segments = explode('/', $_SERVER['REQUEST_URI']);
//echo get_site_url();
$segments = array_reverse($segments);

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
$txtidea = "";
$error_msg = 0;

$rows_categories = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."category Where id = '$id[0]'", $option_prepare));
foreach ($rows_categories as $rows_category ){
	$txtidea.= "<div class=\"listing-element\">";
	$txtidea.= "<p><b>".stripslashes(esc_html($rows_category->title)). "</b></p>";
	$txtidea.= "</div>";	
	
}

if(esc_html($rows_category->status) == "No"){ 
	$txtidea.= "<div class=\"listing-element\">";
	$txtidea.= "<p><b>This category is currently disabled by the administrator. Please come back later.</b></p>";
	$txtidea.= "</div>";
	$error_msg = "1";
	echo $txtidea;
	}

if($error_msg==0){ 
/* ************************* Show Ideas as list *********************** */
// ***********************************************************************

$table_name = $wpdb->prefix . 'ideas_crowdideas';
$count_query = "select count(*) from $table_name Where cat_id = '$id[0]' AND status = 'Y'";
$num = $wpdb->get_var($wpdb->prepare($count_query, $option_prepare));

if($num == 0) {
	$noideasubmitted = "<div class=\"Main Class\">No Idea submitted</div><br>";
}
if($num >0) {
?>
<form name="rank" id="rank" method = "post" action="">
	<select class="rank-dropdown" name="rankby">
		<option value="">Select</option>
		<option value="Desc" <?php if (sanitize_text_field($_POST['rankby'])=="Desc"){echo "selected"; }?>>Show by ratings ascending</option>
		<option value="Asc" <?php if (sanitize_text_field($_POST['rankby'])=="Asc"){echo "selected"; }?>>Show by ratings descending </option>
	</select>
	<input type="hidden" name="rankhidden" Value="rankhidden">
	<input type="submit" name="ranksubmit" Value="Rank" onclick="return validrank();";>
</form>
<?php } // ideas are greater than 0 bracket closed ?>
<?php
if(sanitize_text_field($_POST['rankhidden']) == "rankhidden"){
	$rankby = sanitize_text_field($_POST['rankby']);
	$order_by = " Order By total_rates $rankby, campaign_id ASC";
}

if(sanitize_text_field($_POST['rankhidden']) != "rankhidden"){
	$order_by = " Order By campaign_id asc, id ASC";
}
$rows_ideas = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas Where cat_id = '$id[0]' AND status = 'Y' $order_by", $option_prepare));

$page_of_show_idea_as_list = get_page_by_title( 'Idea Details' );	
$txtidea.= "<div class=\"Main1 Class\">";

foreach ($rows_ideas as $idea ){


if ( get_option('permalink_structure') ) {	
	$replace_idea_title = str_replace(' ', '-', stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($idea->title))));
	$replace_idea_title = str_replace('/', '', $replace_idea_title);
	$idea_link_to_details = get_site_url().'/idea-details?/'.esc_html($idea->id).'-'.$replace_idea_title;
}

if ( !get_option('permalink_structure') ) {	
	$replace_idea_title = str_replace(' ', '-', stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($idea->title))));
	$replace_idea_title = str_replace('/', '', $replace_idea_title);
	$idea_link_to_details = get_site_url().'?page_id='.$page_of_show_idea_as_list->ID.'/'.esc_html($idea->id).'-'.$replace_idea_title;
}		



$txtidea.= "<div class=\"comment-box-div\"> <div class=\"comment-author vcard\">
<a href=\"$idea_link_to_details\">";
if($idea->image == '_') {$txtidea.= '<img class="co-img" src="'.plugins_url( 'img/default.jpg', __FILE__ ).'" width="100" height="100"></td>';}
if($idea->image != '_') {$txtidea.= "<img class=\"co-img\" src='".esc_url( $uploads['baseurl']."/crowdideas/ideas/250x250_".$idea->image)."'></td>";}
$txtidea.= "</a>
<p class=\"title-p\" >
<a class=\"title-anchar\" href=\"$idea_link_to_details\"><b>".substr(stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($idea->title))), 0, 20)."</b></a></p>
</div>";
        

$user = get_user_by( 'ID', esc_html($idea->created_by) );
$txtidea.= "<div class=\"listing-element\">";
$txtidea.= "<p class=\"author\">".$user->user_login. "</p>";
$txtidea.= "<p class=\"date-time\">".date("l F j Y", strtotime(esc_html($idea->created_date))). "</p>";
$txtidea.= "<p class=\"rating-satr\"> <div class=\"rating-active\">";
$txtidea.= '<div class="box-result-cnt"><div class="rate-result-cnt">
                <div class="rate-bg" style="width:'.esc_html($idea->total_rates).'%"></div>
                <div class="rate-stars"></div>
</div></div>
</div>
<div class="rating"></div> ';
$txtidea.= "<a class=\"title-anchar\" href=\"$idea_link_to_details\"><p class=\"more\">READ MORE</p> </a>";
$txtidea.= "</div>";
$txtidea.= "</div>";		


}
$txtidea.= "</div>"; // main div end

echo $txtidea;
echo $noideasubmitted;
echo crowdideas_show_idea_category();

} // 	if user logged in

if ( !is_user_logged_in() ) {
		echo "Please login to see the ideas";
	} // if not logged in
	
} // campaign details function end
}