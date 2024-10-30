<?phpif ( ! defined( 'ABSPATH' ) ) exit;
function crowdideas_settings () {
global $wpdb;add_action( 'init', 'crowdideas_settings_script_enqueuer' );function crowdideas_settings_script_enqueuer() {   wp_enqueue_style("crowd_ideas_settings_jquery_update", plugins_url( 'css/jquery-ui.css', __FILE__ ));  } crowdideas_settings_script_enqueuer();
if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){
if ( current_user_can( 'administrator' ) ){
$post_ideas=sanitize_text_field($_POST["post_ideas"]);
$edit_ideas=sanitize_text_field($_POST["edit_ideas"]);
$rate_ideas=sanitize_text_field($_POST["rate_ideas"]);
$comment=sanitize_text_field($_POST["comment"]);
$select_ideas=sanitize_text_field($_POST["select_ideas"]);
$id = 1;
		$wpdb->get_var( $wpdb->prepare($wpdb->update( $wpdb->prefix// table prefix
			.'settings_crowdideas', //table
			array('post_ideas' => $post_ideas, 
			'edit_ideas' => $edit_ideas, 
			'rate_ideas' => $rate_ideas, 
			'comment' => $comment, 
			'select_ideas' => $select_ideas), 
			array( 'id' => $id )), //where
			array('%s'), //data format
			array('%s') //where format
		));		}
}
$id = 1;
$settings = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."settings_crowdideas where id=%s", $id));
//print_r($settings);

foreach ($settings as $s ){
		$post_ideas=esc_html($s->post_ideas);
		$edit_ideas=esc_html($s->edit_ideas);
		$rate_ideas=esc_html($s->rate_ideas);
		$comment=esc_html($s->comment);
		$select_ideas=esc_html($s->select_ideas);
	}

?>

<div class="wrap">
<h1>Edit Feature Settings</h1>
<?php if(wp_verify_nonce($_POST['formsubmit'], 'formsubmit')){?>
<div class="updated"><p>Settings updated</p></div>
<?php } ?>

<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<table class='wp-list-table widefat fixed'>
<tr><th><h1> Post Ideas </h1> </th></tr>
<tr><td><select name="post_ideas" class="drop-plagin">
<option <?php if ($post_ideas == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
<option <?php if ($post_ideas == "N" ) echo 'selected' ; ?> value="N">No</option>
</select></td><td></td></tr>

<tr><th><h1> Edit Ideas </h1> </th></tr>
<tr><td><select name="edit_ideas" class="drop-plagin">
<option <?php if ($edit_ideas  == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
<option <?php if ($edit_ideas  == "N" ) echo 'selected' ; ?> value="N">No</option>
</select></td><td></td></tr>

<tr><th><h1> Rate Ideas </h1> </th></tr>
<tr><td><select name="rate_ideas" class="drop-plagin">
<option <?php if ($rate_ideas == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
<option <?php if ($rate_ideas == "N" ) echo 'selected' ; ?> value="N">No</option>
</select></td><td></td></tr>

<tr><th><h1> Comments </h1> </th></tr>
<tr><td><select name="comment" class="drop-plagin">
<option <?php if ($comment  == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
<option <?php if ($comment  == "N" ) echo 'selected' ; ?> value="N">No</option>
</select></td><td></td></tr>

<tr><th><h1> Select Ideas </h1> </th></tr>
<tr><td><select name="select_ideas" class="drop-plagin">
<option <?php if ($select_ideas == "Y" ) echo 'selected' ; ?> value="Y">Yes</option>
<option <?php if ($select_ideas == "N" ) echo 'selected' ; ?> value="N">No</option>
</select></td><td></td></tr>

</table></br><?php echo wp_nonce_field('formsubmit', 'formsubmit');?>
<input type='submit' name="update" value='Update' class='button button-primary button-large'> 

</form>
</div>
<?php }