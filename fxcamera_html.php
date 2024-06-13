<style>
#cameramain {display: none;}
#cameramain, #camera--view, #camera--sensor, #camera--output, .cameramain{
    position: fixed;
    height: 100%;
    width: 100%;
    object-fit: cover;
    top: 0;
    left: 0;
    z-index: 9999;
    background: #353535;
}
#camera--view, #camera--sensor
{
    height: 66% !important;
}
#camera--view, #camera--sensor, #camera--output
{
  /*  transform: scaleX(-1);
    filter: FlipH;*/
}
.cambuttons{
    width: 36px;
    background-color: transperant;
    color: white;
    border-radius: 30px;
    position: fixed;
    bottom: 10px;
    z-index: 9999;
    height: 36px;
    box-shadow:0px 0px 0px 1px #fff;
}
#camera--trigger
{
	left: 47%;
}
#camera--front
{
	right: 3%;
}
span#camera--çlose {
    left: 3%;
}
.cambuttons i{
    width: 100%;
    padding-top: 3px;
    font-size: 30px;

}
.taken{
    /*height: 100px!important;
    width: 100px!important;*/
    transition: all 0.5s ease-in;
    border: solid 3px white;
    box-shadow: 0 5px 10px 0 rgba(0,0,0,0.2);
    top: 20px;
    right: 20px;
    z-index: 999999999 !important;
}

.cambottom
{
    width: 100%;
    height: 60px;
    background: #353535;
    z-index: 9999999999;
    position: absolute;
    bottom: 0;
    left:0;
}

#fxcamera_form
{
    max-width: 400px;
}

#fxcamera_form input
{
    background: #fff;
    width: 100%;
    margin: 5px;
}
.redmsg
{
    color: red;
}
#fxcamgal figure {
    width: 100px;
    display: inline-flex;
    padding: 5px;
    height: 100px;
}
#fxcamgal img
{
    max-height: 250px;
}
button.pswp__button.pswp__button--delete {
    background: none;
    background-color: red !important;
    width: 70px;
    color: #fff;
}
.pswp__top-bar .pswp__button
{
    background-color: #4c4c4c !important;
}
.pswp__top-bar, .pswp__button {
    opacity:1 !important;
}
.dashicons.spin {
   animation: dashicons-spin .8s infinite;
   animation-timing-function: linear;
}

@keyframes dashicons-spin {
   0% {
      transform: rotate( 0deg );
   }
   100% {
      transform: rotate( 360deg );
   }
}
#camera--loader {
    left: 42%;
    background-color: transperant;
    color: white;
    position: fixed;
    bottom: 10px;
    z-index: 9999;
    display: none;
}

#camera--loader i {
    width: 65px;
    height: 65px;
    font-size: 65px;
}

</style>

<?php 

$user = wp_get_current_user();

