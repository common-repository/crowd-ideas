<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_category_update () {
global $wpdb;
$id = intval($_GET["id"]);
$title = sanitize_text_field(trim($_POST["title"]));
$status = sanitize_text_field($_POST["status"]);
//update
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
if ( current_user_can( 'administrator' ) ){
	$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.
		'category', //table
		array('title' => $title, //data
		'status' => $status), //data
		array( 'id' => $id )), //where
		array('%s'), //data format
		array('%s') //where format
	));	
}
}
//delete
else if(isset($_POST['delete'])){	
	if ( current_user_can( 'administrator' ) ){
		$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "category WHERE id = %s",$id));
	}
}
else{//selecting value to update	
	$schools = $wpdb->get_results($wpdb->prepare("SELECT * from " . $wpdb->prefix . "category where id=%s",$id));
	//echo "<pre>"; print_r($schools); echo "</pre>";
	foreach ($schools as $s ){
		$title=esc_html($s->title);
		$status=esc_html($s->status);
	}
}

add_action( 'init', 'crowdideas_category_update_script_enqueuer' );
function crowdideas_category_update_script_enqueuer() {
   wp_register_script( "category_update_js", plugins_url( 'js/single-category-update.js', __FILE__ ));
   wp_enqueue_script( 'category_update_js' );
} 
crowdideas_category_update_script_enqueuer();
?>


<div class="wrap">
<h1>Edit Category</h1>
<?php if($_POST['delete']){
if ( current_user_can( 'administrator' ) ){	
$wpdb->get_var( $wpdb->prepare($wpdb->update($wpdb->prefix.
		'category', //table
		array('title' => $title, //data
		'status' => $status), //data
		array( 'id' => $id )), //where
		array('%s'), //data format
		array('%s') //where format
	));
	
$wpdb->query($wpdb->prepare("DELETE FROM " . $wpdb->prefix . "category WHERE id = %s",$id));	
?>
<div class="updated"><p>Category deleted</p></div>
<a href="<?php echo admin_url('admin.php?page=category_list')?>">&laquo; Back to Category list</a>

<?php } } else if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){ ?>
<div class="updated"><p>Category updated</p></div>
<a href="<?php echo admin_url('admin.php?page=category_list')?>">&laquo; Back to Category list</a>

<?php } else {?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" id = "formID">
<table class='wp-list-table widefat fixed'>
<tr><th><h1> Title </h1> </th></tr>
<tr><td colspan="1"><input type="text" id = "title" name="title" required="required" maxlength="200" class="croud-text" value="<?php echo stripslashes($title);?>"/></td></tr>
<tr><th><h1>Status</h1></th></tr>
<tr><td><select name="status" class="drop-plagin">
		<option <?php if ($status == "Yes" ) echo 'selected' ; ?> value="Yes">Yes</option>
		<option <?php if ($status == "No" ) echo 'selected' ; ?> value="No">No</option>
	</select></td><td></td></tr>
</table></br>
<?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='button' name="update" value='Save' class='button button-primary button-large' Onclick="return validcheckfields();"> &nbsp;&nbsp;
<input type='submit' name="delete" value='Delete' class='button button-primary button-large' onclick="return confirm('Are you sure to delete this Category ?')">
</form>
<?php }?>

</div>
<?php
}