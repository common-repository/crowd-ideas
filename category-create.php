<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_category_create () {
$title = sanitize_text_field(trim($_POST["title"]));
$status = sanitize_text_field($_POST["status"]);

//insert
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
	if ( current_user_can( 'administrator' ) ){
	global $wpdb;
	
	$wpdb->get_var( $wpdb->prepare($wpdb->insert($wpdb->prefix.
		'category', //table
		array(
			'title' => $title,
			'status' => $status,
			)), //data
		array('%s','%s') //data format			
	));
	
	
	$message.="Category inserted";
	}
}

add_action( 'init', 'crowdideas_category_create_script_enqueuer' );
function crowdideas_category_create_script_enqueuer() {
   wp_register_script( "category_create_js", plugins_url( 'js/single-create-category.js', __FILE__ ));
   wp_enqueue_script( 'category_create_js' );
} 
crowdideas_category_create_script_enqueuer();

?>

<div class="wrap">
<h1>Create Category</h1>
<?php if (isset($message)): ?><div class="updated"><p><?php echo $message;?></p></div><?php endif;?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" id = "formID">
<table class='wp-list-table widefat fixed'>
<tr><td><h1>Title *</h1></td></tr>
<tr><td colspan="1"> <input type="text" id = "title" name="title" required="required" maxlength="200" class="croud-text" /></td></tr>
<tr><td><h1> Status *</h1> </td> <td></td></tr>
<tr>
<td>
	<select name="status" class="drop-plagin">
		<option value="Yes">Yes</option>
		<option value="No">No</option>
	</select></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="insert" value='Save' class='button button-primary button-large' Onclick="return validcheckfields();">
</form>
</div>
<?php
}