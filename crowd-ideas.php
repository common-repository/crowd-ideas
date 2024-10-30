<?php 
/*
  Plugin Name: Crowd Ideas
  Plugin URI: 
  Description: Crowd Ideas is the extendable Wordpress plugin for corporate websites to crowd source ideas with customers via desktop or mobile and identify ideas for implementation.
  Version: 1.0
  Author: Crowd Ideas
  Author URI: http://www.crowdideas.com.au/
 
*/

/* ===============Function for creating Tables====================== */
register_activation_hook( __FILE__, 'crowdideas_create_plugin_tables' );
register_deactivation_hook(__FILE__, 'crowdideas_deactivation');

function crowdideas_deactivation(){
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'campaign';
    $table_name1 = $wpdb->prefix . 'category';
    $table_name2 = $wpdb->prefix . 'cat_campaign';
	$plugin_settings = $wpdb->prefix . 'settings_crowdideas';
	$plugin_ideas = $wpdb->prefix . 'ideas_crowdideas';
	$plugin_rate_ideas = $wpdb->prefix . 'ideas_rate_crowdideas';
	
	$sql_delete_campaign_table = "DROP TABLE IF EXISTS $table_name";
	$wpdb->query($sql_delete_campaign_table);
	
	$sql_delete_category_table = "DROP TABLE IF EXISTS $table_name1";
	$wpdb->query($sql_delete_category_table);
	
	$sql_delete_category_campaign_table = "DROP TABLE IF EXISTS $table_name2";
	$wpdb->query($sql_delete_category_campaign_table);
	
	$sql_delete_settings_table = "DROP TABLE IF EXISTS $plugin_settings";
	$wpdb->query($sql_delete_settings_table);
	
	$sql_delete_idea_table = "DROP TABLE IF EXISTS $plugin_ideas";
	$wpdb->query($sql_delete_idea_table);
	
	$sql_delete_rate_table = "DROP TABLE IF EXISTS $plugin_rate_ideas";
	$wpdb->query($sql_delete_rate_table);
}

