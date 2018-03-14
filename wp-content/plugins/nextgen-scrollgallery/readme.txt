=== Plugin Name ===
Contributors: bmodesign2
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4AWSR2J4DK2FU
Tags: nextgen, scrollgallery, scroll-gallery, bmo-design, picture, pictures, photo, photos, widgets, photo-albums, post, posts, page, admin, media, scroll, gallery, image, images, slideshow, galerie, mootools, javascript, next gen, next, generation, Style
Requires at least: 3.1
Tested up to: 3.2.2
Stable tag: trunk

Awesome free JavaScript gallery. BMo-Design's Mootools Javascript ScrollGallery as a Plugin for the Wordpress NextGEN Gallery.

== Description ==

**Nextgen Scroll Gallery**: A Wordpress Plugin that allows you to use the awesome Mootools **ScrollGallery from BMo-design** on your **NextGen-Gallery** galleries.

If you want to to use this Plugin on your blog, you need to use the very cool Wordpress gallery manager: [NextGen Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/). So install the NextGENeration Gallery Plugin and upload some pictures. Copy the '[scrollGallery id=xxx]' tag in your post an replace 'xxx' with the NextGEN Gallery id, for example with 1.

After the installation, you will find an admin interface, with which you can adjust the plugin settings.

This gallery do not use flash, so no extra browser plugin is required and search engines can crawl your content easily.

