<?php
/*
 * Plugin Name: Lab Posts
 * Description: Provides the shortcodes for displaying and capturing lab posts. Designed by the NL STEM team.
 * Author: Cody Blankenship
 */
add_action('init', 'register_script');
function register_script() {
	wp_register_style( 'lab_post_css', plugins_url('/css/display_lab_post.css', __FILE__), false, '1.0.2', 'all');	//register css
	wp_register_script( 'lab_post_js', plugins_url('/js/lab_posts.js', __FILE__), array( 'jquery'), false, true);					//register js and jQuery
	wp_localize_script( 'lab_post_js', 'ajaxlabpost', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	//used to get path to admin-ajax.php
}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){
	wp_enqueue_style( 'lab_post_css' );
	wp_enqueue_script('lab_post_js');
	//wp_enqueue_script('jquery');
}


function display_lab_post(){
	global $wpdb;		//database connection
	global $post;
	$lab = $wpdb->get_row("SELECT * FROM lab_posts WHERE POST_ID = ".get_the_id(), ARRAY_N); //object storing the lab
	//$wpdb->show_errors();
	$rows =  $wpdb->get_results( "SELECT * FROM lab_posts WHERE POST_ID = ".get_the_id());
	//$wpdb->print_error();
	$author = $wpdb->get_row("SELECT * FROM wp_users WHERE ID = ".$post->post_author);
		
	/*$numofTabs = 0;
	for($x = 2; $x < count($lab); $x=$x+2){
		if($lab[$x] == "n/a") break;
		$numOfTabs++;
	}*/

	/* Used to generate id's for each visual editor	*/
	$f = new NumberFormatter("en", NumberFormatter::SPELLOUT); 	//object used to convert num to word
	$txt_editor = [];						//array of text editors
	$tablink = [];
	$tabcontent = [];
	$x = 0;
	foreach ($rows as $row){
	//for($x = 1; $x < $numOfTabs+1; $x++){
		$x++;
		$num = $f->format($x);
		//$tabC = (2 * ($x-1)) + 2;
		//$tabL = (2 * ($x-1)) + 3;
		/***************tab links****************/
		//$temp = '<button class="tablinks" onclick="openCity(event, \'nl'.$num.'\')">'.$lab[$tabL].'</button>';
		$temp = '<button class="tablinks" onclick="openCity(event, \'nl'.$num.'\')">'.$row->tab_title.'</button>';
		array_push($tablink, $temp);
		/***************tab content**************/
		//$temp = '<div id = "nl'.$num.'" class = "tabcontent"><h3>spacer</h3>'.$lab[$tabC].'</div>';
		$temp = '<div id = "nl'.$num.'" class = "tabcontent"><h3>spacer</h3>'.$row->tab_content.'</div>';
		array_push($tabcontent, $temp);
	}

	$result = '<h1 id = "title">'.get_the_title().'</h1>
	<div class = "author_box">
	  <img src="avatar.jpg" id = "avatar">
	  <p>'.$author->display_name.'</p>

	  <div style = "flex-basis: 10000px;"></div>

	  <p style = "flex-shrink: 0; padding-right: 100px;">'.get_the_date().'</p>
	</div>

	<div class = "content">
	<div class="tab">';
	foreach($tablink as $link) {$result = $result.$link;}
	//for($x = 0; $x < $numOfTabs; $x++) {$result = $result.$tablink[$x];}
	$result = $result.'</div>';
	foreach($tabcontent as $content) {$result = $result.$content;}
	//for($x = 0; $x < $numOfTabs; $x++) {$result = $result.$tabcontent[$x];}

	$result = $result. '</div>';
	return $result;


	$output = 				//the html return value
	'<h1 id = "title"> Title</h1>
	<div class = "author_box">
	  <img src="avatar.jpg" id = "avatar">
	  <p>'.$author->display_name.'</p>

	  <div style = "flex-basis: 10000px;"></div>

	  <p style = "flex-shrink: 0; padding-right: 100px;"> January 1, 2022 </p>
	</div>

	<div class = "content">
	  <div class="tab">
	    <button class="tablinks" onclick="openCity(event, \'1\')" id="defaultOpen">1</button>
	    <button class="tablinks" onclick="openCity(event, \'2\')">2</button>
	    <button class="tablinks" onclick="openCity(event, \'3\')">3</button>
	  </div>
	  <div id="1" class="tabcontent">
	    <h3>1</h3>
	    <p>1 is the capital city of England.</p>
	  </div>

	  <div id="2" class="tabcontent">
	    <h3>2</h3>
	    <p>2 is the capital of France.</p>
	  </div>

	  <div id="3" class="tabcontent">
	    <h3>3</h3>
	    <p>3 is the capital of Japan.</p>
	  </div>
	</div>';
 	
	return $output;

}
add_shortcode('display_lab', 'display_lab_post');




