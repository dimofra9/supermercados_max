<?php
/*
Plugin Name: NextGEN Scroll Gallery
Plugin URI: http://software.bmo-design.de/scrollgallery/wordpress-plugin-nextgen-scroll-gallery.html
Description: Awesome free JavaScript gallery. <a href="http://software.bmo-design.de/scrollgallery.html">BMo-Design's Mootools Javascript ScrollGallery</a> as a Plugin for the Wordpress NextGEN Gallery.
Author: Benedikt Morschheuser
Author URI: http://bmo-design.de/
Version: 1.8.2

#################################################################

The current version used the ScrollGallery 1.10. with mobile extension 
                    
#################################################################
*/ 
// Restrictions
  if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

//###############################################################
  
  define('SCROLLGALLERY_SITEBASE_URL'  , get_option('siteurl'));
  define('SCROLLGALLERY_URL', get_option('siteurl').'/wp-content/plugins/' . dirname(plugin_basename(__FILE__))); // get_bloginfo('wpurl')

//###############################################################
	
class ScrollGallery {
	
	 private $nggsgversion = "1.8.2"; //Version
	  
	 function nggScrollGalleryReplaceShortcode($atts, $content = '') { //new Version, see http://codex.wordpress.org/Shortcode_API
		global $wpdb;
		
		if(!empty($atts[0]))//fallback to old Version
			if(substr($atts[0], 0, 1)=="=")//falls erstes Zeichen ein = -> alte Version.
				return "[scrollGallery".$atts[0]."]";
		
		//neue Version:
		$sgconfig = $this->get_SGConfiguration($atts);
		
		if( !is_numeric($sgconfig["galleryID"]) )
            $id = $wpdb->get_var( $wpdb->prepare ("SELECT gid FROM $wpdb->nggallery WHERE name = '%s' ", $sgconfig["galleryID"]) );

        if( !empty($sgconfig["galleryID"]) )
            $out = $this->nggSGShow($sgconfig);
        else 
            $out = '[Gallery not found]';
			
		return $out.$content;
	 }
	 function nggScrollGalleryReplaceShortcode_thumbsSolo($atts, $content = '') { //new Version, see http://codex.wordpress.org/Shortcode_API
		global $wpdb;
		
		//neue Version:
		$sgconfig = $this->get_SGConfiguration($atts);
		
		if( !is_numeric($sgconfig["galleryID"]) )
            $id = $wpdb->get_var( $wpdb->prepare ("SELECT gid FROM $wpdb->nggallery WHERE name = '%s' ", $sgconfig["galleryID"]) );

        if( !empty($sgconfig["galleryID"]) )
            $out = $this->nggSGShow($sgconfig, null, true, false);
        else 
            $out = '[Gallery not found]';
			
		return $out.$content;
	 }
	 function nggScrollGalleryReplaceShortcode_imagesSolo($atts, $content = '') { //new Version, see http://codex.wordpress.org/Shortcode_API
		global $wpdb;
		
		//neue Version:
		$sgconfig = $this->get_SGConfiguration($atts);
		
		if( !is_numeric($sgconfig["galleryID"]) )
            $id = $wpdb->get_var( $wpdb->prepare ("SELECT gid FROM $wpdb->nggallery WHERE name = '%s' ", $sgconfig["galleryID"]) );

        if( !empty($sgconfig["galleryID"]) )
            $out = $this->nggSGShow($sgconfig, null, false, true);
        else 
            $out = '[Gallery not found]';
			
		return $out.$content;
	 }
	 