function crowdideas_create_plugin_tables()
{
    global $wpdb;
	
	$upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_dir = $upload_dir . '/crowdideas';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }
	
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/crowdideas/campaigns';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }
	
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/crowdideas/ideas';
    if (! is_dir($upload_dir)) {
       mkdir( $upload_dir, 0700 );
    }

    // current blog table prefix
    $table_name = $wpdb->prefix . 'campaign';
    $table_name1 = $wpdb->prefix . 'category';
    $table_name2 = $wpdb->prefix . 'cat_campaign';
	$plugin_settings = $wpdb->prefix . 'settings_crowdideas';
	$plugin_ideas = $wpdb->prefix . 'ideas_crowdideas';
	$plugin_rate_ideas = $wpdb->prefix . 'ideas_rate_crowdideas';
	/*------------------------Table Insert------------------------ */
	
	if($wpdb->get_var('SHOW TABLES LIKE ' . $plugin_settings) != $plugin_settings){
	$sql = "CREATE TABLE ".$plugin_settings." (
		`id` INT( 1 ) NOT NULL ,
		`post_ideas` ENUM( \"Y\", \"N\" ) NOT NULL ,
		`edit_ideas` ENUM( \"Y\", \"N\" ) NOT NULL ,
		`rate_ideas` ENUM( \"Y\", \"N\" ) NOT NULL ,
		`comment` ENUM( \"Y\", \"N\" ) NOT NULL ,
		`select_ideas` ENUM( \"Y\", \"N\" ) NOT NULL ,
		PRIMARY KEY ( `id` )
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	
	// ********** Insert default values into Settings table ************* //
	$sql_insert_settings_value = "INSERT INTO ".$plugin_settings." (
		`id`, `post_ideas`, `edit_ideas`, `rate_ideas`, `comment`, `select_ideas`) 
		VALUES ('1', 'Y', 'Y', 'Y', 'Y', 'Y'
	);";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_insert_settings_value );
	}
	
	if($wpdb->get_var('SHOW TABLES LIKE ' . $plugin_ideas) != $plugin_ideas){
	$sql = "CREATE TABLE ".$plugin_ideas." (
			`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`image` VARCHAR( 255 ) NOT NULL ,
			`campaign_id` INT( 11 ) NOT NULL ,
			`cat_id` INT( 11 ) NOT NULL ,
			`describe_problem` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`describe_idea` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`created_date` DATE NOT NULL ,
			`created_by` VARCHAR( 10 ) NOT NULL ,
			`modified_date` DATE NOT NULL ,
			`modified_by` VARCHAR( 10 ) NOT NULL ,
			`tags` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
			`total_rates` INT( 11 ) NOT NULL ,
			`status` ENUM( 'Y', 'N' ) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	}
	
	
	if($wpdb->get_var('SHOW TABLES LIKE ' . $table_name) != $table_name){
	$sql = "CREATE TABLE ".$table_name." (
      id int(11) NOT NULL AUTO_INCREMENT,
      user_id int(11) NOT NULL ,
      title varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
      description text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	  image varchar(255) DEFAULT NULL,
	  start_date text DEFAULT NULL,
	  end_date text DEFAULT NULL,
	  category varchar(255) DEFAULT NULL,
	  campaign_goals text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	  campaign_suggestions text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	  post_idea_enable ENUM('Yes','No') DEFAULT 'No',
	  post_idea_edit_enable ENUM('Yes','No') DEFAULT 'No',
	  rate_idea_enable ENUM('Yes','No') DEFAULT 'No',
	  comment_enable ENUM('Yes','No') DEFAULT 'No',
	  select_idea_enable ENUM('Yes','No') DEFAULT 'No',
	  status ENUM('Yes','No') DEFAULT 'No',
	  created_at TIMESTAMP NOT NULL,
	  updated_at TIMESTAMP NOT NULL,
      UNIQUE KEY id (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
	}
	
	
	if($wpdb->get_var('SHOW TABLES LIKE ' . $table_name1) != $table_name1){
	$sql1 = "CREATE TABLE ".$table_name1." (
      id int(11) NOT NULL AUTO_INCREMENT,
      title varchar(255) DEFAULT NULL,
      status ENUM('Yes','No') DEFAULT 'No',
	  created_at TIMESTAMP NOT NULL,
	  updated_at TIMESTAMP NOT NULL,
	  UNIQUE KEY id (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql1 );
	}
	
	
	if($wpdb->get_var('SHOW TABLES LIKE ' . $table_name2) != $table_name2){
	$sql2 = "CREATE TABLE ".$table_name2." (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  campaign_id int(11) NOT NULL,
		  category_id int(11) NOT NULL,
		  UNIQUE KEY id (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql2 );
	 }
	 
	if($wpdb->get_var('SHOW TABLES LIKE ' . $plugin_rate_ideas) != $plugin_rate_ideas){
	$sql2 = "CREATE TABLE ".$plugin_rate_ideas." (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `id_idea` int(11) NOT NULL,
		  `user_id` int(11) NOT NULL,
		  `ip` varchar(40) NOT NULL,
		  `rate` int(11) NOT NULL,
		  `dt_rated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql2 );
	 }
}
/* =====================menu Creation ==============================*/
add_action('admin_menu', 'crowdideas_register_submenu_page');

function crowdideas_register_submenu_page() {
   //this is the main item for the menu
	add_menu_page('crowd_ideas', //page title
	'Crowd Ideas', //menu title
	'manage_options', //capabilities
	'campaign_list', //menu slug
	crowdideas_campaign_list //function
	);
	
	add_submenu_page('campaign_list',
	'Campaign', //page title
	'Campaign', //menu title
	'manage_options', //capabilities
	'campaign_list', //menu slug
	crowdideas_campaign_list //function
	);
	
	//this is a submenu
	add_submenu_page('campaign_list', //parent slug
	'Create Campaign', //page title
	'Create Campaign', //menu title
	'manage_options', //capability
	'campaign_create', //menu slug
	'crowdideas_campaign_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Campaign', //page title
	'Update Campaign', //menu title
	'manage_options', //capability
	'campaign_update', //menu slug
	'crowdideas_campaign_update'); //function
	
	add_submenu_page('campaign_list',
	'Category', //page title
	'Category', //menu title
	'manage_options', //capabilities
	'category_list', //menu slug
	crowdideas_category_list //function
	);
	
	//this is a submenu
	add_submenu_page('campaign_list', //parent slug
	'Create Category', //page title
	'Create Category', //menu title
	'manage_options', //capability
	'category_create', //menu slug
	'crowdideas_category_create');	
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Category', //page title
	'Update', //menu title
	'manage_options', //capability
	'category_update', //menu slug
	'crowdideas_category_update'); //function
	
	add_submenu_page('campaign_list', //parent slug
	'Submit Ideas', //page title
	'Submit Ideas', //menu title
	'manage_options', //capability
	'submit_ideas', //menu slug
	'crowdideas_submit_idea');	
	
	add_submenu_page('campaign_list', //parent slug
	'Feature Settings', //page title
	'Feature Settings', //menu title
	'manage_options', //capability
	'crowdideas_settings', //menu slug
	'crowdideas_settings');
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Show Idea', //page title
	'Show Idea', //menu title
	'manage_options', //capability
	'show_idea', //menu slug
	'crowdideas_show_idea'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Idea', //page title
	'Update Idea', //menu title
	'manage_options', //capability
	'idea_update', //menu slug
	'crowdideas_idea_update'); //function

	
}

