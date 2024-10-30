<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_submit_idea () {
$today_date = date('d M Y');
global $wpdb;
global $current_user;
//print_r($current_user);
//echo $current_user->id;
require_once('function-idea.php');
$campaign_id = sanitize_text_field($_POST["campaign_id"]);
$title = sanitize_text_field(htmlspecialchars($_POST["title"]));
//$description = $_POST["description"];
$cat_id = sanitize_text_field($_POST["cat_id"]);
$tags = sanitize_text_field($_POST["tags"]);
$describe_problem = sanitize_text_field($_POST["describe_problem"]);
$describe_idea = sanitize_text_field($_POST["describe_idea"]);
$status = sanitize_text_field($_POST["status"]);

add_action( 'init', 'crowdideas_idea_backend_add_script_enqueuer' );
function crowdideas_idea_backend_add_script_enqueuer() {
   wp_enqueue_style("crowd_ideas_idea_backend_jquery", plugins_url( 'css/jquery-ui.css', __FILE__ ));
   wp_enqueue_script("crowd_ideas_idea_backend_create_js", plugins_url( 'js/single-idea-backend-create.js', __FILE__ ));
  } 
crowdideas_idea_backend_add_script_enqueuer();


//insert
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
	if ( current_user_can( 'administrator' ) ){
	//print_r($_POST); die();
	global $_FILES;
	
	
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
	

	global $wpdb;
	
	$wpdb->get_var( $wpdb->prepare($wpdb->insert($wpdb->prefix.'ideas_crowdideas', //table
		array(
			'created_by' => $current_user->id,
			'title' => $title,
			'campaign_id' => $campaign_id,
			'cat_id' => $cat_id,
			'tags' => $tags,
			'describe_problem' => $describe_problem,
			'describe_idea' => $describe_idea,
			'image' => $rand.'_'.$_FILES['image']['name'],
			'created_date' => date("Y-m-d"),
			'status' => $status,
			)), //data
		array('%s','%s') //data format			
	));
	$message.="Idea added";
	}
}
?>

<div class="wrap">
<h1>Submit New Idea</h1>
<?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id = "formID">

<table class='wp-list-table widefat fixed'>


<tr><td><h1> Select a Campaign *</h1> </td> <td></td></tr>
<tr>
<?php $rows_campaign = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."campaign order by title", $id)); ?>
<td>
	<select name="campaign_id" class="drop-plagin" required="required">
	<?php foreach ($rows_campaign as $row_campaign){ ?>
		<option value="<?php echo $row_campaign->id?>"><?php if (trim($row_campaign->title) == '') { echo "No Title"; } ?> <?php if (trim($row_campaign->title) != '') {  echo esc_html((stripslashes($row_campaign->title))); } ?></option>
	<?php } ?>
	</select></td></tr>


<tr><td> <h1>Title *</h1> </td> </tr>
<tr><td colspan="1"><input type="text" id = "title" name="title" class="croud-text" maxlength="200" required="required"/></td></tr>

<tr><td><h1>Idea Category *</h1> </td> <td></td></tr>
<tr>
<?php 	$rows_category = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."category order by title", $id)); ?>
<td>
	<select name="cat_id" class="drop-plagin" required="required" id = "cat_id">
		<option value="">Select</option>
	<?php foreach ($rows_category as $row_category){ ?>
		<option value="<?php echo $row_category->id?>"><?php echo sanitize_text_field(stripslashes($row_category->title))?></option>
	<?php } ?>
	</select></td></tr>

<tr><th > <h1> Upload Image </h1> </th></tr> 
<tr><td><input type="file" name="image" />
<input type="hidden" name="id" id="id" value="1" />
</td></tr>

<tr><th><h1>Describe the problem or opportunity *</h1></th></tr>
<tr><td colspan="1" ><textarea name="describe_problem" id = "describe_problem" class="croud-text" cols="40" rows="16"></textarea></td><td></td></tr>

<tr><th><h1>Describe the idea *</h1></th></tr>
<tr><td colspan="1" ><textarea name="describe_idea" id = "describe_idea" class="croud-text" cols="40" rows="16"></textarea></td><td></td></tr>

<tr><td> <h1>Tags </h1> </td> </tr>
<tr><td colspan="1"><input type="text" name="tags" class="croud-text" /></td></tr>

<tr><td><h1>Status *</h1> </td> <td></td></tr>
<tr>
<td>
	<select name="status" class="drop-plagin">
		<option value="Y">Yes</option>
		<option value="N">No</option>
	</select></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="insert" value='Submit Idea' class='button button-primary button-large' Onclick="return validcheckfields();">&nbsp; &nbsp; &nbsp; &nbsp; * = mandatory
</form>
</div>
<?php
}
