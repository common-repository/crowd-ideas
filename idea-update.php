<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_idea_update() {
global $wpdb;
global $current_user;
$today = date('d M Y');
//print_r($current_user);
//echo $current_user->id;
require_once('function-idea.php');
$id = intval($_GET["id"]);
$campaign_id = sanitize_text_field($_POST["campaign_id"]);
$title = sanitize_text_field(htmlspecialchars($_POST["title"]));
//$description = $_POST["description"];
$cat_id = sanitize_text_field($_POST["cat_id"]);
$tags = sanitize_text_field($_POST["tags"]);
$describe_problem = sanitize_text_field($_POST["describe_problem"]);
$describe_idea = sanitize_text_field($_POST["describe_idea"]);
$status = sanitize_text_field($_POST["status"]);

add_action( 'init', 'crowdideas_idea_backend_update_script_enqueuer' );
function crowdideas_idea_backend_update_script_enqueuer() {
   wp_enqueue_script("crowd_ideas_idea_bkend_update_js", plugins_url( 'js/single-idea_update_bkend-update.js', __FILE__ ));
   wp_enqueue_style("crowd_ideas_campaigns_jquery", plugins_url( 'css/jquery-ui.css', __FILE__ ));
  } 
crowdideas_idea_backend_update_script_enqueuer();

//update
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
	if ( current_user_can( 'administrator' ) ){

	
	// --------------------- Custom Image Upload Script -------------------- //
	
	$max_file_size = 2024*2000; // 1000kb
	$valid_exts = array('jpeg', 'jpg', 'png', 'gif');
	// thumbnail sizes
	$sizes = array(100 => 100, 150 => 150, 250 => 250);

	if (($_FILES['image']['name']!="")) {
		
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
		  $error_message = 1;
		}
	  } else{
		$msg = 'Please upload image smaller than 1000KB';
		$error_message = 1;
	  }
	}
	// --------------------- End Custom Image Upload Script -------------------- //
	
	echo $msg;
	if($error_message == 0) {
		if (($_FILES['image']['name']!="")) {
			$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.
			'ideas_crowdideas', //table
			array('image' => $rand.'_'.$_FILES['image']['name'], //data
			'modified_date' => date("Y-m-d")),
			array( 'ID' => $id )), //where
			array('%s'), //data format
			array('%s') //where format
			));
		}
	}
	$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.'ideas_crowdideas', //table
		array(
			'modified_by' => $current_user->id,
			'title' => $title,
			'campaign_id' => $campaign_id,
			'cat_id' => $cat_id,
			'tags' => $tags,
			'describe_problem' => $describe_problem,
			'describe_idea' => $describe_idea,
			'modified_date' => date("Y-m-d"),
			'status' => $status,
			), //data
		array( 'ID' => $id )), //where
		array('%s'), //data format
		array('%s') //where format	
	));
	
	
	//$message.="Idea modified";
	}
}
//delete
else if(($_POST['delete'])){ 
	if ( current_user_can( 'administrator' ) ){
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_rate_crowdideas WHERE id_idea = %s", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_crowdideas WHERE id = %s", $id));
	}
}
else{//selecting value to update	
	$ideas = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas where id=%s", $id));
	foreach ($ideas as $idea ){
		$title=esc_html($idea->title);
		$image=esc_html($idea->image);
		$campaign_id=esc_html($idea->campaign_id);
		$cat_id=esc_html($idea->cat_id);
		$describe_problem=esc_html($idea->describe_problem);
		$describe_idea=esc_html($idea->describe_idea);
		$tags=esc_html($idea->tags);
		$status=esc_html($idea->status);
	
		
	}
}
?>