/*
function crowdideas_category_show(){ 
global $wpdb;
$rows = $wpdb->get_results("SELECT * from ".$wpdb->prefix."category");
$txt.= "<table class='wp-list-table widefat fixed'>";
$txt.= "<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Status</th>
			<th>&nbsp;</th>
		</tr>";
foreach ($rows as $row ){
	$txt.= "<tr>";
	$txt.= "<td>$row->id</td>";
	$txt.= "<td>$row->title</td>";	
	$txt.= "<td>$row->status</td>";	
	$txt.= "</tr>";}
$txt.= "</table>";
echo $txt;
	
}*/

/* function crowdideas_campaign_show(){
	
global $wpdb;
$rows = $wpdb->get_results("SELECT * from wp_campaign");
echo "<table class='wp-list-table widefat fixed'>";
echo "<tr>
		<th>ID</th>
		<th>Title</th>
		<th>Description</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>Campaign Goals</th>
	</tr>";
$i = 1;
foreach ($rows as $row ){
	echo "<tr>";
	echo "<td>".$i++."</td>";
	echo "<td><a href='#'>$row->title</a></td>";	
	echo "<td>$row->description</td>";	
	echo "<td>$row->start_date</td>";	
	echo "<td>$row->end_date</td>";	
	echo "<td>$row->campaign_goals</td>";	
	echo "</tr>";}
echo "</table>";
	
} */

/* -------------- Generate Shortcode ---------------- */
// =====================================================
add_shortcode('campaign-list', 'crowdideas_campaign_show');
add_shortcode('campaign-details', 'crowdideas_details');
add_shortcode('submit-idea', 'crowdideas_submit_idea_fe');
add_shortcode('idea-show-details', 'crowdideas_idea_show_details');
add_shortcode('show-categories-frontend', 'crowdideas_show_categories_frontend');
/* -------------- Generate Shortcode ---------------- */
// =====================================================



/*function crowdideas_settings(){ 
global $wpdb;
$rows = $wpdb->get_results( $wpdb->prepare("SELECT * from" . $plugin_settings, $option_prepare));
$txt.= "<table class='wp-list-table widefat fixed'>";
$txt.= "<tr><th>ID</th><th>Name</th><th>&nbsp;</th></tr>";
foreach ($rows as $row ){
	$txt.= "<tr>";
	$txt.= "<td>".esc_html($row->id)."</td>";
	$txt.= "<td>".esc_html($row->post_ideas)."</td>";	
	$txt.= "</tr>";}
$txt.= "</table>";
echo $txt;
}*/

