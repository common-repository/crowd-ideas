<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_campaign_update () {
require_once('function.php');
global $wpdb;
$id = intval($_GET["id"]);
$title = sanitize_text_field(htmlspecialchars($_POST["title"]));
$description = sanitize_text_field($_POST["description"]);
//$image = $_POST["image"];
$start_date = sanitize_text_field($_POST["start_date"]);
$end_date = sanitize_text_field($_POST["end_date"]);
$campaign_goals = sanitize_text_field($_POST["campaign_goals"]);
$status = sanitize_text_field($_POST["status"]);

add_action( 'init', 'crowdideas_campaign_update_script_enqueuer' );
function crowdideas_campaign_update_script_enqueuer() {
   wp_enqueue_style("crowd_ideas_campaigns_jquery_update", plugins_url( 'css/jquery-ui.css', __FILE__ ));
   wp_enqueue_script("crowd_ideas_campaigns_update_js", plugins_url( 'js/single-campaign-update.js', __FILE__ ));
   wp_enqueue_script("crowd_ideas_campaigns_create_datepicker_js", plugins_url( 'js/single-campaign-create-datepicker.js', __FILE__ ));
} 
crowdideas_campaign_update_script_enqueuer();


//update
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
	if ( current_user_can( 'administrator' ) ){
	

	// --------------------- Custom Image Upload Script -------------------- //
	
	$max_file_size = 2024*2000; // 1000kb
	$valid_exts = array('jpeg', 'jpg', 'png', 'gif');
	// thumbnail sizes
	$sizes = array(100 => 100, 150 => 150, 250 => 250);

	if (isset($_FILES['image'])) {
		
	  if( $_FILES['image']['size'] < $max_file_size ){
		// get file extension
		$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
		if (in_array($ext, $valid_exts)) {
			$rand = rand(0000, 9999).time();
		  /* resize image */
		  foreach ($sizes as $w => $h) {
			$files[] = crowdideas_resize($w, $h, $rand);
		  }
			
		} else {
		  $msg = 'Unsupported file';
		}
	  } else{
		$msg = 'Please upload image smaller than 1000KB';
	  }
	}
	// --------------------- End Custom Image Upload Script -------------------- //
	
	if (($_FILES['image']['name']!="")) {
		$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.
		'campaign', //table
		array('image' => $rand.'_'.$_FILES['image']['name'], //data
		'updated_at' => current_time( 'mysql' )), //data
		array( 'ID' => $id )), //where
		array('%s'), //data format
		array('%s') //where format
		));
	}
	
	$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.
		'campaign', //table
		array('title' => $title, //data
		'description' => $description, //data
		'start_date' => date("Y-m-d", strtotime($start_date)), //data
		'end_date' => date("Y-m-d", strtotime($end_date)), //data
		'campaign_goals' => $campaign_goals,
		'status' => $status,
		'updated_at' => current_time( 'mysql' )), //data
		array( 'ID' => $id )), //where
		array('%s'), //data format
		array('%s') //where format
	));	
	}
	
}
//delete
else if(isset($_POST['delete'])){
	if ( current_user_can( 'administrator' ) ){	
		$fetch_rates = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas where campaign_id=%s", $id));
		//print_r($fetch_rates); exit;
		foreach ($fetch_rates as $fetch_rate ){
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_rate_crowdideas WHERE id_idea = %s", $fetch_rate->id));
		}
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_crowdideas WHERE campaign_id = %s", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."campaign WHERE id = %s", $id));
	}
}
else{//selecting value to update	
	$schools = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."campaign where id=%s", $id));
	//echo "<pre>"; print_r($schools); echo "</pre>";
	foreach ($schools as $s ){
		$title=esc_html($s->title);
		$description=esc_html($s->description);
		//$image=$s->image;
		$start_date=esc_html($s->start_date);
		$end_date=esc_html($s->end_date);
		$campaign_goals=esc_html($s->campaign_goals);
		$status=esc_html($s->status);
		
	}
}
?>

<div class="wrap">
<h1>Edit Campaign</h1>

<?php if($_POST['delete']){
		if ( current_user_can( 'administrator' ) ){
			$fetch_rates = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas where campaign_id=%s", $id));
			//print_r($fetch_rates); exit;
			foreach ($fetch_rates as $fetch_rate ){
				$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_rate_crowdideas WHERE id_idea = %s", $fetch_rate->id));
			}
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_crowdideas WHERE campaign_id = %s", $id));
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."campaign WHERE id = %s", $id));
		}
	
?>
<div class="updated"><p>Campaign deleted</p></div>
<a href="<?php echo admin_url('admin.php?page=campaign_list')?>">&laquo; Back to Campaign list</a>

<?php } else if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){ ?>
<div class="updated"><p>Campaign updated</p></div>
<a href="<?php echo admin_url('admin.php?page=campaign_list')?>">&laquo; Back to Campaign list</a>

<?php } else {?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id = "formID">
<table class='wp-list-table widefat fixed'>
<tr><th><h1> Title *</h1> </th></tr>
<tr><td><input type="text" id = "title" name="title" required="required" class="croud-text" maxlength="200" value="<?php echo (stripslashes($title));?>"/></td></tr>
<tr><th> <h1>  Description *</h1></th></tr>
<tr><td><textarea id="description" name="description" value="" required="required" cols="40" rows="16" class="croud-text" ><?php echo stripslashes($description);?></textarea></td></tr>

<tr><th><h1>Upload Image (only jpg and png)</h1></th></tr>
<tr><td><input type="file" name="image" value="<?php echo $image;?>"/></td></tr>

<tr><th><h1> Start Date *</h1></th></tr>
<tr><td><input type="text" name="start_date" required="required" class="croud-text" value="<?php echo date("Y-m-d", strtotime($start_date));?>" id="datepicker"/></td></tr>

<tr><th><h1> End Date *</h1> </th></tr>
<tr><td><input type="text" name="end_date" required="required" class="croud-text" value="<?php echo date("Y-m-d", strtotime($end_date));?>" id="datepicker1"/></td></tr>

<tr><th><h1> Goals </h1></th></tr>
<tr><td colspan="1"><textarea name="campaign_goals" class="croud-text" value="" rows="16" cols="40"><?php echo stripslashes($campaign_goals);?></textarea></td> <td></td></tr>
<tr><th>Status *</th></tr>
<tr><td><select name="status" class="drop-plagin">
		<option <?php if ($status == "Yes" ) echo 'selected' ; ?> value="Yes">Yes</option>
		<option <?php if ($status == "No" ) echo 'selected' ; ?> value="No">No</option>
	</select></td><td></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="update" value='Save' class='button button-primary button-large' Onclick="return validcheckfields();"> &nbsp;&nbsp; 
<input type='submit' name="delete" value='Delete' class='button button-primary button-large' onclick="return confirm('Are you sure to delete this Campaign ?')">
&nbsp; &nbsp; &nbsp; &nbsp; * = mandatory

</form>
<?php }?>

</div>
<?php
}