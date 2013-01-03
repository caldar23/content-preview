<?php
/*
Plugin Name: Content Preview
Plugin URI: http://www.clonemywebsite.com/
Description: Add a content preview to a CloneMyWebsite Theme using Pilotpress [Required].
Version: 1.3.1
Author: Andrew Myers / CloneMyWebsite.com
Author URI: http://www.clonemywebsite.com/

Vocab: cmwcp_
*/



function cmwcp_will_this_even_work() {
	
	// Check to see if our modified plugin for pilot press is installed with version 1.0.3 at least.
	// Give error and don't do anything besides giving a link for it.	
	
}

		
// Also Admin page!	
function cmwcp_admin_actions() {
			add_options_page('Content Preview', 'Content Preview', 'manage_options', 'ContentPreview', 'cmwcp_admin'); // makes the admin page, using the function cmwcp_admin
}		
		
		
// Makes the admin page link
add_action('admin_menu', 'cmwcp_admin_actions');	


// Top Warning Function
function cmwcp_top_warning() {
	
		$id = get_the_ID();
		$post_levels = get_post_meta($id, "_pilotpress_level");
		$post_levels = preg_replace('/[\s]+/', '_', $post_levels);

		
	$cmwcp_membership_rank_array = array();
		
	foreach ($post_levels as $post_levels) {
	
	$cmwcp_membership_rank = get_option( $post_levels.'_'.'cmwcp_membership_rank' );
	$cmwcp_membership_rank_array[$cmwcp_membership_rank] = $post_levels;
	
	}
	
	// Let's sort this out (Display based on ranking)
	ksort($cmwcp_membership_rank_array); // Arrange by key (which is the ranking given)
	$cmwcp_rank_array[0]; // Get the first one (The lowest number)
	$cmwcp_post_level = current($cmwcp_membership_rank_array) ; // Grab the value (should be the name)
	
	$cmwcp_top_warning = get_option( $cmwcp_post_level.'_'.'cmwcp_top_warning' );
	
	$cmwcp_top_warning = stripslashes($cmwcp_top_warning);
	
	
        return $cmwcp_top_warning;

}


// Bottom Warning Function
function cmwcp_bottomwarning() {
	
		$id = get_the_ID();
		$post_levels = get_post_meta($id, "_pilotpress_level");
		$post_levels = preg_replace('/[\s]+/', '_', $post_levels);

	
	$cmwcp_membership_rank_array = array();
		
	foreach ($post_levels as $post_levels) {
	
	$cmwcp_membership_rank = get_option( $post_levels.'_'.'cmwcp_membership_rank' );
	$cmwcp_membership_rank_array[$cmwcp_membership_rank] = $post_levels;
	
	}
	
	// Let's sort this out (Display based on ranking)
	ksort($cmwcp_membership_rank_array); // Arrange by key (which is the ranking given)
	$cmwcp_rank_array[0]; // Get the first one (The lowest number)
	$cmwcp_post_level = current($cmwcp_membership_rank_array) ; // Grab the value (should be the name)
	
	$cmwcp_bottom_warning = get_option( $cmwcp_post_level.'_cmwcp_bottom_warning' );
	
	$cmwcp_bottom_warning = stripslashes($cmwcp_bottom_warning);
	
		

        return $cmwcp_bottom_warning;
    

}



// Let's take the content, and add the warnings before and after.
function cmwcp_output( $content ) {
	
		$id = get_the_ID();
		$post_levels = get_post_meta($id, "_pilotpress_level");
		$post_levels = preg_replace('/[\s]+/', '_', $post_levels);


	if ( is_single() && !empty($post_levels) && !cmwcp_pilotpress_user_access()) {
//	if ( is_single() && !empty($post_levels)) { // this line is useful if you want to always show the warnings (like in development...)
		
		global $more;    // Declare global $more (before the loop).
		$more = 0;  
		$content = get_the_content('' , TRUE , '');
			
		$cmw_topwarning = cmwcp_top_warning();
		$cmw_bottomwarning = cmwcp_bottomwarning();
	
			echo $cmw_topwarning ; // echoing it places it above the content
			return $content . $cmw_bottomwarning; 		// Returns the content with the bottom warning tacked on. 
			
			
	} else { 

		return $content; 
	}
	
}


// Replace the content with the output funtion.
add_filter( 'the_content', 'cmwcp_output', 1 );



// This finds out if they should have access to the posts. 
function cmwcp_pilotpress_user_access() {
	
		$user_levels = $_SESSION["user_levels"];
		$id = get_the_ID();
		$post_levels = get_post_meta($id, "_pilotpress_level");
		
	if ( current_user_can( 'edit_posts' ) ) { // let's bypass this if you're an admin...
					
				$post_access = TRUE ;
				
				
	} elseif ($user_levels == NULL) {

				$post_access = FALSE ;
				
	} else {			
			
		$post_access = FALSE;
		
		foreach ($post_levels as $post_levels) {
				
			if ( !empty($post_levels) && !empty($user_levels) && in_array( $post_levels, $user_levels ) ) {
				
				$post_access = TRUE ;
				
			}
				
		}
	}
			

	return $post_access; 
	
}