	 function nggScrollGalleryReplace($content) {//old version
		global $wpdb;
		
		$splitContent = $this->nggSGFindStringBetween($content, "[scrollGallery", "]");
		(array_key_exists(0, $splitContent) ? $begin  = $splitContent[0] :$begin="");
		(array_key_exists(1, $splitContent) ? $middle = $splitContent[1] :$middle="");
		(array_key_exists(2, $splitContent) ? $end    = $splitContent[2] :$end="");
		  
		if ($begin == $content) return $content;	
	
		// New Way [smooth=id:; width:; height:; timed:; delay:; transition:; arrows:; info:; carousel:; text:; open:; links:;]
		$middleValues = substr($middle, 0, -1); // Remove last brackets
		$middleValues = explode("=", $middleValues);
		$middleValues = explode(";", $middleValues[1]);
	
		$final = Array();
		foreach($middleValues as $value) {
			if(preg_match("/:/",$value)){
		  		list($key, $value) = explode(":", $value);
		  
		 		 if (trim($key) != "")
					$final[trim(strtolower($key))] = trim($value);
			}
		}
		$sgconfig = $this->get_SGConfiguration($final);
		
		$sgconfig["galleryID"] = $wpdb->get_var("SELECT gid FROM $wpdb->nggallery WHERE gid  = '".$sgconfig["galleryID"]."' ");
		if (! $sgconfig["galleryID"]) 
			$sgconfig["galleryID"] = $wpdb->get_var("SELECT gid FROM $wpdb->nggallery WHERE name = '".$sgconfig["galleryID"]."' ");
		if (! $sgconfig["galleryID"]) 
			return $begin . $middle . $end;
	
		if ($sgconfig["galleryID"]) {
		    $middle = $this->nggSGShow($sgconfig);
		}
	
		return $this->nggScrollGalleryReplace($begin . $middle . $end); // More than one gallery per post
	  }
	  function nggSGFindStringBetween($text, $begin, $end) {
		if ( ($posBegin = stripos($text, $begin         )) === false) return Array($text, "");
		if ( ($posEnd   = stripos($text, $end, $posBegin)) === false) return Array($text, "");
		
		$textBegin  = (string) substr($text, 0, $posBegin);
		$textMiddle = (string) substr($text, $posBegin, $posEnd - $posBegin + strlen($end) );
		$textEnd    = (string) substr($text, $posEnd + strlen($end) , strlen($text));
		return Array($textBegin, $textMiddle, $textEnd);
	  }
	  function get_SGConfiguration($final) {
		//build sgconfig from parameter and options
		$options = get_option("SG_Options");
		$sgconfig = array();      
		$sgconfig["galleryID"]        = (int)     ( (array_key_exists("id"        , $final))? $final["id"]                    :0 );
		$sgconfig["start"]            = (int)     ( (array_key_exists("start"     , $final))? $final["start"]                 : $options["SG_start"] );
		$sgconfig["area"]             = (int)     ( (array_key_exists("area"      , $final))? $final["area"]                  : $options["SG_area"] );
		$sgconfig["thumbarea"]        = (string)  ( (array_key_exists("thumbarea" , $final))? $final["thumbarea"]             : $options["SG_thumbarea"] );
		$sgconfig["imagearea"]        = (string)  ( (array_key_exists("imagearea" , $final))? $final["imagearea"]             : $options["SG_imagearea"] );
		$sgconfig["speed"]            = (string)  ( (array_key_exists("speed"     , $final))? $final["speed"]                 : $options["SG_speed"] );
		$sgconfig["clickable"]        = (bool)    ( (array_key_exists("clickable" , $final))?($final["clickable"] == 'false'?false:true): $options["SG_clickable"] );
		$sgconfig["autoScroll"]       = (bool)    ( (array_key_exists("autoscroll", $final))?($final["autoscroll"] == 'false'?false:true):  $options["SG_autoScroll"] );
		$sgconfig["enableSwipeMode"]       = (bool)    ( (array_key_exists("enableswipemode", $final))?($final["enableswipemode"] == 'false'?false:true):  $options["SG_enableSwipeMode"] );
		$sgconfig["useCaptions"]      = (bool)    ( (array_key_exists("usecaptions", $final))?($final["usecaptions"] == 'false'?false:true):  $options["SG_useCaptions"] );
		$sgconfig["useDesc"]          = (bool)    ( (array_key_exists("usedesc", $final))?($final["usedesc"] == 'false'?false:true):  $options["SG_useDesc"] );
		$sgconfig["thumbsdown"]       = (bool)    ( (array_key_exists("thumbsdown", $final))?($final["thumbsdown"] == 'false'?false:true):  $options["SG_thumbsdown"] );
		$sgconfig["diashow"]          = (bool)    ( (array_key_exists("diashow", $final))?($final["diashow"] == 'false'?false:true):  $options["SG_diashow"] );
		$sgconfig["diashowDelay"]     = (int)     ( (array_key_exists("diashowdelay"      , $final))? $final["diashowdelay"]                  : $options["SG_diashowDelay"] );
		$sgconfig["thumbOpacity"]     = (int)     ( (array_key_exists("thumbopacity"      , $final))? $final["thumbopacity"]                  : $options["SG_thumbOpacity"] );
		$sgconfig["width"]            = (int)     ( (array_key_exists("width"     , $final))? $final["width"]                 : $options["SG_width"] );
		$sgconfig["height"]           = (int)     ( (array_key_exists("height"    , $final))? $final["height"]                : $options["SG_height"] );
		$sgconfig["adjustImagesize"]  = (bool)    ( (array_key_exists("adjustimagesize", $final))?($final["adjustimagesize"] == 'false'?false:true):  $options["SG_adjustImagesize"] );
		//$sgconfig["design"]           = (string)  $options["SG_design"] ;//page should use only one design 
		//margins
		$dimensions = array("px","em","%","pt");
		$margins = explode(" ", trim(str_replace($dimensions,"",$options["SG_design_margin"])));//we only work with px
		$sgconfig["margin_top"]		=$margins[0];
		$sgconfig["margin_right"]	=$margins[1];
		$sgconfig["margin_bottom"]	=$margins[2];
		$sgconfig["margin_left"]	=$margins[3];
		
		return $sgconfig;
	  }
	  function nggSGHead() {
		$options = get_option("SG_Options");
		// As a precaution, deregister any previous 'mootools' registrations. 
		wp_deregister_script(array('mootools'));
		wp_register_script( 'mootools', SCROLLGALLERY_URL.'/scrollGallery/js/mootools-core-1.3.2-full-compat.js', false, '1.3.2');
		wp_deregister_script(array('scrollGallery'));
		wp_register_script( 'scrollGallery', SCROLLGALLERY_URL.'/scrollGallery/js/scrollGallery.js', array('mootools'), '1.12');
		wp_deregister_script(array('powertools'));
		wp_register_script( 'powertools', SCROLLGALLERY_URL.'/scrollGallery/js/powertools-mobile-1.1.1.js', array('mootools'), '1.1.1');
		
		wp_register_style('scrollGallery', SCROLLGALLERY_URL.'/scrollGallery/css/scrollGallery.css',false,$this->nggsgversion,'screen');
		wp_register_style('scrollGalleryDesign', SCROLLGALLERY_URL.'/scrollGallery/css/'.$options['SG_design'],array('scrollGallery'),$this->nggsgversion,'screen');
			
	    echo ' <!-- nextgen scrollGallery '.$this->nggsgversion.' --> 
		   ';
			  if (function_exists('wp_enqueue_style')) {
				  wp_enqueue_style('scrollGallery');
				  wp_enqueue_style('scrollGalleryDesign');
			  }
			  if (function_exists('wp_enqueue_script')) {
				wp_enqueue_script('mootools');
				wp_enqueue_script('scrollGallery');
				wp_enqueue_script('powertools');
			  }
	  }
	  function nggSGShow($sgconfig, $pictures = null, $buildThumbs = true, $buildImages = true) {	
		  global $wpdb;  
		  
		  extract($sgconfig);
		
		  // Get the pictures
		  if ($galleryID) {
			$ngg_options = get_option ('ngg_options'); //NextGenGallery Options
			$pictures    = $wpdb->get_results("SELECT t.*, tt.* FROM $wpdb->nggallery AS t INNER JOIN $wpdb->nggpictures AS tt ON t.gid = tt.galleryid WHERE t.gid = '$galleryID' AND tt.exclude != 1 ORDER BY tt.$ngg_options[galSort] $ngg_options[galSortDir] ");
					   
			$final = array();    
			foreach($pictures as $picture) {
			  $aux = array();
			  $aux["title"] = stripslashes($picture->alttext); // $picture->alttext;
			  $aux["desc"]  = $picture->description;
			  $aux["link"]  = SCROLLGALLERY_SITEBASE_URL . "/" . $picture->path ."/" . $picture->filename;
			  $aux["img"]   = SCROLLGALLERY_SITEBASE_URL . "/" . $picture->path ."/" . $picture->filename;
			  $aux["img_abs_path"]   = ABSPATH . $picture->path ."/" . $picture->filename;
			  $aux["thumb"] = SCROLLGALLERY_SITEBASE_URL . "/" . $picture->path ."/thumbs/thumbs_" . $picture->filename;
			  $serialized_data = unserialize($picture->meta_data);
			  $aux["width"]  = $serialized_data["width"];
			  $aux["height"]  = $serialized_data["height"];
			  $final[] = $aux;
			}
			
			$pictures = $final;
			
		  } else {
			$galleryID = rand();//falls pictures als parameter übergeben werden
		  }
		  
		  if (empty($pictures)) return "";
		  
		  // Build the ScrollGallery HTML
		  $out = '<script type="text/javascript">
		  			window.addEvent(\'domready\', function() {
						var scrollGalleryObj'.$galleryID.' = new scrollGallery({';
		  if(is_numeric($start)) $out .= 'start:'.$start.',';
		  if($area) $out .= 'area:'.$area.',';
		  $out .= 'thumbarea:"'.$thumbarea.'_'.$galleryID.'",';
		  $out .= 'imagearea:"'.$imagearea.'_'.$galleryID.'",';
		  if($speed) $out .= 'speed:'.$speed.',';
		  if(!$clickable) $out .= 'clickable:false,';
		  if($autoScroll) $out .= 'autoScroll:'.$autoScroll.',';
		  if($enableSwipeMode) $out .= 'enableSwipeMode:'.$enableSwipeMode.',';
		  if($useCaptions||$adjustImagesize) $out .= 'toElementClass:".caption_container",';
		  if($diashow&&$diashowDelay) $out .= 'diashowDelay:'.$diashowDelay.',';
		  if($thumbOpacity<100&&$thumbOpacity>0) $out .= 'thumbOpacity:'.$thumbOpacity.',';
		  $out = substr($out, 0, -1); // Remove last ,
		  $out .= '				
						});
					});
				</script>';
		  if($useCaptions==true||$adjustImagesize){
				 $out .= '
					<!--[if lte IE 7]>
					<style type="text/css">
					.scrollgallery .imageareaContent .caption_container{display:inline; position:static;}
					.scrollgallery .imageareaContent .caption_container div{display:none; position:relative;}</style>
					<![endif]-->
				';
		  }
		  