/* ========== Widgets ================ */
/*
class show_categories_frontend extends WP_Widget
{

function crowdideas_show_categories_frontend()
  {
    $widget_ops = array('classname' => 'show_categories_frontend', 'description' => 'Displays a random post with thumbnail' );
    $this->WP_Widget('show_categories_frontend', 'Random Post and Thumbnail', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
    echo "<h1>This is my new widget!</h1>";
	if ( is_user_logged_in() ) {
	global $wpdb;
	$textcat = "";
	$page_of_show_idea_as_list = get_page_by_title( 'Categories' );
	
	
	
	$rows_idea_categories = $wpdb->get_results("SELECT c.title, c.id, count( i.cat_id ) AS total from ".$wpdb->prefix."category as c 
	LEFT JOIN ".$wpdb->prefix."ideas_crowdideas as i 
	ON c.id = i.cat_id
	Where c.status = 'Yes' 
	Group By c.id
	Order By c.title asc");
	$textcat.= "<div class=\"main div 2\">";
	
	$textcat.= "<div><b>Categories: </b></div>";
	$textcat.= "<div class=\"inner  \">";

	
	foreach ($rows_idea_categories as $rows_idea_category ){
		
	if ( get_option('permalink_structure') ) {	
	$replace_cat_title = str_replace(' ', '-', stripslashes($rows_idea_category->title));
	$cat_link_to_details = get_site_url().'/categories?/'.$rows_idea_category->id.'-'.$replace_cat_title;
	}

	if ( !get_option('permalink_structure') ) {	
		$replace_cat_title = str_replace(' ', '-', stripslashes($rows_idea_category->title));
		$cat_link_to_details = get_site_url().'?page_id='.$page_of_show_idea_as_list->ID.'/'.$rows_idea_category->id.'-'.$replace_cat_title;
	}	
	
		$textcat.= "<div ><a href='$cat_link_to_details'> $rows_idea_category->title (". $rows_idea_category->total .") </a></div>";	
	}
	$textcat.= "</div>";
	$textcat.= "</div>"; // end of main div 2
	echo $textcat;
	}
 
    echo $after_widget;
  }
}
  add_action( 'widgets_init', create_function('', 'return register_widget("show_categories_frontend");') );
  */
/* ========== Widgets ================ */






function crowdideas_details(){ 
global $wpdb;

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

//echo "SELECT id,title from ".$wpdb->prefix."campaign Where id = '$id[0]'";
//wp_title('School Details');

$today_date = strtotime(date('Y-m-d'));
$rows = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."campaign Where id = '$id[0]'", $option_prepare));
$show_error = 0; 
$uploads = wp_upload_dir();

$count_array_result = count($rows);
if($count_array_result == 0){echo "This campaign is not exists. <br>"; $show_error = 1;}

foreach ($rows as $row ){
if(esc_html($row->status) == 'No') {echo "This campaign is no more exists. <br>"; $show_error = 1; }
if(strtotime(esc_html($row->start_date)) > $today_date) {echo "This campaign will be starting from ". esc_html($row->start_date); $show_error = 1; }
if(strtotime(esc_html($row->end_date)) < $today_date) {echo "This campaign was expired on ". esc_html($row->end_date); $show_error = 1; }
if($show_error == 0){
global $current_user;
global $post;
$pagename = $post->post_name;
$page_of_submit_idea = get_page_by_title( 'Submit Idea' );
#print_r($page_of_submit_idea);
 	
if ( get_option('permalink_structure') ) {	
	$replace_title = str_replace(' ', '-', stripslashes(esc_html($row->title)));
	$replace_title = str_replace('/', '-', stripslashes($replace_title));
	$submit_idea_link = get_site_url().'/submit-idea?/'.esc_html($row->id).'-'.$replace_title;
}

if ( !get_option('permalink_structure') ) {	
	$replace_title = str_replace(' ', '-', stripslashes(esc_html($row->title)));
	$replace_title = str_replace('/', '-', stripslashes($replace_title));
	$submit_idea_link = get_site_url().'?page_id='.$page_of_submit_idea->ID.'/'.esc_html($row->id).'-'.$replace_title;
}

$txt.= '<div>'; if(intval($_GET['idea'])=='1'){ echo "<b>You have successfully submitted your idea</b>"; } $txt.='</div>
		<div style="float: right;" class="post-link field"><a href='.$submit_idea_link.'>Submit Your Idea</a></div>';
$txt.= "<div>
        <h2>".nl2br(stripslashes(esc_html($row->title)))."</h2>";
	   
$txt.= "<div class=\"post-image\">";
		
		
		if(esc_html($row->image) != '_') { $campaign_image = esc_html($row->image);
//$txt.=	'<img src='.get_site_url().'/wp-content/plugins/crowd-ideas/uploads/campaigns/250x250_'.$campaign_image.'>';
$txt.=	"<img src='".esc_url( $uploads['baseurl']."/crowdideas/campaigns/250x250_".$campaign_image)."'>";
	
		}
		
		if(esc_html($row->image) == '_') { 
$txt.=	'<img src="'.plugins_url( 'img/default.jpg', __FILE__ ).'"width="300" height="300">';		
		}
		$txt.= "</div>

		<div>
		
		<p style=\"text-align:justify\">".nl2br((stripslashes(esc_html($row->description))))."</p>
		
		<br>
		
		<div><h3>Goal</h3></div>
		<p style=\"text-align:justify\">".nl2br((stripslashes(esc_html($row->campaign_goals))))."</p>

		</div>";
$txt.= '<div style="float: right;" class="post-link"><a href='.$submit_idea_link.'>Submit Your Idea</a></div>';
$txt.= 	"<div style=\"clear:both\"></div>
		</div>
		<div style=\"clear:both\"></div><br>";
		}
	}