If you have some questions or you need instructions look at [BMo-Design - Nextgen Scroll Gallery](http://software.bmo-design.de/scrollgallery/wordpress-plugin-nextgen-scroll-gallery.html). There you will find instructions and discussions, which can help you.

If you want a special adaptation to the needs of your page, you can commission me at [BMo-Design](http://BMo-design.de/kontakt/).

This is stable version 1.8.0. Since the code of NextGEN Gallery was massivly changed in version 2.0.X, I decided to develop a new version of my plugin. You can download it at [http://wordpress.org/plugins/bmo-expo/](http://wordpress.org/plugins/bmo-expo/). 




== Screenshots ==

1. Screenshot NextGen Scroll Gallery Example - Design Photobook
2. Screenshot NextGen Scroll Gallery Example - Design Shadow
3. Screenshot NextGen Scroll Gallery Example - Design Shadow - portrait mode
4. Screenshot NextGen Scroll Gallery Example
5. Screenshot NextGen Scroll Gallery Example
6. Screenshot NextGen Scroll Gallery Example - Admin interface

== Installation ==

1. 	Download, upload & activate the NextGen-Gallery plugin 
2. 	Download, upload & activate this plugin 
3.  Add a NextGen Gallery and upload some images (the main gallery folder must have write permission)
4. 	Go to your post/page an enter the tag '[scrollGallery id=xxx]' you have to replace xxx with your Gallery Id.

That's it ... Have fun

== Options ==
More Options can be set in the post/page tag:
'[scrollGallery id=xxx option1="value1" option2="value2"]'

= Possibel Options =

* start: (number) start at picture number ... the first picture is number 0
* area: (number) width of the area to scroll left or right
* thumbarea: (string) div class name for the thumbs
* imagearea: (string) div class name for the images
* speed: (number) 0<=speed<=1 thumb scroll speed
* clickable: (boolean) images can be clicked
* autoScroll: (boolean) autoscroll thumbs
* enableSwipeMode: (boolean) enable Swipe-Mode on touchable devices
* useCaptions: (boolean) use captions or not
* useDesc: (boolean) use description as caption
* thumbsdown: (boolean) set the thumbs under the images
* diashow: (boolean) activates the diashow option
* diashowDelay: (number) delay in seconds, have to be more than 1
* thumbOpacity: (number) optional opacity level for the tumbs in percent, have to be more than 0
* width: (number) gallery width
* height: (number) gallery height
* adjustImagesize: (boolean) if false it will strech the images

For Example: '[scrollGallery id=1 start=5 autoScroll=true]'

Of Course you can change the style, borders and colors! Go into the plugin-folder "nextgen-scrollGallery" there you will find a folder "css" and the "scrollGallery_greyDesign.css".
Here you can add custom css designs, like the "scrollGallery_greyDesign.css".
I recommend to copy your "scrollGallery_customDesign.css" in your theme folder. Because if you make a update of the plugin, the "css" folder will be replaced.
After you save a css-file in this "css" folder, it will be possible to change the style of the gallery in the admin interface.

The dimensions of thumbnails and images can be changed in your NextGEN Gallery plugin.

Since version 1.5 the do_shortcode() function is supported and since version 1.6 you can use [sG_thumbsSolo id=xxx] and [sG_imagesSolo id=xxx] instead of [scrollGallery id=xxx] to show only thumbs or images.

Since version 1.7 it is possible to set links to thumbs and images. To set a link, go into your NextGen Gallery and edit the image descriptions. If you write: [scrollGallery thumblink="http://www.bmo-design.de" thumblink_target="_blank"] you will set an external link to my page, which will open in a new window (target="_blank").
Also you can set image links and image targets. Here's an example with all the possibilities: [scrollGallery imglink="http://www.bmo-design.de" thumblink="http://software.bmo-design.de" imglink_target="_self" thumblink_target="_blank"].

== Frequently Asked Questions ==

= Have a question? =

 * Ask the forum: [forum](http://wordpress.org/tags/nextgen-scrollgallery/).
 * Look at [BMo-Design - Nextgen Scroll Gallery](http://software.bmo-design.de/scrollgallery/wordpress-plugin-nextgen-scroll-gallery.html), you will find instructions and discussions, which can help you.
 * You also can commission me at [BMo-Design](http://bmo-design.de).

= Compatibility Problems =

Is not compatible with the Plugin *Mootools Framework* by Nils K. Windisch, *Multibox* by phatfusion and the *NextGEN Smooth Gallery* by Bruno Guimaraes. It's not compatible with the Theme sliding-door. 
Because the plugins and theme do not use the wp_enqueue_script() function.
If you like to use the sliding-door theme. I fixed the problem. If you need the solution write me at [BMo-Design](http://bmo-design.de).

= Developer tips =

 * If you are a wordpress plugin developer and you want to know how I integrated the Google+ Button into my plugin, you can read the [tutorial](http://software.bmo-design.de/how-to-integrate-google-plus-into-your-wordpress-plugin) here.


== Changelog ==

= Version 1.8 =
	* 1.8.2 Recommendation  to switch to my new gallery-plugin BMo-Expo.
	* 1.8.1 there was a little bug with the shortcode function that lowercase my parameters. New CSS Design from iWednesday.
	* I integrated a Swipe-Mode for touchable devices. Now it's possible to swipe through the main images with iPhone 4 with iOS 4.0.2, iPad with iOS 3.2.2, iPod Touch 2g with iOS 4.0.1, Android: HTC Magic with Android 2.2 Android Browser, Dolfin HD, Motorola Droid / Milestone with Android 2.1: Android Browser, Nexus One with Android 2.2 Android Browser, HTC Desire with Android 2.2: Android Browser, Samsung Galaxy S with Android 2.1: Android Browser

= Version 1.7 =
	* 1.7.8 little fix for external links
	* 1.7.7 little caption bugfix
	* 1.7.6 option to use desc as caption
	* 1.7.5 remove slashes (stripslashes) in captions
	* 1.7.4 safe options if updating the plugin
	* 1.7.3 little js fix for safari & chrome
	* 1.7.2 little php opacity bug fix for updates
	* 1.7.1 little js bug fix
	* It's done, the diashow option is integrated.
	* Especially for http://squantoworks.nl/ - it's possible to change the thumbnail opacity now.
	* Especially for http://squantoworks.nl/ - it's now possible to set external links to images and thumbs

= Version 1.6 =
	* 1.6.2 include new CSS-Design for likewise.dk
	* 1.6.1 Loading image integration in background.
	* New Shortcodes: sG_thumbsSolo and sG_imagesSolo. Now you can use the shortcode: [sG_thumbsSolo id=xxx] and [sG_imagesSolo id=xxx] to show only the thumbs or images.
	* better Safari and Chrome workaround

= Version 1.5 =
	* 1.5.3 -> Little CSS fix in shadowDesign
	* 1.5.2 -> Better compatibility with IE7
	* 1.5.1 -> Fix the js bug (scrollbar) in Safari and Chrome
	* Now I use the WordPress Shortcode API. The shortcode is now everywhere available. In the content [scrollGallery id=1] and also now in the template by <?php echo do_shortcode('[scrollGallery id=1]'); ?> 
    * New CSS-Design included: CSS3 shadow design
    * Possibility to use margins around images with the comment ImgMargins e.g. "ImgMargins: 0px 0px 0px 3px;".

= Version 1.4 =
    * Adjust image function
	* Version 1.4.2 -> I fix the URL file-access is disabled in the server configuration with getimagesize($picture["img"]) problem by using the ngg width and height
	
= Version 1.3 =
    * Styleable Version, custom css can be included in the css folder.
	* Dynamic thumb dimensions.
	* Check if NGG is installed and activated.
	
= Version 1.2 =
    * I fix the start at image 0 problem
	
= Version 1.1 =
    * Add Scripts in compat mode and with wp_enqueue_script
	* ScrollGallery Version 1.11

= Version 1.0 =
    * First release
	* ScrollGallery Version 1.10
