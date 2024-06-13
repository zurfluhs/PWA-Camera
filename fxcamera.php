<?php 
/*
Plugin Name: FX Camera
Description: Camera Plugin
Version:     1.0
Author:      FX Camera
*/


add_action('admin_menu', 'setup_menu');

//action: save
add_action('admin_post_nopriv_fxcamsave', 'fxcamsave');
add_action('admin_post_fxcamsave', 'fxcamsave');

//menu, submenu init
function setup_menu(){
    add_menu_page( 'Camera', 'Camera', 'manage_options', 'init', 'init' );
}

//init menu
function init(){
  //db for email configuration
  echo "<script>

    function doValidate(){
			if(document.getElementById('emailAddr').value.trim()==''){
				alert('Email Address is required!');
				return false;
			} else {
				if (/^[a-zA-Z0-9.!#$%&'*+/=?^_{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(document.getElementById('emailAddr').value)){
					return true;
				} else {
					alert('Invalid Email Address!');
				}
				return false;
			}
		}
		</script>";



  //check if email config table exists?

  global $wpdb;
	$sqlTableHeader = [];
	$sqlTableHeader[] = 'CREATE TABLE '.$wpdb->prefix.'fxcamsettings (
  	id int(11) NOT NULL AUTO_INCREMENT,
		email varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		emailbeforemessage varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		emailinputplaceholder varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		submitbuttontext varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		submittingtext varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		opencamerabuttontext varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		imageuploadingtext varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		createddate datetime DEFAULT NULL,
		lastupdated datetime DEFAULT NULL,
		UNIQUE KEY ID (id)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

	$sqlTableHeader[] = 'INSERT INTO '.$wpdb->prefix.'fxcamsettings (id, emailbeforemessage, emailinputplaceholder, submitbuttontext, submittingtext, opencamerabuttontext, imageuploadingtext)
		VALUES (
			1,
			"Enter old email and password if you are already a user otherwise a new user will be created for you", 
			"Enter Email",
			"Submit",
			"Submitting",
			"Open Camera",
			"Uploading"
		);';

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sqlTableHeader );

  global $wpdb;
	$result_email = $wpdb->get_row ("SELECT * FROM " . $wpdb->prefix . "fxcamsettings ORDER BY id DESC LIMIT 1");
	$ex_email=$result_email->email;

	//check if table exists?

	global $wpdb;
	$sqlTableHeader = 'CREATE TABLE '.$wpdb->prefix.'pwa_camera (
		id int(11) NOT NULL AUTO_INCREMENT,
		imagefile varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		uemail varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
		uuid int(11) NOT NULL,
		createddate datetime DEFAULT NULL,
		lastupdated datetime DEFAULT NULL,
		UNIQUE KEY ID (id)
	) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sqlTableHeader );
  
  echo "<h1>Email Setting</h1>";

  if(isset($_GET["act"])){

		if($_GET["act"]=="1"){

      echo '<div class="notice notice-warning is-dismissible"><p>Email address is saved successfully!</p></div>';

    }

  }

	

  echo "<p>Configure your email address here to received the PWA Image Capture:</p>";
	
	echo "<form id='emailForm' name='emailForm' method='POST' action='". esc_url(admin_url('admin-post.php')) . "?action=updatefxemail' onsubmit='return doValidate();'>";

	echo "<table cellpadding='2'>";

	echo '<tr><td><h3>Admin settings</h3></td></tr>';
	
	echo '<tr><td>*Email Address:<br>(This emil address will notify when customer upload photo)</td><td><input type="text" id="emailAddr" name="emailAddr" class="" style="width:300px;" value="'.$ex_email.'"/></td></tr>';
	
	
	echo '<tr><td><h3>Front side settings</h3></td></tr>';

	echo '<tr><td><h3>(Email screen texts)</h3></td></tr>';

	echo '<tr><td>Email input before message:</td><td><textarea name="emailbeforemessage" class="" style="width:300px;">'.$emailbeforemessage.'</textarea></td></tr>';

	echo '<tr><td>Email input placeholder text:</td><td><input type="text" id="emailinputplaceholder" name="emailinputplaceholder" class="" style="width:300px;" value="'.$emailinputplaceholder.'"/></td></tr>';

	echo '<tr><td>Submit button text:</td><td><input type="text" name="submitbuttontext" class="" style="width:300px;" value="'.$submitbuttontext.'"/></td></tr>';

	echo '<tr><td>Submitting text:</td><td><input type="text" name="submittingtext" class="" style="width:300px;" value="'.$submittingtext.'"/></td></tr>';

	echo '<tr><td>Open camera button text:</td><td><input type="text" name="opencamerabuttontext" class="" style="width:300px;" value="'.$opencamerabuttontext.'"/></td></tr>';

	echo '<tr><td><h3>(Camera gallery texts)</h3></td></tr>';

	echo '<tr><td>Submitting text:</td><td><input type="text" name="submittingtext" class="" style="width:300px;" value="'.$submittingtext.'"/></td></tr>';


	echo "<tr><td>&nbsp;</td><td></td></tr></table>";
	
	echo "<input type='submit' value='Save' class='button-primary' style='width:120px;height:40px;'/>";
	
	echo "<p>&nbsp;</p><hr /></p>&nbsp;</p>";
	

	global $wpdb;
	$result = $wpdb->get_results ("SELECT * FROM " . $wpdb->prefix . "pwa_camera ORDER BY createddate DESC", ARRAY_A); //latest on top
	//$rowCount = $wpdb->num_rows;

	$imagarr = [];
	foreach ($result as $key => $value) {
		$imURL = site_url().'/wp-content/uploads/fxcamera/'.$value['uuid']."/".$value['imagefile'];
		$imagarr[$key]['imagefile'] = "<a href='".$imURL."' target='_blank'>".$value['imagefile']."</a>";
		$imagarr[$key]['uemail'] = $value['uemail'];
		$imagarr[$key]['createddate'] = date('d F Y', strtotime($value['createddate']));
	}

  //echo "<pre>"; print_r($result); echo "</pre>";
  require_once dirname(__FILE__) . '/fxcamera_btable.php';
  $myListTable = new My_Example_List_Table();
  echo '</pre><div class="wrap"><h2>Captured Images</h2>'; 
  $myListTable->prepare_items($imagarr); 
  $myListTable->display(); 
  echo '</div>'; 
}