		  $outT  = ''; //Thumbs out	
		  if($buildThumbs==true){  
			  $outT .= '    <div id="'.$thumbarea.'_'.$galleryID.'" class="thumbarea">
								<div class="thumbareaContent">';
									foreach ($pictures as $picture){
										if ($picture["img"]) {
											$descMetaInfo=$this->nggSG_get_picdescmeta($picture["desc"]);
											$picture["desc"] = preg_replace("/\[.*\]/U",'', $picture["desc"], -1);//delete [...] in desc
										    if($descMetaInfo['thumblink']!=NULL){
												$thumblink_start='<a href='.$descMetaInfo['thumblink'].' target='.$descMetaInfo['thumblink_target'].' title="'.stripslashes($picture["desc"]).'">';
												$thumblink_end='</a>';
											}else{
												$thumblink_start='';
												$thumblink_end='';
											}
											$outT .= $thumblink_start.'<img  src="'.$picture["thumb"].'" alt="NextGen ScrollGallery thumbnail" />'.$thumblink_end;
										}
									}
			  $outT .= '
								</div> 
							</div>';
		  }
						
		  $outI  = ''; //Images out
		  if($buildImages==true){			
			  $outI .= '     <div id="'.$imagearea.'_'.$galleryID.'" class="imagearea">
								  <div class="imageareaContent">';
										foreach ($pictures as $picture){
											if ($picture["img"]) {
												if($useCaptions==true||$adjustImagesize==true){
													 $outI .= '<div class="caption_container">';
													 if($useCaptions==true&&$useDesc==false&&$picture["title"]!="")
													 	$outI .='<div>'.$picture["title"].'</div>';
													 if($useCaptions==true&&$useDesc==true&&stripslashes($picture["desc"])!="")
														$outI .='<div>'.stripslashes($picture["desc"]).'</div>';
												}
												if($adjustImagesize==true){
													/*$imgsize   = @getimagesize($picture["img"]);//0=width 1=height
													$imgwidth  = $imgsize[0];
													$imgheight = $imgsize[1];*/
													$imgwidth  = $picture["width"];
													$imgheight = $picture["height"];
													if($width>$height){//landscape
														//get new size
														$newimageheight=$height;
														$newimagewidth=($imgwidth/$imgheight)*$newimageheight;
														//check ob passt, sonst weiter verkleinern
														if($newimagewidth>$width){
															$newimagewidth2=$width;
															$newimageheight=($newimageheight/$newimagewidth)*$newimagewidth2;
															$newimagewidth=$newimagewidth2;
														}
													}else{//portrait
														//get new size
														$newimagewidth=$width;
														$newimageheight=($imgheight/$imgwidth)*$newimagewidth;
														//check ob passt, sonst weiter verkleinern
														if($newimageheight>$height){
															$newimageheight2=$height;
															$newimagewidth=($newimagewidth/$newimageheight)*$newimageheight2;
															$newimageheight=$newimageheight2;
														}
													}
													$style='width:'.($newimagewidth).'px; height:'.($newimageheight).'px; max-width:'.($newimagewidth).'px; ';
													//build Margins
													if($newimagewidth<$width){
														$style.='margin-left:'.(($width-$newimagewidth)/2+$margin_left).'px; ';
														$style.='margin-right:'.(($width-$newimagewidth)/2+$margin_right).'px; ';
													}else{
														$style.='margin-left:'.($margin_left).'px; ';
														$style.='margin-right:'.($margin_right).'px; ';
													}
													if($newimageheight<$height){
														$style.='margin-top:'.(($height-$newimageheight)/2+$margin_top).'px; ';
														$style.='margin-bottom:'.(($height-$newimageheight)/2+$margin_bottom).'px; ';
													}else{
														$style.='margin-top:'.($margin_top).'px; ';
														$style.='margin-bottom:'.($margin_bottom).'px; ';
													}
												}else{
													$style='width:'.($width).'px; height:'.($height).'px; max-width:'.($width).'px;';
												}
												$descMetaInfo=$this->nggSG_get_picdescmeta($picture["desc"]);
												$picture["desc"] = preg_replace("/\[.*\]/U",'', $picture["desc"], -1);//delete [...] in desc
												if($descMetaInfo['imglink']!=NULL){
													$imglink_start='<a href='.$descMetaInfo['imglink'].' target='.$descMetaInfo['imglink_target'].' title="'.stripslashes($picture["desc"]).'">';
													$imglink_end='</a>';
												}else{
													$imglink_start='';
													$imglink_end='';
												}
											
												$outI .= $imglink_start.'<img  src="'.$picture["img"].'" alt="'.$picture["title"].'" style="'.$style.'"/>'.$imglink_end;
												if($useCaptions==true||$adjustImagesize==true){
													 $outI .= '</div>';
												}
											}
										}
			  $outI .= '
								  </div> 
							  </div>';
		  }
		  //Build out with $thumbsdown==true or false?
		  $out .= '
			 <div id="scrollgallery_'.$galleryID.'" class="scrollgallery" style="width:'.($width+26).'px;">';
					if(!$thumbsdown==true){
						if($buildThumbs==true) 
							$out .= '<div class="scrollGalleryHead">'.$outT.'</div>';
						if($buildImages==true) 
							$out .= '<div class="scrollGalleryFoot">'.$outI.'</div>';
					}else{
						if($buildImages==true) 
							$out .= '<div class="scrollGalleryHead">'.$outI.'</div>';
						if($buildThumbs==true) 
							$out .= '<div class="scrollGalleryFoot">'.$outT.'</div>';
					}
		  $out .= '
			 </div>';
					
					
					