// Admin page!
function cmwcp_admin() {
?>

<div class="wrap">
      	<?php if($_POST['cmwcp_hidden'] == 'Y') { ?>
<div class="updated"><p><strong>Options saved</strong></p></div>
		<?php } ?>
			<?php echo "<h2>" . __( 'Content Preview', 'cmwcp_trdom' ) . "</h2>"; ?>
			<p>Welcome to the Content Preview plugin's options page. blah blah blah</p>
			<form name="cmwcp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
				<input type="hidden" name="cmwcp_hidden" value="Y">

<?php // This gives you an array of membership levels activated in $unique_membership_levels
		
		$post_id_array = array(); // Want an array of ALL your post IDs? 
		
			// The Query
			$the_query = new WP_Query('posts_per_page=-1');
			
				// The Loop
				while ( $the_query->have_posts() ) : $the_query->the_post();
					
					$id = get_the_ID();
					$post_id_array[] = $id;
					
				endwhile;
			
				// Reset Post Data
			wp_reset_postdata();
			/* */
				
		$post_levels_array = array();
		foreach ($post_id_array as $post_id_array) {
			
			$post_levels = get_post_meta($post_id_array, "_pilotpress_level");
			
			$post_levels = preg_replace('/[\s]+/', '_', $post_levels);

			if (!empty($post_levels)) {
			
				foreach ($post_levels as $post_levels) {
					$post_levels_array[] = $post_levels;
				}
			}
			
			
		}
		
		$unique_membership_levels = array_unique($post_levels_array);
		
foreach ( $unique_membership_levels as $unique_membership_levels ) {		
	
	// What to do with posted data
		if($_POST['cmwcp_hidden'] == 'Y') {
			//Form data sent
			
			$cmwcp_top_warning = $_POST[$unique_membership_levels.'_cmwcp_top_warning'];
			update_option($unique_membership_levels.'_cmwcp_top_warning', $cmwcp_top_warning);
			$cmwcp_top_warning = stripslashes($cmwcp_top_warning);
			
			$cmwcp_bottom_warning = $_POST[$unique_membership_levels.'_cmwcp_bottom_warning'];
			update_option($unique_membership_levels.'_cmwcp_bottom_warning', $cmwcp_bottom_warning);
			$cmwcp_bottom_warning = stripslashes($cmwcp_bottom_warning);
			
			$cmwcp_membership_rank = $_POST[$unique_membership_levels.'_'.'cmwcp_membership_rank'];
			$cmwcp_membership_rank = trim($cmwcp_membership_rank);
			update_option($unique_membership_levels.'_'.'cmwcp_membership_rank', $cmwcp_membership_rank);
			
		} else {
			
		//  Normal page display $unique_membership_levels_cmwcp_top_warning
			$cmwcp_top_warning = get_option($unique_membership_levels.'_cmwcp_top_warning');
			$cmwcp_top_warning = stripslashes($cmwcp_top_warning);
			
			$cmwcp_bottom_warning = get_option($unique_membership_levels.'_cmwcp_bottom_warning');
			$cmwcp_bottom_warning = stripslashes($cmwcp_bottom_warning);
			
			$cmwcp_membership_rank = get_option($unique_membership_levels.'_'.'cmwcp_membership_rank');
		
		}
			
			$cmwcp_nicename = preg_replace('/_/',' ',$unique_membership_levels); // to make things nice :)

?>
    
               <div style="border:#666 thin;"> 
                 <h3><?php echo $cmwcp_nicename; ?> Options</h3>
                 	
                 <p><input type="text" size="5" name="<?php echo $unique_membership_levels.'_'.'cmwcp_membership_rank' ;?>" id="<?php echo $unique_membership_levels.'_'.'cmwcp_membership_rank' ;?>" value="<?php echo $cmwcp_membership_rank; ?>" />&nbsp;<b>Ranking</b> - This is used to determine which warning will show up on a post with more than one membership level. Only the highest ranked (lowest number) of the membership will be displayed.</p>
                        
                <p><b><?php _e("Top Warning" ); ?></b> - This is displayed before the main post content, letting people know that they don't have access. HTML can be used here. <br>
                <textarea rows="1" cols="40" name="<?php echo $unique_membership_levels.'_cmwcp_top_warning' ;?>" tabindex="6" style="margin:0;height:8em;width:98%;" id="<?php echo $unique_membership_levels.'_cmwcp_top_warning' ;?>" /><?php echo $cmwcp_top_warning; ?></textarea></p>
                
                <p>&nbsp;</p>
                
                <p><b><?php _e("Bottom Warning" ); ?></b> - This is displayed after the main post content, letting people know that they don't have access. HTML can be used here. <br>
                <textarea rows="1" cols="40" name="<?php echo $unique_membership_levels.'_cmwcp_bottom_warning' ;?>" tabindex="6" style="margin:0;height:8em;width:98%;" id="<?php echo $unique_membership_levels.'_cmwcp_bottom_warning' ;?>" /><?php echo $cmwcp_bottom_warning; ?></textarea></p>
                
                        
<?php } ?>		
		<?php submit_button('Update Options'); ?>
			</form>
            </div>

<?php 

}

// The End! ?>