add_action( 'admin_post_nopriv_updatefxemail', 'updatefxemail' );
add_action( 'admin_post_updatefxemail', 'updatefxemail');
function updatefxemail(){
	if(!empty($_REQUEST["action"]) && $_REQUEST["action"] == "updatefxemail")
	{
		
			$email = $_REQUEST["emailAddr"];
			global $wpdb;
			$wpdb->update(
			$wpdb->prefix.'fxcamsettings', 
			array( 
					'email'    => $email,
					'lastupdated' => time(),
					'createddate' => time()
				),
			array(
					'id' => 1
				)
			);
			
			wp_redirect(admin_url('admin.php?page=init&act=1'));
		}
}

/*end of admin init function*/



/******* FXCam front **************/

add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );

function load_dashicons_front_end() {

  wp_enqueue_style( 'dashicons' );
  wp_enqueue_script('jquery');

}


add_action('init', 'wpse_77390_enable_media_categories' , 1);

function wpse_77390_enable_media_categories() {

   register_taxonomy_for_object_type('category', 'attachment');

}



add_action('wp_enqueue_scripts','fxcamera_add_js');

function fxcamera_add_js() {
	$adminurl = esc_url(admin_url("admin-post.php"));
	wp_enqueue_script( 'fxcamera-js', plugins_url( '/fxcamera.js', __FILE__ ), array(), time(), true);
	wp_localize_script( 'fxcamera-js', 'fxjsdata', array( 'ajax_url' => $adminurl));
	wp_enqueue_script( 'fxcameraada-js', plugins_url( '/adapter-min.js', __FILE__ ), array(), '', true);
	wp_enqueue_script( 'fxcamera-js', "https://webrtc.github.io/adapter/adapter-latest.js", array(), '', true);
}

//AJAX Actions
add_action("wp_ajax_fxdelimg", "fxdelimg");
function fxdelimg()
{
	if ( wp_verify_nonce( $_REQUEST['noncee'], 'ajax-nonce' ) )
	{
		$filename = basename($_REQUEST['imgsrc']);
		$filename_clean = str_replace(".jpg", "", $filename);

		$path_full = str_replace(site_url()."/", ABSPATH, $_REQUEST['imgsrc']);
		$uplpath = wp_upload_dir();
		$path_small = $uplpath['basedir'].'/fxcamera/'.get_current_user_id()."/small_".$filename;

		$cuser = wp_get_current_user();

		global $wpdb;
    $table_name = $wpdb->prefix . 'pwa_camera';
    $wpdb->query( 
									 $wpdb->prepare( "DELETE FROM $table_name WHERE imagefile = %s AND uemail = %s", $filename, $cuser->user_email )
									);
		unlink($path_full);
		unlink($path_small);
	}
	die('1');
}

