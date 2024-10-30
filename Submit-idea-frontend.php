<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_redirect_submission_idea($url){
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . esc_url_raw($url) . '"';
    $string .= '</script>';
    echo $string;
}
function crowdideas_submit_idea_fe() {
add_action( 'init', 'crowdideas_submit_idea_frontend_form' );
function crowdideas_submit_idea_frontend_form() {
   wp_register_style( "crowd_ideas_submit_idea_frontend_form_style", plugins_url('css/submit_idea_frontend_form.css', __FILE__ ));
   wp_enqueue_script("crowd_ideas_submit_idea_frontend_form_style_js", plugins_url('js/submit_idea_frontend_js.js', __FILE__ ));
   wp_enqueue_style( 'crowd_ideas_submit_idea_frontend_form_style' );
} 
crowdideas_submit_idea_frontend_form();

if ( is_user_logged_in() ) {
global $wpdb;
global $current_user;
global $post;
$today_date = date('d M Y');

$rows_settings = $wpdb->get_results($wpdb->prepare("SELECT id, post_ideas from ".$wpdb->prefix."settings_crowdideas", $option_prepare));
foreach ($rows_settings as $row_settings ){ 
	$row_settings_post_ideas = esc_html($row_settings->post_ideas);
}

if ( $row_settings_post_ideas == "N" ) {
		echo "This is feature has been disabled by administrator";
}

if( $row_settings_post_ideas == "Y" ) {
/* ************ Get the Campaign ID ************* */
$segments = explode('/', $_SERVER['REQUEST_URI']);
$segments = array_reverse($segments);

if ( get_option('permalink_structure') ) { 	
	$id = explode("-", $segments[0]);
}
if ( !get_option('permalink_structure') ) { 	
	$id = explode("-", $segments[0]);
	$id = array_reverse(explode("?", $id[0]));
}

$rows = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."campaign Where id = '$id[0]'", $option_prepare));
$show_error = 0; 
$count_array_result = count($rows);
if($count_array_result == 0){echo "This campaign is not exists. <br>"; $show_error = 1;}
if($count_array_result == 1){
foreach ($rows as $row ){ 
if(esc_html($row->status) == 'No') {echo "This campaign is no more exists. <br>"; $show_error = 1; }
//if($row->start_date > $today_date) {echo "This campaign will be starting from ". $row->start_date; $show_error = 1; }
//if($row->end_date < $today_date) {echo "This campaign was expired on ". $row->end_date; $show_error = 1; }
if($show_error == 0){


//print_r($current_user);
require_once('function-idea.php');
$campaign_id = esc_html($row->id);
$title = htmlspecialchars(sanitize_text_field($_POST["title"]));
//$description = $_POST["description"];
$cat_id = sanitize_text_field($_POST["cat_id"]);
$tags = sanitize_text_field($_POST["tags"]);
$describe_problem = sanitize_text_field($_POST["describe_problem"]);
$describe_idea = sanitize_text_field($_POST["describe_idea"]);
$status = sanitize_text_field($_POST["status"]);


/* -------- code for redirecting -------- */
$page_of_campaign_details = get_page_by_title( 'campaign details' ); 
if ( !get_option('permalink_structure') ) { 
$generate_redirect_url = get_site_url() . '/?page_id=' . $page_of_campaign_details->ID . '?'. $segments[0]. '&idea=1' ;
}
if ( get_option('permalink_structure') ) { 
$generate_redirect_url = get_site_url() . '/campaign-details?/' . $segments[0] . '&idea=1';
}

/* -------- code for redirecting (end)-------- */


if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
if ( current_user_can( 'read' ) ){
$error_message = 0;
	
	// --------------------- Custom Image Upload Script -------------------- //
	
	$max_file_size = 2024*2000; // 1000kb
	$valid_exts = array('jpeg', 'jpg', 'png', 'gif');
	// thumbnail sizes
	$sizes = array(100 => 100, 150 => 150, 250 => 250);

	if (isset($_FILES['image'])) { 
	if ($_FILES['image']['size'] != 0) {
		
	  if( $_FILES['image']['size'] < $max_file_size ){
		// get file extension
		$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
		if (in_array($ext, $valid_exts)) {
			$rand = rand(0000, 9999).time();
		  /* resize image */
		  foreach ($sizes as $w => $h) {
			$files[] = crowdideas_resize($w, $h, $rand);
		  }
			
		} 
		if (in_array($ext, $valid_exts) == false) {
		  $msg = 'Unsupported file.<br><br>';
		  $error_message = 1;
		}
	  } else{
		$msg = 'Please upload image smaller than 1000KB';
		$error_message = 1;
	  }
	}
	}
	// --------------------- End Custom Image Upload Script -------------------- //
	echo $msg;
	if($error_message == 0) {
		$wpdb->get_var( $wpdb->prepare($wpdb->insert($wpdb->prefix.'ideas_crowdideas', //table
			array(
				'created_by' => $current_user->id,
				'title' => $title,
				//'description' => $description,
				'campaign_id' => $campaign_id,
				'cat_id' => $cat_id,
				'tags' => $tags,
				'describe_problem' => $describe_problem,
				'describe_idea' => $describe_idea,
				'image' => $rand.'_'.$_FILES['image']['name'],
				'created_date' => date("Y-m-d"),
				'status' => 'Y',
				)), //data
			array('%s','%s') //data format			
		));
		#header("location:$generate_redirect_url");
		#wp_redirect($generate_redirect_url); 
		crowdideas_redirect_submission_idea($generate_redirect_url);
	}
}
}
}
?>

<div class="wrap">
<h4>Submit Idea</h4>
<?php if (isset($message)):?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>" enctype="multipart/form-data" id = "formID">

<table class='wp-list-table widefat fixed idea-wrap'>

<tr><td> <h4 style="margin-bottom:0px;">Title *</h4> </td> </tr>
<tr><td colspan="1"><input type="text" id="title" name="title" class="croud-text" maxlength="200" value="<?php echo stripslashes(sanitize_text_field($title));?>" required="required"/></td></tr>

<tr><td><h4 style="margin-bottom:0px; padding-top:20px;">Idea Category *</h4> </td>  </tr>
<tr>
<?php 	$rows_category = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."category Where status = 'Yes' order by title", $id));?>
<td>
	<select name="cat_id" class="drop-plagin" required="required" id = "cat_id">
		<option value="">Select</option>
	<?php foreach ($rows_category as $row_category){?>
		<option value="<?php echo $row_category->id?>"><?php echo stripslashes(esc_html($row_category->title))?></option>
	<?php }?>
	</select></td></tr>

