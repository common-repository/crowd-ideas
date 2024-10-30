<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function crowdideas_campaign_show(){	
global $wpdb;
global $current_user;

add_action( 'init', 'crowdideas_campaign_show_frontend_enqueuer' );
function crowdideas_campaign_show_frontend_enqueuer() {
   wp_register_style( "crowd_ideas_campaign_show_list", plugins_url( 'css/campaign_show_frontend.css', __FILE__ ));
   wp_enqueue_style( 'crowd_ideas_campaign_show_list' );
} 
crowdideas_campaign_show_frontend_enqueuer();


//print_r($current_user);
if ( is_user_logged_in() ) {
	
	$today_date = date('Y-m-d');
	/*echo "SELECT * from ".$wpdb->prefix."campaign Where start_date <= '$today_date' And end_date >= '$today_date' 
	Order By id desc"; */
	
	/*$rows = $wpdb->get_results("SELECT * from ".$wpdb->prefix."campaign Where start_date <= '$today_date' And end_date >= '$today_date' 
	And status = 'Yes' Order By id desc"); */
	
	//$rows = $wpdb->get_results("SELECT * from ".$wpdb->prefix."campaign Where status = 'Yes' Order By id desc");
	
		//echo "<table class='wp-list-table widefat fixed'>";
		/*echo "<tr>
			<th>ID</th>
			<th>Title</th>
			<th>Description</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Campaign Goals</th>
		</tr></table>"; */
		$i = 1;
		
		// *********** Get Page Name ********* //
		global $post;
		$pagename = $post->post_name;
		//echo 'page name: ' . $pagename;
		//echo the_ID();
		#echo get_site_url();
		$page_of_campaign_details = get_page_by_title( 'campaign details' ); 
		//echo $page_of_campaign_details->ID;


		// *********** Get Page Name ********* //
		
		?>
		<div>
		<div>
		<h1>Campaigns</h1>
		<div>
		<?php
		
		$customPagHTML     = "";
		$query             = "SELECT * from ".$wpdb->prefix."campaign Where start_date <= '".$today_date."' And end_date >= '".$today_date."' And status = 'Yes'";
		//$query           = "SELECT * from ".$wpdb->prefix."campaign Where status = 'Yes'";
		$total_query       = "SELECT COUNT(1) FROM (${query}) AS combined_table";
		$total             = $wpdb->get_var( $total_query );
		$items_per_page    = 25;
		$page              = intval(isset( $_GET['cpage'] )) ? intval(abs( (int) $_GET['cpage'] )) : 1;
		$offset            = ( $page * $items_per_page ) - $items_per_page;
		$result            = $wpdb->get_results( $wpdb->prepare($query . " ORDER BY id DESC LIMIT ${offset}, ${items_per_page}", $option_prepare) );
		$totalPage         = ceil($total / $items_per_page);
		$uploads 		   = wp_upload_dir();
		
	if($totalPage == 1){ 
		foreach ($result as $row ){ 
			$replace = str_replace(' ', '-', stripslashes(esc_html($row->title)));
			$replace = str_replace('/', '-', stripslashes($replace));
			//echo "<tr>";
			//echo "<td>".$i++."</td>";
			//echo "<td>";
			if ( get_option('permalink_structure') ) { 	
			//echo 'permalinks enabled'; 
			$campaign_details_link = stripslashes(esc_html($row->title));
			$raw_details_link =  get_site_url()."/campaign-details?/".esc_html($row->id)."-$replace";	
			}
			
			if ( !get_option('permalink_structure') ) { 	
			//echo 'permalinks enabled'; 
			$campaign_details_link = stripslashes(esc_html($row->title));
			$raw_details_link =  get_site_url()."?page_id=$page_of_campaign_details->ID?".esc_html($row->id)."-$replace";
			}
			
		?>
		
		
	
	
	<div class="container">
      <div class="campain-listing">
         <div class="one-third">
             <div class="campain-wrap">
                <div class="campain-image">
                  <a href="<?php echo $raw_details_link?>"> <?php if($row->image != '_') { ?>
						<img src="<?php echo esc_url( $uploads['baseurl'] . '/crowdideas/campaigns/250x250_'.$row->image )?>">
					
					<?php } ?>
			
					<?php if($row->image == '_') { ?>
						<img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>">
					<?php } ?> 
				  </a>
                </div>
                <div class="campain-details">
				<?php if(trim($row->title) == ""){ ?>
					<h2>No Title</h2>
				<?php } if(trim($row->title) != ""){ ?>
                	<h2><?php echo substr($campaign_details_link, 0, 20)?></h2>
				<?php } ?>	
                    <div class="date-time">
                        <ul>
                         <li>By Administrator</li>
                          <li> <?php echo date('j F Y', strtotime(esc_html($row->start_date)))?> </li>
                        </ul>
                    </div>
                    <p><?php echo ((stripslashes(esc_html(substr($row->description, 0, 100))))); ?></p>
                    <div class="read-more-btn">
                       <a href="<?php echo $raw_details_link?>"> Read More </a>
                    </div>
                </div>
                
             </div><!-- campain-wrap -->
         </div> <!-- one-third -->
      </div>  <!-- campain-listing -->
   </div>
		
		
		
		
		
		
		
			
			<?php
			
			}
	}
	
	if($totalPage > 1){ 
		foreach ($result as $row ){
			$replace = str_replace(' ', '-', stripslashes(esc_html($row->title)));
			$replace = str_replace('/', '-', stripslashes($replace));
			//echo "<tr>";
			//echo "<td>".$i++."</td>";
			//echo "<td>";
			if ( get_option('permalink_structure') ) { 	
			//echo 'permalinks enabled'; 
			$campaign_details_link = stripslashes(esc_html($row->title));
			$raw_details_link = get_site_url()."/campaign-details?/".esc_html($row->id)."-$replace";
			}
			
			if ( !get_option('permalink_structure') ) { 	
			//echo 'permalinks enabled'; 
			$campaign_details_link = stripslashes(esc_html($row->title));
			$raw_details_link = get_site_url()."?page_id=$page_of_campaign_details->ID?".esc_html($row->id)."-$replace";
			}
		?>
	<div class="container">
      <div class="campain-listing">
         <div class="one-third">
             <div class="campain-wrap">
                <div class="campain-image">
                  <a href="<?php echo $raw_details_link?>"> <?php if($row->image != '_') { ?>
						<img src="<?php echo esc_url( $uploads['baseurl'] . '/crowdideas/campaigns/250x250_'.$row->image )?>">
					<?php } ?>
			
					<?php if($row->image == '_') { ?>
						<img src="<?php echo plugins_url( 'img/default.jpg', __FILE__ );?>">
					<?php } ?> 
				  </a>
                </div>
                <div class="campain-details">
                	<?php if(trim($row->title) == ""){ ?>
					<h2>No Title</h2>
					<?php } if(trim($row->title) != ""){ ?>
                	<h2><?php echo substr($campaign_details_link, 0, 20)?></h2>
					<?php } ?>
                    <div class="date-time">
                        <ul>
                         <li>By Administrator</li>
                          <li> <?php echo date('j F Y', strtotime(esc_html($row->start_date)))?> </li>
                        </ul>
                    </div>
                    <p><?php echo ((stripslashes(esc_html(substr($row->description, 0, 100))))); ?></p>
                    <div class="read-more-btn">
                       <a href="<?php echo $raw_details_link?>"> Read More </a>
                    </div>
                </div>
                
             </div><!-- campain-wrap -->
         </div> <!-- one-third -->
      </div>  <!-- campain-listing -->
     </div>
			<?php
			
			}
		
		
		$customPagHTML     =  '<div class="cat-pagination"><span class="pagi-start">Page '.$page.' of '.$totalPage.'</span>&nbsp;&nbsp;'.paginate_links( array(
		'base' => add_query_arg( 'cpage', '%#%' ),
		'format' => '',
		'prev_text' => __('&laquo;'),
		'next_text' => __('&raquo;'),
		'total' => $totalPage,
		'current' => $page
		)).'</div>';
	}
	echo $customPagHTML;
	
			?></div>
			</div>
			</div>
			
		<?php
		
		
	}
	if ( !is_user_logged_in() ) {
		echo "Please login to see the campaigns";
	}
}
?>