add_action("wp_ajax_nopriv_fxuserauth", "fxuserauth");
function fxuserauth()
{
	if ( wp_verify_nonce( $_REQUEST['noncee'], 'ajax-nonce' ) )
	{
		if(is_email($_POST['fxemail']) == false)
		{
			die('Enter valid email address');
		}
		$passkey = "fxuserpass";
		if($_POST['fxpassword'])
		{
			$passkey = $_POST['fxpassword'];
		}
		$info = array();
		$info['user_login'] = $_POST['fxemail'];
		$info['user_password'] = $passkey;
		$info['remember'] = true;
		
		$user_signon = wp_signon( $info, false );
		
    if ( !is_wp_error($user_signon) )
		{
			wp_set_current_user($user_signon->ID);
			wp_set_auth_cookie($user_signon->ID, true);
			die("2");
    }
		else
		{
			if($user_signon->errors['incorrect_password'] && !empty($_POST['fxpassword']))
			{
				echo $user_signon->errors['incorrect_password'][0];
				die();
			}

			$isaluser = "";
			if($user_signon->errors['empty_password'] || $user_signon->errors['incorrect_password'])
			{
				$onlyemailuser = get_user_by('email', $_POST['fxemail']);
				if(!empty($onlyemailuser->data->ID))
				{
					wp_set_current_user($onlyemailuser->data->ID);
					wp_set_auth_cookie($onlyemailuser->data->ID, true);
					die("2");
				}
				else
				{
					$isaluser = "yes";
				}
				//echo "<pre>"; print_r($onlyemailuser); echo "</pre>"; die();
			}
			
			
			if($user_signon->errors['invalid_username'] || $user_signon->errors['invalid_email'] || $isaluser == "yes")
			{

    		$user_data = array(

			      'user_login' => stripcslashes($_POST['fxemail']),
						'user_email' => stripcslashes($_POST['fxemail']),
						'user_pass' => $passkey,
						'user_nicename' => $user_nice_name,
						'display_name' => $new_user_first_name,
						'role' => 'subscriber',
						'show_admin_bar_front' => "false"
				);

			  $user_id = wp_insert_user($user_data);

				$info = array();
				$info['user_login'] = $_POST['fxemail'];
				$info['user_password'] = $passkey;
				$info['remember'] = true;
				$user_signon = wp_signon( $info, false );
				die("2");
			}
			die("3");
		}
	}
	die("1");
}

function fxcamera($atts) {
	ob_start();
	require_once dirname(__FILE__) . '/fxcamera_html.php';
	return ob_get_clean();

}

add_shortcode('fxcamera', 'fxcamera');



function fxcamsave(){

	if(isset($_GET["action"])){

		if($_GET["action"]=="fxcamsave"){

							$cuser = wp_get_current_user();
							$uplpath = wp_upload_dir();
							$data = $_POST["dataURL"];
							//$data = 'data:image/png;base64,AAAFBfj42Pj4';
							list($type, $data) = explode(';', $data);
							list(, $data)      = explode(',', $data);
							$data = base64_decode($data);
							$imFile = 'image-'.md5(uniqid()).'.jpg';

							$fpath = $uplpath['basedir'].'/fxcamera/'.get_current_user_id();
							if (!file_exists($fpath)) {
							    mkdir($fpath, 0777, true);
							}

							file_put_contents($uplpath['basedir'].'/fxcamera/'.get_current_user_id()."/".$imFile, $data);
							$image = wp_get_image_editor($uplpath['basedir'].'/fxcamera/'.get_current_user_id()."/".$imFile);
							if ( ! is_wp_error( $image ) ) {
							    $image->resize( 300, 300, true );
							    $image->save($uplpath['basedir'].'/fxcamera/'.get_current_user_id()."/small_".$imFile);
							}
							global $wpdb;
							$wpdb->insert(

              $wpdb->prefix.'pwa_camera', 
							array( 
								'imagefile' => $imFile,
								'uemail' => $cuser->user_email,
								'uuid' => $cuser->ID,
								'lastupdated' => current_time('mysql', 1),
								'createddate' => current_time('mysql', 1)
							)
						);

            $record_id = $wpdb->insert_id;

            global $wpdb;
						$result_email = $wpdb->get_row ("SELECT * FROM " . $wpdb->prefix . "fxcamsettings ORDER BY id DESC LIMIT 1");
						$ex_email=$result_email->email;

						$imURL = site_url().'/wp-content/uploads/fxcamera/'.$cuser->ID."/".$imFile;
						$content = "Hello admin <br>";
						$content .= "A customer uploaded a new photo on the site <br>";
						$content .= "<a href='".$imURL."' target=_blank>Click Here</a> to check the photo";

						$content_type = function() { return 'text/html'; };
  					add_filter( 'wp_mail_content_type', $content_type );
            wp_mail( $ex_email, "New photo uploaded", $content );
            remove_filter( 'wp_mail_content_type', $content_type );

        }

    }

}



?>