<div class="wrap">
<h1>Edit Idea</h1>
<?php if($_POST['delete']){ 
	if ( current_user_can( 'administrator' ) ){
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_rate_crowdideas WHERE id_idea = %s", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."ideas_crowdideas WHERE id = %s", $id));
	}
?>
<div class="updated"><p>Idea deleted</p></div>
<a href="<?php echo admin_url('admin.php?page=campaign_list')?>">&laquo; Back to Campaign list</a>

<?php } else if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){?>
<div class="updated"><p>Idea updated</p></div>
<a href="<?php echo admin_url('admin.php?page=campaign_list')?>">&laquo; Back to Campaign list</a>

<?php } else {?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id = "formID">

<table class='wp-list-table widefat fixed'>


<tr><td><h1> Select a Campaign *</h1> </td> <td></td></tr>
<tr>
<?php $rows_campaign = $wpdb->get_results($wpdb->prepare("SELECT id, start_date, end_date, title, status from ".$wpdb->prefix."campaign order by title", $id)); ?>
<td>
	<select name="campaign_id" class="drop-plagin">
	<?php foreach ($rows_campaign as $row_campaign){ 
	?>
		<option <?php if($campaign_id==$row_campaign->id){echo"selected";}?> value="<?php echo $row_campaign->id?>"><?php if(trim($row_campaign->title) == ""){ echo "No Title"; }?>
		<?php if(trim($row_campaign->title) != ""){ echo sanitize_text_field(stripslashes($row_campaign->title)); } ?></option>
	<?php } ?>
	</select></td></tr>


<tr><td> <h1>Title *</h1> </td> </tr>
<tr><td colspan="1"><input type="text" id="title" name="title" class="croud-text" maxlength="200" required="required" value="<?php echo (stripslashes($title));?>" /></td></tr>

<tr><td><h1>Idea Category *</h1> </td> <td></td></tr>
<tr>
<?php 	$rows_category = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."category order by title", $id)); ?>
<td>
	<select name="cat_id" class="drop-plagin" required="required" id = "cat_id">
		<option value="">Select</option>
	<?php foreach ($rows_category as $row_category){ ?>
		<option value="<?php echo $row_category->id?>" <?php if($cat_id==$row_category->id){echo"selected";}?>><?php echo sanitize_text_field(stripslashes($row_category->title))?></option>
	<?php } ?>
	</select></td></tr>
	
<tr><th> <h1>Upload Image </h1> </th></tr> 
<tr><td><input type="file" name="image" />
<input type="hidden" name="id" id="id" value="1" />

</td></tr>

<!--<tr><th><h1> Description *</h1> </th></tr>
<tr><td colspan="1"><textarea name="description" id = "description" required="required" class="croud-text" cols="40" rows="16"><?php #echo stripslashes($description);?></textarea></td></tr>
-->
<tr><th><h1>Describe the problem or opportunity *</h1></th></tr>
<tr><td colspan="1" ><textarea name="describe_problem" id="describe_problem" class="croud-text" cols="40" rows="16"><?php echo stripslashes($describe_problem);?></textarea></td><td></td></tr>

<tr><th><h1>Describe the idea *</h1></th></tr>
<tr><td colspan="1" ><textarea name="describe_idea" id="describe_idea" class="croud-text" cols="40" rows="16"><?php echo stripslashes($describe_idea);?></textarea></td><td></td></tr>

<tr><td> <h1>Tags </h1> </td> </tr>
<tr><td colspan="1"><input type="text" name="tags" class="croud-text" value="<?php echo (stripslashes($tags));?>" /></td></tr>

<tr><td><h1>Status *</h1> </td> <td></td></tr>
<tr>
<td>
	<select name="status" class="drop-plagin">
		<option <?php if ($status == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
		<option <?php if ($status == "N" ) echo 'selected' ; ?> value="N">No</option>
	</select></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="update" value='Update Idea' class='button button-primary button-large' Onclick="return validcheckfields();">&nbsp;&nbsp; 
<input type='submit' name="delete" value='Delete' class='button button-primary button-large' onclick="return confirm('Are you sure to delete this Idea ?')">
&nbsp; &nbsp; &nbsp; &nbsp; * = mandatory
</form>
</div>
<?php
	}
}
?>