function show_form_lab_post(){

	$numOfTabs = 6;

	/* Used to generate id's for each visual editor	*/
	$f = new NumberFormatter("en", NumberFormatter::SPELLOUT); 	//object used to convert num to word
	$txt_editor = [];						//array of text editors
	$tablink = [];
	$tabcontent = [];
	for($x = 1; $x < $numOfTabs+1; $x++){
		/***************txt editor***************/
		ob_start();
		$num = $f->format($x);
		wp_editor($description, 'textarea'.$num);
		$temp = ob_get_clean();
		array_push($txt_editor, $temp);
		/***************tab links****************/
		$temp = '<button class="tablinks unused_tab" onclick="openCity(event, \'nl'.$num.'\')"><input type = "text" id = "tablink'.$num.'" name = "tablink'.$num.'"> </button>';
		array_push($tablink, $temp);
		/***************tab content**************/
		$temp = '<div id = "nl'.$num.'" class = "tabcontent"><h3>spacer</h3>'.$txt_editor[$x-1].'</div>';
		array_push($tabcontent, $temp);
	}
	$tablink[0] = '<button class="tablinks" id = "defaultOpen" onclick="openCity(event, \'nlone\')"><input type = "text" id="tablinkone" name = "tablinkone"></button>';


	$result = '<button onclick="ajaxLabPost(tablinkone.value, tablinktwo.value, tablinkthree.value, tablinkfour.value, tablinkfive.value, tablinksix.value, \'publish\')">submit</button>
	<button onclick="ajaxLabPost(tablinkone.value, tablinktwo.value, tablinkthree.value, tablinkfour.value, tablinkfive.value, tablinksix.value, \'preview\')">preview</button>
		  <div class = "content">
		  <div class="tab">';
	for($x = 0; $x < $numOfTabs; $x++) {$result = $result.$tablink[$x];}
	$result = $result.'<button class="tablinks" onclick="newTab()" id="new_tab_btn">+</button>';
	for($x = 0; $x < $numOfTabs; $x++) {$result = $result.$tabcontent[$x];}

	$result = $result. 
		'<div id = "nlChooseThumbnail"></div></div>
 		   </div>';
 	return $result;


	$new = '<div class = "content">
	          <div class="tab">
	            <button class="tablinks" onclick="openCity(event, \'1\')" id="firstTab"><input type= "text"></button>
	    	    <button class="tablinks unused_tab" onclick="openCity(event, \'2\')"><input type = "text"></button>
	    	    <button class="tablinks unused_tab" onclick="openCity(event, \'3\')"><input type = "text"></button>
	    	    <button class="tablinks" onclick="newTab()" id="new_tab_btn">+</button>
	  	    <div id="1" class="tabcontent">
	              <h3>1</h3>
		      <p>1 is the capital city of England.</p>
	  	   </div>
	  	    <div id="2" class="tabcontent">
	    	      <h3>2</h3>
	    	      <p>2 is the capital city of England.</p>
	    	    </div>
	  	    <div id="3" class="tabcontent">
	    	      <h3>3</h3>
	    	      <p>3 is the capital city of England.</p>
	    	    </div>';
	$temp= '<div class = "content">
		    <button class = "tablinks">1</button>
		</div>';
	$output= '<div class = "content">
	  <div class="tab">
	    <button class="tablinks" onclick="openCity(event, \'1\')" id="defaultOpen">1</button>
	    <button class="tablinks unused" onclick="openCity(event, \'2\')">2</button>
	    <button class="tablinks unused" onclick="openCity(event, \'3\')">3</button>
	    <button class="tablinks" onclick="addNewTab()">+</button>
	  </div>
	  <div id="1" class="tabcontent">
	    <h3>1</h3>
	    <p>1 is the capital city of England.</p>
	  </div>

	  <div id="2" class="tabcontent">
	    <h3>2</h3>
	    <p>2 is the capital of France.</p>
	  </div>

	  <div id="3" class="tabcontent">
	    <h3>3</h3>
	    <p>3 is the capital of Japan.</p>
	  </div>
	</div>';

	return $new;
}