if(empty($user->ID))
{
?>



<h3>Camera Gallery</h3>

<div id="fxcamera_form">

	<p>Enter old email and password if you are already a user otherwise a new user will be created for you</p>

	<p><input type="email" id="fxemail" name="fxemail" placeholder="Enter email" value="" autocomplete="off"></p>

    <?php if(empty($atts['emailonly'])) { ?>
	<p><input type="password" id="fxpassword" name="fxpassword" placeholder="Enter password" value="" autocomplete="off"></p>
    <?php } ?>
	
    <p><button id="fxemailsubmit" style="display: inherit;">Submit</button></p>

	<p id="fxformmsg"></p>

</div>
<?php 
} 
else
{
    $plgpath = plugin_dir_url(__FILE__);
?>

    <button id="opencamfx">Open Camera</button>
    <link href="<?php echo $plgpath; ?>lightbox/photoswipe.css" rel="stylesheet" />
    <link href="<?php echo $plgpath; ?>lightbox/default-skin/default-skin.css" rel="stylesheet" />
    <script src="<?php echo $plgpath; ?>lightbox/photoswipe.js"></script>
    <script src="<?php echo $plgpath; ?>lightbox/photoswipe-ui-default.js"></script>
    <div id="fxcamgal">

<?php 
    global $wpdb;
    $cuser = wp_get_current_user();
    $result = $wpdb->get_results ("SELECT * FROM " . $wpdb->prefix . "pwa_camera WHERE uemail='".$cuser->user_email."' ORDER BY createddate DESC"); //latest on top
    //echo "<pre>"; print_r($result); echo "</pre>";
    $rowCount = $wpdb->num_rows;
    $imagefile="";
    $createddate="";
    $id=-1;
    $short_description = "";
    if($rowCount>0)
    {
        foreach ( $result as $key => $row )
        {
            $id = $row->id;
            $imagefile = $row->imagefile;
            $createddate = $row->createddate;
            $imURL = site_url().'/wp-content/uploads/fxcamera/'.get_current_user_id()."/".$imagefile;
            $imSmallURL = site_url().'/wp-content/uploads/fxcamera/'.get_current_user_id()."/small_".$imagefile;
            list($width, $height, $type, $attr) = getimagesize($imURL);
            $widthheight = $width."x".$height;
            echo "<figure  postid='{$id}'>
                        <a href='{$imURL}' data-size='{$widthheight}' data-index='{$key}'>
                          <img src='$imSmallURL' height='100' width='100'>
                        </a>
                    </figure>";
        }
    } 
    else 
    {
        echo 'No captured image found.';
    }
?>
</div>

<div id="cameramain">
    <canvas id="camera--sensor"></canvas>
    <!-- <img src="" id="camera--output"> -->
    <video id="camera--view" autoplay playsinline></video>
    <div class="cambottom">
        <span id="camera--loader" class="cambuttons_load"><i class="dashicons dashicons-update spin"></i></span>
        <span id="camera--çlose" class="cambuttons"><i class="dashicons dashicons-no"></i></span>
        <span id="camera--trigger" class="cambuttons"><i class="dashicons dashicons-camera"></i></span>
        <span id="camera--front" class="cambuttons" face="environment"><i class="dashicons dashicons-update"></i></span>
    </div>
</div>

<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
 
        <div class="pswp__container">
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
            <div class="pswp__item"></div>
        </div>
 
        <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <button class="pswp__button pswp__button--delete" title="Delete image">Delete</button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div> 
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>
<?php 
} 
?>



<script type="text/javascript">
jQuery(function(){
    
    var $pic = jQuery('#fxcamgal');
    getItems = function() {
        var items = [];
        $pic.find('a').each(function() {
            var $href   = jQuery(this).attr('href'),
                $size   = jQuery(this).data('size').split('x'),
                $width  = $size[0],
                $height = $size[1];

            var item = {
                src : $href,
                w   : $width,
                h   : $height
            }

            items.push(item);
        });
        return items;
    }
    var items = getItems();
    var $pswp = jQuery('.pswp')[0];
    $pic.on('click', 'figure', function(event) {
        event.preventDefault();
         
        var $index = jQuery(this).attr('postid');
        var options = {
            index: $index,
            bgOpacity: 0.7,
            showHideOpacity: true,
            bgOpacity: 1,
            removeEl: true
        }
         
        // Initialize PhotoSwipe
        const lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
        lightBox.init();
        lightBox.listen('removePhoto', function (item, index) {
            jQuery(".pswp__button--delete").text('Deleting..');
            noncee = "<?php echo  wp_create_nonce('ajax-nonce'); ?>";
            jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            data: {"imgsrc": item.src, "action": "fxdelimg", "noncee": noncee},
            success: function(msg) {
                    if(msg == "1")
                    {
                        location.reload(true);
                    }

                }

            });
        });

    });

    

    jQuery("#fxemailsubmit").click(function(){
        jQuery(this).text('Submitting..');
        fxemail = jQuery("#fxemail").val();
        fxpassword = jQuery("#fxpassword").val();
        if(fxemail == "" || fxpassword == "")
        {
            jQuery("#fxformmsg").html('<span class="redmsg">Enter email and password</span>');
        }
        else
        {

            noncee = "<?php echo  wp_create_nonce('ajax-nonce'); ?>";
            jQuery.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            data: {"fxemail": fxemail, "fxpassword": fxpassword,  "action": "fxuserauth", "noncee": noncee},
            success: function(msg) {
                    if(msg == "2")
                    {
                        location.reload(true);
                    }
                    else
                    {
                        jQuery("#fxformmsg").html('<span class="redmsg">'+msg+'</span>');
                        jQuery("#fxemailsubmit").text('Submit');
                    }

                }

            });

        }

    });

});



</script>