		  return $out;  
	  }
	  function nggSG_get_picdescmeta($picdescmeta){
		  //BSP [scrollGallery imglink="http://www.bla.com" thumblink="http://www.blub.com" imglink_target="_blank" thumblink_target="_blank"]
		  $metaArray = array("imglink_target"=>"'_self'","thumblink_target"=>"'_self'");
		  $splitContent = $this->nggSGFindStringBetween($picdescmeta, "[scrollGallery", "]");
		  (array_key_exists(0, $splitContent) ? $begin  = $splitContent[0] :$begin="");
		  (array_key_exists(1, $splitContent) ? $middle = $splitContent[1] :$middle="");
		  (array_key_exists(2, $splitContent) ? $end    = $splitContent[2] :$end="");
		  if ($begin == $picdescmeta) return NULL;	//nichts gefunden
		  $middleValues = substr($middle, 0, -1); // Remove last brackets
		  $middleValues = explode("[scrollGallery", $middleValues);
		  $middleValues = explode(" ",trim($middleValues[1]));
		  foreach($middleValues as $value) {
			 if(preg_match("/=/",$value)){
		  		list($key, $value) = explode("=", $value, 2);//2 damit nur erstes = geteilt wird
		  
		 		 if (trim($key) != "")
					$metaArray[trim(strtolower($key))] = stripslashes(trim($value));
			 }
		  }
		  return $metaArray;
	  }
	  //admin
	  function admin_menu() {  
		add_menu_page('ScrollGallery', 'ScrollGallery', 'manage_options', plugin_basename( dirname(__FILE__)), array($this, 'admin_general_page')); // add Admin Menu
	  } 
	  function admin_general_page() {
		global $wpdb;
		?>    
		<div class="wrap">
		  <h2>NextGen ScrollGallery</h2>  
           <form action="options.php" method="post" id="post" name="post">  
           <div class="metabox-holder has-right-sidebar" id="poststuff">
				<div class="inner-sidebar" id="side-info-column">
          			<?php  do_meta_boxes( plugin_basename( dirname(__FILE__)), 'side', NULL); ?>
                </div>
         		<div id="post-body">
					<div id="post-body-content">
                    	<div id="normal-sortables" class="meta-box-sortables">
                        	<div id="scrollGallery_main_box" class="postbox ">
								<?php settings_fields('nggSG_options'); ?>
                                <?php do_settings_sections('nggSG_options_section_el'); ?>
                                <div class="inside">
                                    <p><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" style="margin-left:220px"/></p>
                                    <p>&nbsp;</p>
                                    <p>To add a gallery to your post/page <br/>enter the tag <table style="background-color:#6CF; padding:4px;"><tr><td>[scrollGallery id=xxx]</td></tr></table><br/> in your text. You have to replace xxx with your gallery id like all NextGen Gallerys.</p>
                                    <p>The options can be overridden in the post/page tag. For example: <table style="background-color:#6CF; padding:4px;"><tr><td>[scrollGallery id=1 start=5 autoScroll=false thumbsdown=true]</td></tr></table></p>
                      
                                    <p>That's it ... Have fun</p>
                                </div>
                        	</div>
               			 </div>
                     </div>
                     <br class="clear"/>
                </div>
          </div>
		  </form>
		</div>
        <div class="clear"></div>
		<?php
	 }  
	 function admin_init() {
		global $scrollGallery;
		if ( !defined('NGGALLERY_ABSPATH') ) {//check if NGG Plugin is activated
			add_action('admin_notices', array($scrollGallery, 'admin_notices'));
		}
		//admin Header  
	   	wp_deregister_script(array('plusone'));
	   	wp_register_script( 'plusone', 'https://apis.google.com/js/plusone.js');
	   	if (function_exists('wp_enqueue_script')) {
			wp_enqueue_script('plusone');
		 }
		//meta boxes
		add_meta_box('scrollGallery_meta_box', 'Do you like this Plugin?', array($this, 'nggSG_like_MetaBox'), plugin_basename( dirname(__FILE__)), 'side', 'core');//add_meta_box('scrollGallery_meta_box', 'Do you like this Plugin?', array($scrollGallery, 'nggSG_like_MetaBox'), 'nextgen-scrollgallery', 'right', 'core');
		//form
		register_setting( 'nggSG_options', 'SG_Options', array($scrollGallery,'nggSG_options_validate') );
		add_settings_section('nggSG_options_section', 'ScrollGallery Options', array($scrollGallery,'nggSG_options_section_html'), 'nggSG_options_section_el');
		add_settings_field('nggSG_options_field0', 'Gallery design, see plugin-folder scrollGallery/css/', array($scrollGallery,'nggSG_options_field_html_design'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field1', 'start: (number) start at picture number ... the first picture is number 0', array($scrollGallery,'nggSG_options_field_html_start'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field2', 'area: (number) width of the area to scroll left or right', array($scrollGallery,'nggSG_options_field_html_area'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field3', 'thumbarea: (string) div class name for the thumbs', array($scrollGallery,'nggSG_options_field_html_thumbarea'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field4', 'imagearea: (string) div class name for the images', array($scrollGallery,'nggSG_options_field_html_imagearea'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field5', 'speed: (number) 0<=speed<=1 thumb scroll speed', array($scrollGallery,'nggSG_options_field_html_speed'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field6', 'clickable: (boolean) images can be clicked', array($scrollGallery,'nggSG_options_field_html_clickable'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field7', 'autoScroll: (boolean) autoscroll thumbs', array($scrollGallery,'nggSG_options_field_html_autoScroll'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field8', 'enableSwipeMode: (boolean) enable Swipe-Mode on touchable devices', array($scrollGallery,'nggSG_options_field_html_enableSwipeMode'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field9', 'useCaptions: (boolean) use captions or not (IE7 is not supported)', array($scrollGallery,'nggSG_options_field_html_useCaptions'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field10', 'useDesc: (boolean) use the image description as caption', array($scrollGallery,'nggSG_options_field_html_useDesc'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field11', 'thumbsdown: (boolean) set the thumbs under the images', array($scrollGallery,'nggSG_options_field_html_thumbsdown'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field12', 'diashow: (boolean) activates the diashow option', array($scrollGallery,'nggSG_options_field_html_diashow'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field13', 'diashowDelay: (number) delay in seconds, have to be more than 1', array($scrollGallery,'nggSG_options_field_html_diashowDelay'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field14', 'thumbOpacity: (number) optional opacity level for the tumbs in percent, have to be more than 0', array($scrollGallery,'nggSG_options_field_html_thumbOpacity'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field15', 'width: (number) gallery width', array($scrollGallery,'nggSG_options_field_html_w'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field16', 'height: (number) gallery height', array($scrollGallery,'nggSG_options_field_html_h'), 'nggSG_options_section_el', 'nggSG_options_section');
		add_settings_field('nggSG_options_field17', 'adjustImagesize: (boolean) adjust the imagesize', array($scrollGallery,'nggSG_options_field_html_adjustImagesize'), 'nggSG_options_section_el', 'nggSG_options_section');
		
		
		//BMo Expo Banner
		add_meta_box('scrollGallery_meta_box2', 'Recommendation', array($this, 'nggSG_recommendation_MetaBox'), plugin_basename( dirname(__FILE__)), 'side', 'core');
	 }
	 function nggSG_options_section_html() {
	  // echo '<p>Here you can change the global ScrollGallery options:</p>';
	 }
	 function nggSG_options_field_html_design() {
	    $options = get_option("SG_Options");
		$act_cssfile = $options['SG_design'];
		echo '<select name="SG_Options[SG_design]" onchange="this.form.submit();">';
				$csslist = $this->nggSG_get_cssfiles();
				foreach ($csslist as $key =>$a_cssfile) {
					$css_name = $a_cssfile['Name'];
					if ($key == $act_cssfile) {
						$file_show = $key;
						$selected = " selected='selected'";
						$act_css_description = $a_cssfile['Description'];
						$act_css_author = $a_cssfile['Author'];
						$act_css_version = $a_cssfile['Version'];
						$act_css_margin = $a_cssfile['ImgMargins'];//needed margins around the img
						$act_css_name = esc_attr($css_name);
					}
					else $selected = '';
					$css_name = esc_attr($css_name);
					echo "\n\t<option value=\"$key\" $selected>$css_name</option>";
				}
			
		echo "</select>
		
		<p><strong>Active design:</strong> $act_css_name<br/>
		Autor: $act_css_author<br/>
		Version: $act_css_version<br/>
		Description: $act_css_description</p>";
	 }
 	 function nggSG_options_field_html_start() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_start]' size='4' type='text' value='{$options['SG_start']}' />";
	 }
	 function nggSG_options_field_html_area() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_area]' size='4' type='text' value='{$options['SG_area']}' />";
	 }
	 function nggSG_options_field_html_thumbarea() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_thumbarea]' size='10' type='text' value='{$options['SG_thumbarea']}' />";
	 }
	 function nggSG_options_field_html_imagearea() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_imagearea]' size='10' type='text' value='{$options['SG_imagearea']}' />";
	 }
	 function nggSG_options_field_html_speed() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_speed]' size='4' type='text' value='{$options['SG_speed']}' />";
	 }
	 function nggSG_options_field_html_clickable() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_clickable]' value='1' ".($options["SG_clickable"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_autoScroll() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_autoScroll]' value='1' ".($options["SG_autoScroll"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_enableSwipeMode() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_enableSwipeMode]' value='1' ".($options["SG_enableSwipeMode"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_useCaptions() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_useCaptions]' value='1' ".($options["SG_useCaptions"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_useDesc() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_useDesc]' value='1' ".($options["SG_useDesc"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_thumbsdown() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_thumbsdown]' value='1' ".($options["SG_thumbsdown"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_diashow() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_diashow]' value='1' ".($options["SG_diashow"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_field_html_diashowDelay() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_diashowDelay]' size='4' type='text' value='{$options['SG_diashowDelay']}' />";
	 }
	 function nggSG_options_field_html_thumbOpacity() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_thumbOpacity]' size='4' type='text' value='{$options['SG_thumbOpacity']}' />";
	 }
	 function nggSG_options_field_html_w() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_width]' size='4' type='text' value='{$options['SG_width']}' />";
	 }
	 function nggSG_options_field_html_h() {
		$options = get_option("SG_Options");
		echo "<input  name='SG_Options[SG_height]' size='4' type='text' value='{$options['SG_height']}' />";
	 }
	 function nggSG_options_field_html_adjustImagesize() {
		$options = get_option("SG_Options");
		echo "<input type='checkbox' name='SG_Options[SG_adjustImagesize]' value='1' ".($options["SG_adjustImagesize"]?"checked='checked'":"")."/>";
	 }
	 function nggSG_options_validate($input) {
		
		$input['SG_design']  = (string) (isset( $input['SG_design'] ) ? htmlspecialchars(stripslashes($input['SG_design']), ENT_QUOTES, 'UTF-8') : "scrollGallery_greyDesign.css");
		$input['SG_start']  = (int) (is_numeric( $input['SG_start'] ) ? $input['SG_start'] : 0);
		$input['SG_area']  = (int) is_numeric( $input['SG_area'] ) ? $input['SG_area'] : 200;
		$input['SG_thumbarea']  = (string) (isset( $input['SG_thumbarea'] ) ? htmlspecialchars(stripslashes($input['SG_thumbarea']), ENT_QUOTES, 'UTF-8') : "thumbarea");
		$input['SG_imagearea']  = (string) (isset( $input['SG_imagearea'] ) ? htmlspecialchars(stripslashes($input['SG_imagearea']), ENT_QUOTES, 'UTF-8') : "imagearea");
		$input['SG_speed']  = (string) (isset( $input['SG_speed'] ) ? htmlspecialchars(stripslashes($input['SG_speed']), ENT_QUOTES, 'UTF-8') : "0.1");
		$input['SG_clickable']  = (bool) (isset( $input['SG_clickable'] ) ? 1 : 0);
		$input['SG_autoScroll']  = (bool) (isset( $input['SG_autoScroll'] ) ? 1 : 0);
		$input['SG_enableSwipeMode']  = (bool) (isset( $input['SG_enableSwipeMode'] ) ? 1 : 0);
		$input['SG_useCaptions']  = (bool) (isset( $input['SG_useCaptions'] ) ? 1 : 0);
		$input['SG_useDesc']  = (bool) (isset( $input['SG_useDesc'] ) ? 1 : 0);
		$input['SG_thumbsdown']  = (bool) (isset( $input['SG_thumbsdown'] ) ? 1 : 0);
		$input['SG_diashow']  	= (bool) (isset( $input['SG_diashow'] ) ? 1 : 0);
		$input['SG_diashowDelay']  = (int) is_numeric( $input['SG_diashowDelay'] ) ? $input['SG_diashowDelay'] : 4;
		$input['SG_thumbOpacity']  = (int) is_numeric( $input['SG_thumbOpacity'] ) ? $input['SG_thumbOpacity'] : 100;
		$input['SG_width']  = (int) (is_numeric( $input['SG_width'] ) ? $input['SG_width'] : 640);
		$input['SG_height']  = (int) (is_numeric( $input['SG_height'] ) ? $input['SG_height'] : 480);
		$input['SG_adjustImagesize']  = (bool) (isset( $input['SG_adjustImagesize'] ) ? 1 : 0);
		
		//save margins
		$act_cssfile = $input['SG_design'];
		$csslist = $this->nggSG_get_cssfiles();
		foreach ($csslist as $key =>$a_cssfile) {
			if ($key == $act_cssfile) {
				$input['SG_design_margin'] = (string) ((isset( $a_cssfile['ImgMargins'] )&&$a_cssfile['ImgMargins']!='') ?  htmlspecialchars(stripslashes($a_cssfile['ImgMargins'])) : "0px 0px 0px 0px");//needed margins around the img
			}
		}
		return $input;
	 }
	 function admin_notices() {
		if ( !defined('NGGALLERY_ABSPATH') ) {//check if NGG Plugin is activated
			$this->show_message("In order to use the scrollGallery, you have to install and activate the plugin <strong><a href='http://wordpress.org/extend/plugins/nextgen-gallery/' target='_blank'>NextGEN Gallery</a></strong>!");
		}
	 }
	 function show_error($message) {
		echo '<div class="wrap"><h2></h2><div class="error" id="error"><p>' . $message . '</p></div></div>';
	 }
	 function show_message($message) {
		echo '<div class="wrap"><h2></h2><div class="updated fade" id="message"><p>' . $message . '</p></div></div>';
	 }
	 /**********************************************************/
	 // ### Code from wordpress plugin NGG
	 // read in the css files
	 function nggSG_get_cssfiles() {
		
		global $cssfiles;
		
		if (isset ($cssfiles)) {
			return $cssfiles;
		}
	
		$cssfiles = array ();
		
		$plugin_root = ABSPATH.'wp-content/plugins/nextgen-scrollgallery/scrollGallery/css';
		$plugins_dir = @ dir($plugin_root);
		
		if ($plugins_dir) {
			while (($file = $plugins_dir->read()) !== false) {
				if (preg_match('|^\.+$|', $file))
					continue;
				if (is_dir($plugin_root.'/'.$file)) {
					$plugins_subdir = @ dir($plugin_root.'/'.$file);
					if ($plugins_subdir) {
						while (($subfile = $plugins_subdir->read()) !== false) {
							if (preg_match('|^\.+$|', $subfile))
								continue;
							if (preg_match('|\.css$|', $subfile))
								$plugin_files[] = "$file/$subfile";
						}
					}
				} else {
					if (preg_match('|\.css$|', $file))
						$plugin_files[] = $file;
				}
			}
		}
		if ( !$plugins_dir || !$plugin_files )
			return $cssfiles;
	
		foreach ( $plugin_files as $plugin_file ) {
			if ( !is_readable("$plugin_root/$plugin_file"))
				continue;
	
			$plugin_data = $this->nggSG_get_cssfiles_data("$plugin_root/$plugin_file");
	
			if ( empty ($plugin_data['Name']) )
				continue;
	
			$cssfiles[plugin_basename($plugin_file)] = $plugin_data;
		}
	
		uasort($cssfiles, create_function('$a, $b', 'return strnatcasecmp($a["Name"], $b["Name"]);'));
	
		return $cssfiles;
	 }
	 // parse the Header information
	 function nggSG_get_cssfiles_data($plugin_file) {
		$plugin_data = implode('', file($plugin_file));
		preg_match("|CSS Name:(.*)|i", $plugin_data, $plugin_name);
		preg_match("|Description:(.*)|i", $plugin_data, $description);
		preg_match("|Author:(.*)|i", $plugin_data, $author_name);
		if (preg_match("|Version:(.*)|i", $plugin_data, $version))
			$version = trim($version[1]);
		else
			$version = '';
		
		if (preg_match("|ImgMargins:(.*)|i", $plugin_data, $img_margins)){
			$img_margins = preg_replace('/\\\s\\\s+/', ' ',trim($img_margins[1]));//delete more than one spaces
			if (substr($img_margins, -1) == ';') { //and the ;
				$img_margins = substr($img_margins, 0, -1); 
			}  
		}else{
			$img_margins = '';
		}
		$description = wptexturize(trim($description[1]));
		$name = trim($plugin_name[1]);
		$author = trim($author_name[1]);
	
		return array ('Name' => $name, 'Description' => $description, 'Author' => $author, 'Version' => $version, 'ImgMargins' => $img_margins );
	 }
	 function nggSG_like_MetaBox(){
		 echo '<p>This plugin is developed by <br/><a href="http://www.BMo-Design.de" target="_blank">Benedikt Morschheuser</a>.<br/>Any kind of contribution would be highly appreciated. Thank you!</p>
		 <ul>
		 	<li>If you like it, please...</li>
		 	<li><a href="http://wordpress.org/extend/plugins/nextgen-scrollgallery/" target="_blank">rate it at wordpress.org</a> &diams;</li>
			<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4AWSR2J4DK2FU" target="_blank">donate my work</a> &hearts;</li>
			<li><a href="http://bmo-design.de" target="_blank">set a link to my website</a> &rarr;</li>
			<li>or give me a <g:plusone size="small"  href="http://software.bmo-design.de/scrollgallery/wordpress-plugin-nextgen-scroll-gallery.html"></g:plusone></li>
			<li>&nbsp;</li>
			<li>If you are a stylesheet-designer, send me your <a href="http://bmo-design.de/kontakt/" target="_blank">custom gallery css</a> &raquo;</li>
			<li>&nbsp;</li>
		</ul>';
	 }
	 function nggSG_recommendation_MetaBox(){
		  echo '<p><a target="_blank" href="http://wordpress.org/plugins/bmo-expo/"><img src="http://s.wordpress.org/plugins/bmo-expo/screenshot-1.png" width="100%"/></a></p>
		  <p>Thank you for downloading and using my plugin! The great success of the plugin has made me very happy. Due to the different scenarios in which the plugin was used I recognized some problems and optimization possibilities. Few months ago I decided to develop a completely new version from sketch.
Based on jQuery, with a new HTML structure, better usability and more features.
I am proudly present you the <a target="_blank" href="http://wordpress.org/plugins/bmo-expo/">BMo Expo</a>.<br/>
I look forward to your downloads and feedback.<br/>
<br/>
Best regards<br/>
Benedikt</p>';
	 }
	 function nggSGRegisterPluginLinks($links, $file) {
		$plugin = plugin_basename(__FILE__);
		if ($file == $plugin) {
			return array_merge(
				$links,
				array( sprintf( '<a href="admin.php?page=%s">%s</a>', plugin_basename( dirname(__FILE__)), __('Settings') ), '<g:plusone size="small"  href="http://software.bmo-design.de/scrollgallery/wordpress-plugin-nextgen-scroll-gallery.html"></g:plusone>', 'Switch to my new gallery-plugin <a href="http://wordpress.org/plugins/bmo-expo/" target="_blank" >BMo-Expo</a>.' )
			);
		}
		return $links;
	 }
	 //install
	 function activation() {
		$options = array(
			'SG_design'=>'scrollGallery_greyDesign.css',
			'SG_design_margin'=>'0px 0px 0px 0px',
			'SG_start'=>'0',
			'SG_area'=>'200',
			'SG_thumbarea'=>'thumbarea',
			'SG_imagearea'=>'imagearea',
			'SG_speed'=>'0.1',
			'SG_clickable'=> true,
			'SG_autoScroll'=> true,
			'SG_enableSwipeMode'=> true,
			'SG_useCaptions'=> false,
			'SG_useDesc'=> false,
			'SG_thumbsdown'=> false,
			'SG_diashow'=> false,
			'SG_diashowDelay'=> '4',
			'SG_thumbOpacity'=>'100',
			'SG_width'=>'640',
			'SG_height'=>'480',
			'SG_adjustImagesize'=> true,);
		add_option("SG_Options",$options);//It does nothing if the option already exists. 
	 }
	 function deactivation() {
		wp_deregister_script(array('mootools'));
		wp_deregister_script(array('scrollGallery'));
	 }
}