function lab_post_callback(){
	global $wpdb;	//db connection
	$tablename = 'lab_posts';

	$link1 = $_POST['tablinkone'];
	$link2 = $_POST['tablinktwo'];
	$link3 = $_POST['tablinkthree'];
	$link4 = $_POST['tablinkfour'];
	$link5 = $_POST['tablinkfive'];
	$link6 = $_POST['tablinksix'];
	$links = array($link1, $link2, $link3, $link4, $link5, $link6);

	$text1 = $_POST['textareaone'];
	$text2 = $_POST['textareatwo'];
	$text3 = $_POST['textareathree'];
	$text4 = $_POST['textareafour'];
	$text5 = $_POST['textareafive'];
	$text6 = $_POST['textareasix'];

	$featImage = $_POST['featuredimage'];

	$postStatus = $_POST['post_status'];

	$oPostStatus = $_POST['post_status'];
	if($oPostStatus == 'save' || $oPostStatus == 'autosave' || $oPostStatus == 'preview') $oPostStatus = 'draft';

	$texts = array($text1, $text2, $text3, $text4, $text5, $text6);
	/*******************create new post in db**********************************/
	$new_post = array(
		'post_title'   => 'New post',
		'post_content' => '<!-- wp:shortcode -->
				     [display_lab]
				  <!-- /wp:shortcode -->',
		'post_status'   => $oPostStatus 
	);
	$post_id = wp_insert_post($new_post);
	add_post_meta($post_id, '_wp_page_template', 'wpb-single-post.php'); //sets the post template
	/*************************************************************************/

	/*************insert data into db*****************************************/

	$htmlDom = new DOMDocument;
	for($x = 0; $x < count($links); $x++){
		if($links[$x] == "") break;
		$texts[$x] = wp_kses_stripslashes($texts[$x]);

		$htmlDom->loadHTML($texts[$x]);
		$images = $htmlDom->getElementsByTagName('img');

		foreach($images as $image){
			$desc = "test";
    			$imgSrc = $image->getAttribute('src');				//get url of image
			$newSrc = media_sideload_image($imgSrc, $post_id, $desc, 'id');	//upload image 
			preg_replace($imgSrc, $newSrc, $texts[$x]);			//set new src in code
			if($imgSrc == $featImage) set_post_thumbnail( $post_id, $newSrc );	//set featured imaged
		}

		$wpdb->insert($tablename, array('POST_ID' => $post_id, 'tab_content' => $texts[$x], 'tab_title' => $links[$x], 'tab_number' => $x));
	}
//	$wpdb->insert($tablename, array('POST_ID' => $post_id, 'section1' => $text1, 'section1_title' => $link1, 'section2' => $text1, 'section2_title' => $link2));
	/*************************************************************************/

	if($postStatus == 'publish'){
		$results = '0';
	} else if($postStatus== 'preview'){
		$results = '1'.get_permalink($post_id);
	} else if($postStatus== 'save'){
		$results = '2';
	} else if($postStatus== 'autosave'){
		$results = '3'; 
	}
	die($results);
	//$result = $post_id.' '.$imgSrc.' success!';
	//die($imgSrc);
}
add_action( 'wp_ajax_nopriv_lab_post_callback', 'lab_post_callback');
add_action( 'wp_ajax_lab_post_callback', 'lab_post_callback');


add_shortcode('capture_lab', 'show_form_lab_post');



function remove_media_tab( $strings ) {
    if( !current_user_can( 'administrator' ) ) {
         $strings["createGalleryTitle"] = "";
         $strings["setFeaturedImageTitle"] = "";
         $strings["insertFromUrlTitle"] = "";
         $strings['createPlaylistTitle'] = "";
         $strings['createVideoPlaylistTitle'] = "";
    }
    return $strings;
}
add_filter( 'media_view_strings', 'remove_media_tab' );		// used to hide unwanted tabs in add media
 

add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );	// Used to Restrict Media Library Access to User’s Own Uploads
function wpb_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts') ) {
        $query['author'] = $user_id;
    }
    return $query;
}


add_action('admin_init', 'allow_contributor_uploads');		// allows contributers to upload files

function allow_contributor_uploads() {
     $contributor = get_role('contributor');
     $contributor->add_cap('upload_files');
}

function tinymce_init() {
    // Hook to tinymce plugins filter
    add_filter( 'mce_external_plugins', 'tinymce_plugin' );
}



add_filter('init', 'tinymce_init');			//used to add js to rich editors
function tinymce_plugin($init) {
    $init['keyup_event'] = plugins_url() . '/lab_posts/js/lab_post_featuredimg.js';
    return $init;
}

add_filter( 'mce_buttons', 'jivedig_remove_tiny_mce_buttons_from_editor');
function jivedig_remove_tiny_mce_buttons_from_editor( $buttons ) {
    $remove_buttons = array(
        'link',
        'unlink',
    );
    foreach ( $buttons as $button_key => $button_value ) {
        if ( in_array( $button_value, $remove_buttons ) ) {
            unset( $buttons[ $button_key ] );
        }
    }
    return $buttons;
}
?>