echo $txt;

if($show_error == 0){
/* ************************* Show Ideas as list *********************** */
// ***********************************************************************
add_action( 'init', 'crowdideas_campaign_show_details_frontend_enqueuer' );
function crowdideas_campaign_show_details_frontend_enqueuer() {
   wp_register_script( "crowd_ideas_campaign_show_list_ideas_as_list", plugins_url( 'js/show_idea_as_list.js', __FILE__ ));
   wp_enqueue_script( 'crowd_ideas_campaign_show_list_ideas_as_list' );
} 
crowdideas_campaign_show_details_frontend_enqueuer();

$table_name = $wpdb->prefix . 'ideas_crowdideas';
$count_query = "select count(*) from $table_name Where campaign_id = '$id[0]' AND status = 'Y'";
$num = $wpdb->get_var($wpdb->prepare($count_query, $option_prepare));
if($num >0) {
?>
<form name="rank" id="rank" method = "post" action="">
	<select name="rankby" class="rank-dropdown">
		<option value="">Select</option>
		<option value="Desc" <?php if ($_POST['rankby']=="Desc"){echo "selected"; }?>>Show by ratings ascending</option>
		<option value="Asc" <?php if ($_POST['rankby']=="Asc"){echo "selected"; }?>>Show by ratings descending </option>
	</select>
	<input type="hidden" name="rankhidden" Value="rankhidden">
	<input type="submit" name="ranksubmit" Value="Rank" onclick="return validrank();";>
</form>
<?php } // ideas are greater than 0 bracket closed ?>
<?php 
if($_POST['rankhidden'] == "rankhidden"){
	$rankby = sanitize_text_field($_POST['rankby']);
	$order_by = " Order By total_rates $rankby";
}

if(sanitize_text_field($_POST['rankhidden']) != "rankhidden"){
	$order_by = " Order By id desc";
}


$rows_ideas = $wpdb->get_results($wpdb->prepare("SELECT * from ".$wpdb->prefix."ideas_crowdideas Where campaign_id = '$id[0]' AND status = 'Y' $order_by", $option_prepare));

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
	

$txtidea.= "<div class=\"comment-box-div\"> <div class=\"comment-author vcard\"> <a href=\"$idea_link_to_details\">";

if(esc_html($idea->image) == '_') {$txtidea.= "<img class=\"co-img\" src=".plugins_url('img/default.jpg', __FILE__ )." width=\"100\" height=\"100\"></td>";}
if(esc_html($idea->image) != '_') {$txtidea.= "<img class=\"co-img\" src='".$uploads['baseurl']."/crowdideas/ideas/250x250_".esc_html($idea->image)."'>";
//<img class=\"co-img\" src=".get_site_url()."/wp-content/plugins/crowd-ideas/uploads/ideas/100x100_".$idea->image."></td>";
}

if(trim(esc_html($idea->title)) == '') {
$txtidea.= "<a class=\"title-anchar\" href=\"$idea_link_to_details\">
<b>No Title</b></a>
</div>";
}  

if(trim(esc_html($idea->title)) != '') {
$txtidea.= "<a class=\"title-anchar\" href=\"$idea_link_to_details\">
<b>".substr(stripslashes(wp_filter_nohtml_kses(wp_strip_all_tags($idea->title))), 0, 20)."</b></a>
</div>";
}      
		
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

echo crowdideas_show_idea_category();
}
} // 	if user logged in

