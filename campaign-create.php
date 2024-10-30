<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_campaign_create () {
require_once('function.php');
$title = sanitize_text_field(htmlspecialchars($_POST["title"]));
$description = sanitize_text_field($_POST["description"]);
$start_date = sanitize_text_field($_POST["start_date"]);
$end_date = sanitize_text_field($_POST["end_date"]);
$campaign_goals = sanitize_text_field($_POST["campaign_goals"]);
$status = sanitize_text_field($_POST["status"]);

add_action( 'init', 'crowdideas_campaign_create_script_enqueuer' );
function crowdideas_campaign_create_script_enqueuer() {
   wp_enqueue_style("crowd_ideas_campaigns_jquery", plugins_url( 'css/jquery-ui.css', __FILE__ ));
   wp_enqueue_script( "crowd_ideas_campaigns_create_js", plugins_url( 'js/single-campaign-create.js', __FILE__ ));
   wp_enqueue_script("crowd_ideas_campaigns_create_datepicker_js", plugins_url( 'js/single-campaign-create-datepicker.js', __FILE__ ));
} 
crowdideas_campaign_create_script_enqueuer();


//insert
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
	if ( current_user_can( 'administrator' ) ){
	global $_FILES;
	//if ( $_POST['image']['tmp_name']!="") { 

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

	//} 
	//exit;
	global $wpdb;
	
	$wpdb->get_var( $wpdb->prepare($wpdb->insert($wpdb->prefix.'campaign', //table
		array(
			'user_id' => get_current_user_id(),
			'title' => $title,
			'description' => $description,
			'image' => $rand.'_'.$_FILES['image']['name'],
			'start_date' => date("Y-m-d", strtotime($start_date)),
			'end_date' =>  date("Y-m-d", strtotime($end_date)),
			'campaign_goals' => $campaign_goals,
			'status' => $status,
			)), //data
		array('%s','%s') //data format			
	));
	$message.="Campaign added";
	}
}
?>

<div class="wrap">
<h1>Create A Campaign</h1>
<?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data" id = "formID">
<table class='wp-list-table widefat fixed'>
<tr><td> <h1>Title *</h1> </td> </tr>
<tr><td colspan="1"><input id = "title" type="text" name="title" class="croud-text" maxlength="200" required="required"/></td></tr>
<tr><th><h1> Description *</h1> </th></tr>
<tr><td colspan="1"><textarea id = "description" name="description" required="required" class="croud-text" cols="40" rows="16"></textarea></td></tr>

<tr><th > <h1> Upload Image (only jpg and png) </h1> </th></tr> 
<tr><td><input type="file" name="image" value="<?php echo $image;?>"/>
		<input type="hidden" name="id" id="id" value="1" />
</td></tr>
<tr><th  ><h1> Start Date *</h1> </th> </tr> <tr><td><input type="text" name="start_date"  class="croud-text" id="datepicker" required="required"/></td> </tr>
<tr> <th> <h1> End Date *</h1> </th></tr> <tr><td><input type="text" name="end_date" class="croud-text" id="datepicker1" required="required"/></td>
</tr>
<tr><th><h1>Goals </h1></th></tr>
<tr><td colspan="1" ><textarea name="campaign_goals" class="croud-text" cols="40" rows="16"></textarea></td><td></td></tr>
<tr><td><h1> Status *</h1> </td> <td></td></tr>
<tr>
<td>
	<select name="status" class="drop-plagin">
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="insert" value='Save' class='button button-primary button-large' Onclick="return validcheckfields();">&nbsp; &nbsp; &nbsp; &nbsp; * = mandatory
</form>
</div>

<?php
}