$scrollGallery = new ScrollGallery();

if (isset($scrollGallery)) {
	// Plugin installieren bei aktivate
	register_activation_hook( __FILE__,  array($scrollGallery, 'activation'));
	register_deactivation_hook(__FILE__, array($scrollGallery, 'deactivation'));
	
	add_action('admin_menu' , array($scrollGallery, 'admin_menu'));//add menu
	add_action('admin_init', array($scrollGallery, 'admin_init'));//init settings for Admin Page
	add_filter( 'plugin_row_meta', array($scrollGallery,'nggSGRegisterPluginLinks'), 10, 2 );
	//
	add_filter('the_content', array($scrollGallery, 'nggScrollGalleryReplace'));//replace content, old Version
	add_shortcode('scrollGallery', array($scrollGallery, 'nggScrollGalleryReplaceShortcode'));//replace the shortcode, everywhere, new Version
	add_shortcode('sG_thumbsSolo', array($scrollGallery, 'nggScrollGalleryReplaceShortcode_thumbsSolo'));
	add_shortcode('sG_imagesSolo', array($scrollGallery, 'nggScrollGalleryReplaceShortcode_imagesSolo'));
	//add_filter('the_excerpt', array($scrollGallery, 'nggScrollGalleryReplace'));//replace content in the_excerpt, Uncomment it on your own risk
	// Hook wp_head to add css
	add_action('wp_head'   , array($scrollGallery,'nggSGHead'),1);
}

//Deinstall (outside Class)
if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__, 'deinstall');

function deinstall() {
	delete_option("SG_Options");
}
?>