if ( !is_user_logged_in() ) {
		echo "Please login to see the campaign";
	} // if not logged in
	
} // campaign details function end


function crowdideas_show_idea_category(){
	global $wpdb;
	$textcat = "";
	$page_of_show_idea_as_list = get_page_by_title( 'Categories' );

add_action( 'init', 'crowdideas_cat_list_show_frontend_enqueuer' );
function crowdideas_cat_list_show_frontend_enqueuer() {
   wp_register_style( "crowd_ideas_cat_show_list", plugins_url( 'css/show_idea_category_list.css', __FILE__ ));
   wp_enqueue_style( 'crowd_ideas_cat_show_list' );
} 
crowdideas_cat_list_show_frontend_enqueuer();	
	
	
	$rows_idea_categories = $wpdb->get_results($wpdb->prepare("SELECT c.title, c.id, count( i.cat_id ) AS total from ".$wpdb->prefix."category as c 
	LEFT JOIN ".$wpdb->prefix."ideas_crowdideas as i 
	ON c.id = i.cat_id
	Where c.status = 'Yes' And i.status= 'Y' 
	Group By c.id
	Order By c.title asc", $option_prepare));
	$textcat.= "<div class=\"main1 div 2\">";
	
	$textcat.= "<div><b>Categories: </b></div>";
	$textcat.= "<div class=\"inner  \">";

	
	foreach ($rows_idea_categories as $rows_idea_category ){
		
	if ( get_option('permalink_structure') ) {	
	$replace_cat_title = str_replace(' ', '-', stripslashes(esc_attr($rows_idea_category->title)));
	$cat_link_to_details = get_site_url().'/categories?/'.$rows_idea_category->id.'-'.$replace_cat_title;
	}

	if ( !get_option('permalink_structure') ) {	
		$replace_cat_title = str_replace(' ', '-', stripslashes(esc_attr($rows_idea_category->title)));
		$cat_link_to_details = get_site_url().'?page_id='.$page_of_show_idea_as_list->ID.'/'.esc_html($rows_idea_category->id).'-'.$replace_cat_title;
	}	
	
		$textcat.= "<div class=\"one-third  \"><a href='$cat_link_to_details'>" .stripslashes(esc_html($rows_idea_category->title)) . " (". esc_html($rows_idea_category->total) .") </a></div>";	
	}
	$textcat.= "</div>";
	$textcat.= "</div>"; // end of main div 2
	echo $textcat;
}

add_action('init', 'crowdideas_function');

function crowdideas_function(){

 $user_ID= get_current_user_id();   

   //echo "XXXXXXXYYYYYYY = User number $user_ID is loggedin";
   
   $user = new WP_User( $user_ID );

if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
    foreach ( $user->roles as $role )
    	if($role != "administrator") {
		add_action('admin_menu','crowdideas_show_ideas_self');
		}
}
}

//==========================//

// get the the role object
   //$subscriber = get_role('subscriber');
   // add $cap capability to this role object
   //$subscriber->add_cap('manage_schools');
   
 
// Define our custom capabilities
    $customCaps = array(
        'manage_schools'          => true,
        'read'                    => true,
    );
     
    // Create our CRM role and assign the custom capabilities to it
    add_role( 'subscriber', __( 'Subscriber', 'sinetiks-schools'), $customCaps ); 

   
/* ---------- For other users -------------- */

function crowdideas_show_ideas_self(){
	
	add_menu_page('Show Idea', 'Show Idea', 'read', 'crowd_ideas_show_idea_self_submitted', 'crowdideas_show_idea_self_submitted');
	//add_menu_page('Add Schools', 'Add Schools', 'read', 'sinetiks_schools_update', 'sinetiks_schools_update');

	
	add_submenu_page(null, //parent slug
	'Update Idea', //page title
	'Update Idea', //menu title
	'read', //capability
	'idea_update_frontend', //menu slug
	crowdideas_idea_update_frontend); //function
	
}

/* ---------- For other users -------------- */

//wp_localize_script( 'my_script', 'MyAjax', array( 'ajaxurl' => admin_url( 'wp-admin/admin-ajax.php' ) ) );
//add_action('wp_ajax_myFunction', 'myfunctionajax');


add_action( 'init', 'crowdideas_script_enqueuer' );
function crowdideas_script_enqueuer() {
   wp_enqueue_script('jquery');
   wp_enqueue_script('json2');
   wp_enqueue_script('jquery-ui-core');
   wp_enqueue_script('jquery-ui-datepicker');
   wp_enqueue_script('jquery-effects-core');
   
   wp_register_style( "crowdideas_example_style", plugins_url('css/example.css', __FILE__ ));
   wp_register_style( "crowdideas_single_style", plugins_url('css/single.css', __FILE__ ));
   wp_register_style( "crowdideas_style_admin", plugins_url('style-admin.css', __FILE__ ));
   wp_register_script( "crowdideas_voter_script", plugins_url('js/ajax.js', __FILE__ ));       
   wp_enqueue_script( 'crowdideas_voter_script' );
   wp_enqueue_style( 'crowdideas_example_style' );
   wp_enqueue_style( 'crowdideas_single_style' );
   wp_enqueue_style( 'crowdideas_style_admin' );
   
   
   
   
}

add_action("crowdideas_wp_ajax_my_user_vote", "my_user_vote");


if($_REQUEST['my_user_vote'] == 1){ 

global $wpdb;

function crowdideas_get_ip() {
		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}
		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$the_ip = $headers['X-Forwarded-For'];
		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
			
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		return $the_ip;
	}

//function my_user_vote() {
	
	//if($_REQUEST['act'] == 'rate'){ 
	
			/*$wpdb->insert($wpdb->prefix.
				'ideas_rate_crowdideas', //table
				array(
					'id_idea' => '1',
					'ip' => '::15',
					'rate' => '1'
					), //data
				array('%s','%s') //data format			
			);
			*/
	
	
    	//search if the user(ip) has already gave a note
		
    	$ip = crowdideas_get_ip();
    	$therate = sanitize_text_field($_POST['rate']);
    	$thepost = sanitize_text_field($_POST['post_id']);
		$theuserid = sanitize_text_field($_POST['theuserid']);

		
		/*$wpdb->insert($wpdb->prefix.
				'ideas_rate_crowdideas', //table
				array(
					'id_idea' => $thepost,
					'ip' => $ip,
					'rate' => $therate,
					'user_id' => $current_user->id,
					), //data
				array('%s','%s') //data format			
		); */


    	#$query = mysql_query("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas where user_id = '$theuserid' And id_idea = '$thepost'"); 
		
		/*while($data = mysql_fetch_assoc($query)){
    		$rate_db[] = $data;
    	}*/
		
		$queries_rates = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas where user_id = '$theuserid' And id_idea = '$thepost'", $option_prepare));
		foreach($queries_rates as $query_rate){
			$rate_db[] = $query_rate;
		}
		

    	if(@count($rate_db) == 0 ){
    		//mysql_query("INSERT INTO wp_ideas_rate_crowdideas (id_idea, user_id, ip, rate)VALUES('$thepost', '$theuserid', '$ip', '$therate')");
			
			$wpdb->get_var( $wpdb->prepare($wpdb->insert($wpdb->prefix.
				'ideas_rate_crowdideas', //table
				array(
					'id_idea' => $thepost,
					'ip' => $ip,
					'rate' => $therate,
					'user_id' => $theuserid,
					)), //data
				array('%s','%s') //data format			
			));
			$rate_bg1 = 0;
			#$query_fetch_current_rate1 = mysql_query("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$thepost'"); 
			$query_fetch_current_rate1 = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$thepost'", $option_prepare));
				
				/*
				while($data1 = mysql_fetch_assoc($query_fetch_current_rate1)){
					$rate_db1[] = $data1;
					$sum_rates1[] = $data1['rate'];
				}*/
			foreach($query_fetch_current_rate1 as $query_fetch_current_rate_result){
				$rate_db1[] = $query_fetch_current_rate_result;
				$sum_rates1[] = esc_html($query_fetch_current_rate_result->rate);
			}	
				
				if(@count($rate_db1)){
					$rate_times1 = count($rate_db1);
					$sum_rates1 = array_sum($sum_rates1);
					$rate_value1 = $sum_rates1/$rate_times1;
					$rate_bg1 = ((($rate_value1)/5)*100);
				}else{
					$rate_times1 = 0;
					$rate_value1 = 0;
					$rate_bg1 = 0;
			}
			$wpdb->get_var( $wpdb->prepare($wpdb->update( $wpdb->prefix.
				'ideas_crowdideas', //table
				array(
					'total_rates' => $rate_bg1,
					), //data
				array( 'ID' => $thepost )), //where
				array('%s'), //data format
				array('%s') //where format			
			));
			
    	}else{
    		#mysql_query("UPDATE ".$wpdb->prefix."ideas_rate_crowdideas SET rate= '$therate' WHERE user_id = '$theuserid' And id_idea = '$thepost'");
			$wpdb->get_var( $wpdb->prepare( $wpdb->update($wpdb->prefix.
				'ideas_rate_crowdideas', //table
				array(
					'rate' => $therate,
					), //data
				array( 'user_id' => $theuserid, 'id_idea' => $thepost)), //where
				array('%s'), //data format
				array('%s') //where format			
			));
			
			$rate_bg2 = 0;
			#$query_fetch_current_rate2 = mysql_query("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$thepost'"); 
			$query_fetch_current_rate2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."ideas_rate_crowdideas Where id_idea = '$thepost'", $option_prepare));
			
				/*while($data2 = mysql_fetch_assoc($query_fetch_current_rate2)){
					$rate_db2[] = $data2;
					$sum_rates2[] = $data2['rate'];
				}*/
				
				foreach($query_fetch_current_rate2 as $query_fetch_current_rate2_result){
				$rate_db2[] = $query_fetch_current_rate2_result;
				$sum_rates2[] = esc_html($query_fetch_current_rate2_result->rate);
			}
				
				
				
				
				
				if(@count($rate_db2)){
					$rate_times2 = count($rate_db2);
					$sum_rates2 = array_sum($sum_rates2);
					$rate_value2 = $sum_rates2/$rate_times2;
					$rate_bg2 = ((($rate_value2)/5)*100);
				}else{
					$rate_times2 = 0;
					$rate_value2 = 0;
					$rate_bg2 = 0;
			}
			$wpdb->get_var( $wpdb->prepare( $wpdb->update($wpdb->prefix.
				'ideas_crowdideas', //table
				array(
					'total_rates' => $rate_bg2,
					), //data
				array( 'ID' => $thepost )), //where
				array('%s'), //data format
				array('%s') //where format			
			));
			
			//mysql_query("update ".$wpdb->prefix."ideas_crowdideas SET total_rates= '$rate_bg2' WHERE id = '$thepost'"); 
    	}
    //} 
}
//}

require_once('home.php');
require_once('campaign-list.php');
require_once('campaign-create.php');
require_once('campaign-update.php');
require_once('category-create.php');
require_once('category-list.php');
require_once('category-update.php');
require_once('campaign-show.php');
require_once('pager.php');
require_once('crowdideas-settings.php');
require_once('submit-idea.php');
require_once('Submit-idea-frontend.php');
require_once('show-idea.php');
require_once('idea-update.php');
require_once('show-idea-frontend.php');
require_once('idea-update-frontend.php');
require_once('crowd_ideas_idea-show-details.php');
require_once('category-show-frontend.php');
?>