<tr><th > <h4 style="margin-bottom:0px; padding-top:20px;">Upload Image</h4> </th></tr> 
<tr><td><input type="file" name="image" />
		
</td></tr>

<tr><th><h4 style="margin-bottom:0px; padding-top:20px;">Describe the problem or opportunity *</h4></th></tr>
<tr><td colspan="1" ><textarea name="describe_problem" id="describe_problem" class="croud-text" cols="40" rows="16"><?php echo stripslashes(sanitize_text_field($describe_problem));?></textarea></td></tr>

<tr><th><h4 style="margin-bottom:0px; padding-top:20px;">Describe the idea *</h4></th></tr>
<tr><td colspan="1" ><textarea name="describe_idea" id="describe_idea" class="croud-text" cols="40" rows="16"><?php echo stripslashes(sanitize_text_field($describe_idea));?></textarea></td></tr>

<tr><td> <h4 style="margin-bottom:0px; padding-top:20px;">Tags </h4> </td> </tr>
<tr><td colspan="1"><input type="text" name="tags" class="croud-text" value="<?php echo sanitize_text_field($tags);?>" /></td></tr>

<tr>
	<td><?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
	<input type='button' name="insert" value='Submit Idea' class='button button-primary button-large' Onclick="return validcheckfields();">&nbsp; &nbsp; &nbsp; &nbsp; * = mandatory</td>
</tr>

</table>

</form>
</div>
<?php  }
  }
} // bracket closed for settings set to Yes
}// login to see bracket closed

if ( !is_user_logged_in() ) {
		echo "Please login to submit idea";
	}

}