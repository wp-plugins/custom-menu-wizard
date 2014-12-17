<?php
/*
 * Plugin Name: Custom Menu Wizard
 * Plugin URI: http://wordpress.org/plugins/custom-menu-wizard/
 * Description: Show any part of a custom menu in a Widget, or in content using a Shortcode. Customise the output with extra classes or html; filter by current menu item or a specific item; set a depth, show the parent(s), change the list style, etc. Use the included emulator to assist with the filter settings.
 * Version: 3.1.3
 * Author: Roger Barrett
 * Author URI: http://www.wizzud.com/
 * License: GPL2+
*/
defined( 'ABSPATH' ) or exit();
/*
 * v3.1.3 change log
 * - tweak : css tweak for the assist when in customizer, for WordPress 4.1
 * 
 * v3.1.2 change log
 * - modification of the readme to avoid WordPress truncating documentation under Other Notes
 * No code changes.
 * 
 * v3.1.1 change log
 * - fixed bug : only show the allow_all_root setting in the shortcode equivalent if the primary filter is by branch
 * - added work-around for occasions when a theme causes de-registration of the widget which prevents the shortcode working in content
 * 
 * v3.1.0 change log
 * - added an Alternative section which takes a cmwizard shortcode and conditionally applies it as an entirely new widget configuration
 * - added fallback determination (has to be enabled) for no current item found as using items marked as current_item_parent (first found)
 * - fixed bug in determination of home page pagination pages (?paged=2, etc) as home page still being current item
 * - fixed bug introduced in v3.0.4 that prevented CMW script loading on the customizer page - when the Widget Customizer plugin is loaded - for WordPress v3.8 and below
 * - fixed bug : stop disabling selected fields based on other settings, because this caused the customizer to wipe values that may have been still required
 * 
 * v3.0.4 change log
 * - fixed bug in the display of the "No Current Item!" warning in the "assist"
 * - corrected the enabling/disabling of a couple of fields in the widget form, and tweaked the indentation for better responsiveness
 * - fixed a bug with accessibility mode when javascript is enabled, and added a warning about the accuracy of the shortcode when javascript is disabled
 * - extended the All Root Items inclusion to be a selectable number of levels (as per the Exclusions by Level)
 * 
 * v3.0.3 change log
 * - removed all occurrences of "Plugin " followed by "Name" from everywhere except the main plugin file (this one!) to avoid update() incorrectly reporting "invalid header" when activating straight from installation (rather than from the Plugin admin page)
 * - tweak : eliminate the over-use of get_title() when determining the widget title
 * - tweak : added self-terminating forward slash to generated shortcodes
 * - prepare for WPv4 (avoid deprecated functions)
 * 
 * v3.0.2 change log
 * - fixed bug where the shortcode shown on new instances didn't initially reflect the automatically selected menu
 * 
 * v3.0.1 change log
 * - fixed bug in determination of pre-existing legacy widgets versus brand new widget instances
 * - replaced widget property _cmw_allow_legacy_update with a filter, custom_menu_wizard_prevent_legacy_updates : return TRUE to prevent updates of legacy widgets
 * - added new filter, custom_menu_wizard_wipe_on_update : return TRUE to cleanse an instance of old settings
 * 
 * v3.0.0 change log
 * - Major rethink/rewrite : the Children Of filter is now a Branch filter, and the selected menu item becomes the key focus point rather 
 *   than its children. The Levels available for a Branch filter now include relative levels as well as absolute levels, and there are more
 *   options available for requiring that the menu contains the current menu item. With the exception of some anomalies (edge cases) the
 *   output achievable with v2 of the widget is still available with v3. Although there is no automatice upgrade available, v2 is still
 *   fully supported; however, any new instances of the widget will be created as v3 only. Note that the shortcode for v3 has changed to
 *   [cmwizard ... ], but, again, v2's [custom_menu_wizard] is still supported! NB: There is no separate 2.1.0 release - it is incorporated
 *   into this release.
 *   Simplest examples : "Children Of = Current Item" becomes "Branch = Current Item, Starting at +1 (children)"
 *                       "Children Of = Current Item, with Include Parent" becomes "Branch = Current Item, Starting at the Current Item"
 * - menu items can now be specifically excluded from the final output
 * - ids for Items and Exclusions can be set to include all descendants
 * - widget title can be set from the root of Branch or current item
 * - the inclusion of branch ancestors, and optionally their siblings, can be set by absolute or relative level
 * - compatibile with Widget Customizer plugin, and its implementation in WP 3.9 core
 * - added title_tag to shortcode options
 * - added findme to shortcode options, [cmwizard findme=1], output restricted to edit_pages capability
 * 
 * v2.1.0 change log
 * - fixed bug where duplicate menu item ids were causing elements to be ignored
 * - fixed IE8 bug with levels indentation in assist
 * - removed automatic selection of shortcode text (inconsistent cross-browser, so just triple click as usual; paste-as-text if possible!)
 * - swapped the Items' checkboxes for clickable Ticks
 * - tweaked dialog styling, and make more responsive to re-sizing
 * - added collapsible menu structures to dialog, and set fixed position (with toggle back to absolute)
 * - added utility to "assist" to locate posts containing a CMW shortcode
 * - minimum requirement for WP raised to v3.6
 * 
 * v2.0.6 change log:
 * - modified determination of current item to cope better with multiple occurences (still first-found, but within prioritised groups)
 * - replaced display of update information on plugins list with styled request (and link) to read changelog (update info sometimes didn't display, and some considered it "scary" for users)
 * 
 * v2.0.5 change log:
 * - prevent PHP warnings of Undefined index/offset when building $substructure
 * 
 * v2.0.4 change log:
 * - fixed bug where clearing the container field failed to remove the container from the output
 * - remove WordPress's menu-item-has-children class (WP v3.7+) when the filtered item no longer has children
 * - added automatic selection of the shortcode text when it is clicked
 * - tweaked admin styling and javascript for WordPress v3.8
 * 
 * v2.0.3 change log:
 * - fixed bug with missing global when enqueuing scripts and styles for admin page
 * 
 * v2.0.2 change log:
 * - fixed bug where Include Ancestors was not automatically including the Parent
 * - fixed bug where the "assist" was incorrectly calculating Depth Relative to Current Item when the current menu item was outside the scope of the Filtered items
 * - behaviour change : only recognise the first "current" item found (used to allow subsequent "current" items to override any already encountered)
 * 
 * v2.0.1 change log:
 * - fixed bug that set a specific items filter when it shouldn't have been set, and prevented show-all working
 * 
 * v2.0.0 change log:
 * - Possible Breaker! : start level has been made consistent for showall and kids-off filters. Previously, a kids-of filter on an item at level 2,
 *   with start level set to 4, would return no output because the immediate kids (at level 3) were outside the start level; now, there will
 *   be output, starting with the grand-kids (at level 4)
 * - Possible Breaker! : there is now an artificial "root" above the top level menu items, which means that a parent or root children-of filter will no 
 *   longer fail for a top-level current menu item; this may well obviate the need for the current item fallback, but it has been left in for
 *   backward compatibility.
 * - added option for calculating depth relative to current menu item
 * - added option allowing list output to be dependent on current menu item being present somewhere in the list
 * - refactored the code
 * 
 * v1.2.2 change log:
 * - bugfix : fallback for Current Item with no children was failing because the parent's children weren't being picked out correctly
 * 
 * v1.2.1 change log:
 * - added some extra custom classes, when applicable : cmw-fellback-to-current & cmw-fellback-to-parent (on outer UL/OL) and cmw-the-included-parent, cmw-an-included-parent-sibling & cmw-an-included-ancestor (on relevant LIs)
 * - corrected 'show all from start level 1' processing so that custom classes get applied and 'Title from "Current" Item' works (regardless of filter settings)
 * - changed the defaults for new widgets such that only the Filter section is open by default; all the others are collapsed
 * - updated demo.html and readme.txt, and made demo available from readme
 * 
 * v1.2.0 change log:
 * - added custom_menu_wizard shortcode, to run the widget from within content
 * - moved the 'no ancestor' fallback into new Fallback collapsible section, and added a fallback for Current Item with no children
 * - fixed bug with optgroups/options made available for the 'Children of' selector after the widget has been saved (also affecting disabled fields & styling)
 * - don't include menus with no items
 * - updated demo.html
 * 
 * v1.1.0 change log:
 * - added 'Current Root Item' and 'Current Parent Item' to the 'Children of' filter
 * - added an Output option to include both the parent item and the parent's siblings (for a successful 'Children of' filter)
 * - added Fallback to Current Item option, and subsidiary Output option overrides, to enable Current Root & Current Parent to match a Current Item at root level
 * - added max-width style (100%) to the 'Children of' SELECT in the widget options
 * - added widget version to the admin js enqueuer
 * - ignore/disable hide_empty for WP >= v3.6 (wp_nav_menu() does it automatically)
 * - rebuilt 'Children of' SELECT to account for IE's lack of OPTGROUP/OPTION styling
 * - moved the setting of 'disabled' attributes on INPUTs/SELECTs from PHP into javascript
 */

if( !class_exists( 'Custom_Menu_Wizard_Plugin' ) ){

	//include the widget class and its walker...
	include( plugin_dir_path( __FILE__ ) . 'include/class.widget.php' );
	include( plugin_dir_path( __FILE__ ) . 'include/class.walker.php' );

	//instantiate...
	add_action( 'plugins_loaded', array( 'Custom_Menu_Wizard_Plugin', 'init' ) );

	//declare the main plugin class...
	class Custom_Menu_Wizard_Plugin {
		
		public static $version = '3.1.3';
		public static $script_handle = 'custom-menu-wizard-plugin-script';
		public static $widget_class = 'Custom_Menu_Wizard_Widget';
		protected static $instance;
		
		/**
		 * constructor : adds actions
		 */
		public function __construct(){

			add_action( 'widgets_init', array( &$this, 'widget_and_shortcode' ) );
			add_action( 'wp_ajax_cmw-find-shortcodes', array( &$this, 'ajax_find_shortcodes' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'register_scripts' ) );
			add_action( 'admin_print_styles-widgets.php', array( &$this, 'enqueue_styles' ) );
			add_action( 'admin_print_scripts-widgets.php', array( &$this, 'enqueue_scripts' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
			//add customizer support...
			add_action( 'customize_controls_enqueue_scripts', array( &$this, 'enqueue_styles' ) );
			add_action( 'customize_controls_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

			//add filter for encoding a cmwizard shortcode into instance settings...
			add_filter( 'custom_menu_wizard_encode_shortcode', array( $this, 'encode_shortcode' ), 10, 1 );
			//add filter for sanitizing an alternative shortcode setting... 
			add_filter( 'custom_menu_wizard_sanitize_alternative', array( $this, 'sanitize_alternative' ), 10, 1 );

		} //end __construct()

		/**
		 * hooked into plugins_loaded action : creates the plugin instance
		 */
		public static function init(){
			
			is_null( self::$instance ) && self::$instance = new self;
			return self::$instance;

		} //end init()

		/**
		 * hooked into admin_menu action : add action for when an update to this plugin is available
		 */
		public function admin_menu(){

			add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), array( &$this, 'update_message' ), 10, 2 );
			
		} //end admin_menu()
		
		/**
		 * hooked into admin_print_scripts-widgets.php & customize_controls_enqueue_scripts actions : queues scripts needed by the plugin
		 */
		public function enqueue_scripts(){

			//script is pre-registered - see this->register_scripts() - so that it can be localized if need be (like for accessibility mode)
			//BUT on customize screens pre WPv3.9 the script does not get the chance to pre-register before it is 
			//asked to enqueue, so this has to check that it is actually registered!...
			if( !wp_script_is( self::$script_handle, 'registered' ) ){
				$this->register_scripts();
			}
			wp_enqueue_script( self::$script_handle );
			
		} //end enqueue_scripts()
		
		/**
		 * hooked into admin_print_styles-widgets.php & customize_controls_enqueue_scripts actions : queues styles needed by the plugin
		 */
		public function enqueue_styles(){
			global $wp_scripts;

			wp_enqueue_style( 'custom-menu-wizard-plugin-styles', plugins_url( '/custom-menu-wizard.css', __FILE__ ), array(), self::$version );
			//if there's no jquery-ui style already registered, register Smoothness...
			if( !wp_style_is( 'jquery-ui', 'registered' ) ) {
				//get the jquery ui core version (default to 1.9.2 if not found)...
				$jquery_ui_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
				//register Smoothness theme for the determined ui version...
//				wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_ui_version . '/themes/smoothness/jquery-ui.css' );
				wp_register_style( 'jquery-ui', '//code.jquery.com/ui/' . $jquery_ui_version . '/themes/smoothness/jquery-ui.css' );
			}
			wp_enqueue_style( 'jquery-ui' );
			
		} //end enqueue_styles()

		/**
		 * hooked into admin_enqueue_scripts : registers the plugin script
		 */
		public function register_scripts(){

			$min = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
			wp_register_script( self::$script_handle, plugins_url( "/custom-menu-wizard$min.js", __FILE__ ), array('jquery-ui-dialog'), self::$version, true );

		}	//end register_scripts()

		/**
		 * hooked into in_plugin_update_message-custom-menu-wizard action : request read changelog before updating
		 * @param array $plugin_data Plugin metadata
		 * @param array $r Metadata about the available plugin update
		 */
		public function update_message( $plugin_data, $r ){
			
			$url = 'http://wordpress.org/plugins/' . $r->slug. '/changelog/';
			$style = implode( ';', array(
				'-webkit-box-sizing:border-box',
				'-moz-box-sizing:border-box',
				'box-sizing:border-box',
				'background-color:#D54E21',
				'border-radius:2px',
				'color:#FFFFFF',
				'display:inline-block',
				'margin:0',
				'max-width:100%',
				'overflow:hidden',
				'padding:0 0.5em',
				'text-overflow:ellipsis',
				'text-shadow:0 1px 0 rgba(0, 0, 0, 0.5)',
				'vertical-align:text-bottom',
				'white-space:nowrap'
				) ) . ';';

?>
 <p style="<?php echo $style; ?>"><em><?php printf( __('Please <a href="%s" style="color:#FFFFFF;text-decoration:underline;" target="_blank">read the Changelog</a> <strong>before</strong> updating!'), $url ); ?></em></p>
<?php

		} //end update_message()

		/**
		 * hooked into wp_ajax_cmw-find-shortcodes action : handle ajax request to find posts containing CMW shortcodes, returning XML
		 */
		public function ajax_find_shortcodes(){

			check_admin_referer( 'cmw-find-shortcodes' );
			$response = array(
				'what' => 'cmw_find_shortcodes',
				'action' => 'list_posts',
				'id' => '1',
				'data' => $this->find_shortcodes()
			);
			$xmlResponse = new WP_Ajax_Response($response);
			$xmlResponse->send();

		}
		
		/**
		 * list any post that contains a CMW shortcode; can be called from a shortcode or via an ajax call
		 * 
		 * @param array $shortcodeInst Array of shortcode attributes
		 * @return string HTML
		 */

		public function find_shortcodes( $shortcodeInst = false ){
			global $wpdb;

			$html = '';

			//from a shortcode, the user must have edit_pages capability (implies Editor or above)...
			if( $shortcodeInst !== false && !current_user_can( 'edit_pages' ) ){
				return $html;
			}

			$codes = array(
				'cmw-demo-found-old' => '[custom_menu_wizard',
				'cmw-demo-found-new' => '[cmwizard'
				);
			foreach( $codes as $k => $v ){
				$j = str_replace( '-', '_', $k );
				//like_escape deprecated in v4...
				if( method_exists( $wpdb, 'esc_like' ) ){
					$$j = '%' . $wpdb->esc_like( $v ) . '%';
				}else{
					$$j = '%' . like_escape( esc_sql ( $v ) ) . '%';
				}
			}

			//search in all custom fields...
			$sql  = "SELECT DISTINCT post_id FROM {$wpdb->postmeta}";
			$sql .= " WHERE meta_value LIKE '%s' OR meta_value LIKE '%s'";
			$post_ids_meta = $wpdb->get_col( $wpdb->prepare( $sql, $cmw_demo_found_old, $cmw_demo_found_new ) );
			//search in post_content...
			$sql  = "SELECT DISTINCT ID FROM {$wpdb->posts}";
			$sql .= " WHERE post_content LIKE '%s' OR post_content LIKE '%s'";
			$post_ids_post = $wpdb->get_col( $wpdb->prepare( $sql, $cmw_demo_found_old, $cmw_demo_found_new ) );

			$post_ids = array_merge( $post_ids_meta, $post_ids_post );

			if( empty( $post_ids ) ){
				$html .= '<p>' . __('No CMW shortcodes found.') . '</p>';
			}else{
				$args = array(
					'ignore_sticky_posts' => true,
					'nopaging' => true,
					'orderby' => 'date',
					'post_type' => 'any',
					'post_status' => array( 'publish', 'draft', 'future', 'pending', 'private' ),
					'post__in' => $post_ids
					);

				$the_query = new WP_Query( $args );
				if( $the_query->have_posts() ){
					$html .= '<dl>';  
					while( $the_query->have_posts() ){
						$the_query->the_post();
						$id = get_the_ID();
						$inPost = in_array( $id, $post_ids_post );
						$inMeta = in_array( $id, $post_ids_meta );
						$dtClass = array();
						$anchorTitle = array();
						if( $inPost ){
							$content = get_the_content();
							foreach( $codes as $k => $v ){
								if( strpos( $content, $v ) !== false ){
									$dtClass[ $k ] = 1;
									$anchorTitle[ $v . ']' ] = 1;
								}
							}
						}
						if( $inMeta ){
							$content = get_post_meta( $id );
							foreach( $content as $k => $v ){
								$content[ $k ] = implode(' ', $v );
							}
							$content = implode( ' ', $content );
							foreach( $codes as $k => $v ){
								if( strpos( $content, $v ) !== false ){
									$dtClass[ $k ] = 1;
									$anchorTitle[ $v . ']' ] = 1;
								}
							}
						}
						$anchorTarget = get_post_type( $id );
						if( empty( $anchorTarget ) ){
							$anchorTarget = __('unknown type');
						}else{
							$anchorTarget = (string)$anchorTarget;
						}
						$content = $inPost ? ( $inMeta ? __( 'content+meta' ) : __( 'content' ) ) : __( 'meta' );
						$anchorTitle = $anchorTarget . ' #' . $id . ', ' . $content . ', ' . implode( __(' and '), array_keys( $anchorTitle ) );
						$anchorTarget = '';
						if( $shortcodeInst === false ){
							//is from assistant via ajax...
							$anchorTarget = 'target="_blank"';
							$anchorTitle .= ' ... ' . __('opens new tab/window');
						}
						$html .= '<dt class="' . implode( ' ', array_keys( $dtClass ) ) . '"><a href="' . get_permalink() . '" ' . $anchorTarget . ' title="' . $anchorTitle . '">' . get_the_title() . '</a></dt>';
					}
					$html .= '</dl>';
				}else{
					$html .= '<p>' . __('No CMW shortcodes found.') . '</p>';
				}

				wp_reset_postdata();
			}

			//if originator is shortcode, put a simple wrapper (no styling!) around the results, and optionally an H3 title...
			if( $shortcodeInst !== false ){
				$anchorTitle = is_array( $shortcodeInst ) && !empty( $shortcodeInst['title'] ) ? esc_attr( strip_tags( trim( $shortcodeInst['title'] ) ) ) : '';
				$anchorTitle = empty( $anchorTitle ) ? '' : '<h3>' . $anchorTitle . '</h3>';
				$html = '<div class="cmw-list-posts-with-shortcodes">' . $anchorTitle . $html . '</div>';
			}

			return $html;

		} //end find_shortcodes()

		/**
		 * hooked into widgets_init action : registers widget and adds shortcode(s)
		 */
		public function widget_and_shortcode(){

			//register the widget class...
			register_widget( self::$widget_class );
			//add shortcode...
			add_shortcode( 'cmwizard', array( &$this, 'shortcode' ) );
			//add shortcode, v2.1.0 version (deprecated!)...
			add_shortcode( 'custom_menu_wizard', array( &$this, 'shortcode_legacy' ) );
			
		} //end widget_and_shortcode()

		/**
		 * hooked into custom_menu_wizard_sanitize_alternative filter : sanitizes an alternative shortcode setting
		 * used by this->shortcode_instance, and the widget class
		 * 
		 * @param string $alt Alternative (switch_to setting)
		 * @return string
		 */
		public function sanitize_alternative( $alt = '' ){

			if( empty( $alt ) || !is_string( $alt ) ){
				return '';
			}

			//kill containing square brackets, self-terminators and spaces, then split on square bracket...
			$alt = preg_split( '/[\[\]]/', trim( $alt, ' []/' ) );
			//use the first element, kill tabs, CRLFs and multiple spaces, and retrim for self-terminators and spaces...
			$alt = trim( preg_replace( array( '/[\r\n\t]+/', '/\s\s+/' ), ' ', $alt[0] ), ' /' );
			//remove leading 'cmwizard' tag...
			$alt = preg_replace( '/^cmwizard\s/', '', $alt . ' ' );
			//remove any occurrences of 'menu=whatever' and 'alternative="whatever"' (optional double quotes), and trim spaces...
			$alt = trim( preg_replace( array('/\smenu=[^\s]*\s/', '/\salternative=("[^"]*"|[^\s]*)\s/' ), ' ', ' ' . $alt . ' ' ) );

			return $alt;

		} //end sanitize_alternative()

		/** 
		 * shortcode processing for [cmwizard option="" option="" ...] (as of v3.0.0)
		 * 
		 * see wp-includes/widgets.php for the_widget() code
		 * Note that hide_empty is set to ON and can not be overridden!
		 * 
		 * differences from [custom_menu_wizard] shortcode (ie. v2.1.0)
		 *   deprecated:
		 *   - children_of : now branch, and limited to current[-item] or digits; parent|current-parent|root|current-ancestor all require conversion
		 *   - start_level : now level (integer) for a by-level filter, or start_at (string) for a by-branch filter (determining branch_start)
		 *   - include_root : (as of v3.0.4) replaced by include_level (like exclude_level); include_root On equiv. is include_level == '1'
		 *   changed:
		 *   - contains_current : was a switch, now a string (empty or menu|primary|secondary|output); switch ON = 'output'
		 *   - include : now accepts siblings, ancestors and/or ancestor-siblings (csv); parent is gone, and hyphen separator no longer allowed
		 *   - title_from : should now be csv, hyphen separator no longer allowed
		 *   added:
		 *   - title_tag & findme
		 *   - include_level (v3.0.4)
		 * 
		 * default (ie. no options) is:
		 *  - show all
		 *  - of first populated menu found (alphabetically)
		 *  - from root, for unlimited depth
		 *  - as hierarchical nested ULs inside a DIV.widget_custom_menu_wizard.shortcode_custom_menu_wizard
		 * 
		 * @filters : custom_menu_wizard_shortcode_attributes        array of attributes (unfiltered!) supplied to the shortcode
		 *            shortcode_atts_cmwizard                        array of output attributes, array of supported attributes, array of supplied attributes
		 *            custom_menu_wizard_shortcode_settings          array of widget settings derived from the attributes
		 *            custom_menu_wizard_shortcode_widget_args       array of the sidebar args used to wrap widgets and their titles (before|after_widget, before|after_title)
		 * NB each of the arrays passed to the above filters has a extra key-value pair of 'cmwv' => the current plugin version, eg. '3.0.0'
		 * 
		 * @param array $atts options supplied to the shortcode
		 * @param string $content Within start-end shortcode tags
		 * @param string $tag Shortcode tag
		 * @return string HTML that comes from running the_widget()
		 */
		public function shortcode($atts, $content, $tag){

			$html = '';
			$instance = $this->shortcode_instance( $atts, $tag, $content, true );
			$ok = !empty( $instance );

			if( $ok && !empty( $instance['findme'] ) ){
				//return the findme output...
				return $this->find_shortcodes( $instance );
			}

			if( $ok ){

				//if widget isn't registered(!), try re-registering; if still not registered, cop out...
				if( !$this->widget_registered() ){
					if( did_action( 'widgets_init' ) > 0 ){
						if( apply_filters( 'custom_menu_wizard_widget_reregister', true )  ){
							//re-register the widget...
							register_widget( self::$widget_class );
							if( !$this->widget_registered() ){
								return WP_DEBUG ? __('[cmwizard PROBLEM="widget de-registered, and failed to re-register!"/]') : $html;
							}
						}else{
							return WP_DEBUG ? __('[cmwizard PROBLEM="widget de-registered, and not allowed to re-register!"/]') : $html;
						}
					}else{
						//hasn't had a chance to register yet!...
						return WP_DEBUG ? __('[cmwizard PROBLEM="widgets have not been initialised yet!"/]') : $html;
					}
				}

				//handle widget_class here because we have full control over $before_widget...
				$before_widget_class = array(
					'widget_custom_menu_wizard',
					'shortcode_custom_menu_wizard'
					);
				$instance['widget_class'] = empty( $instance['widget_class'] ) ? '' : esc_attr( trim ( $instance['widget_class'] ) );
				if( !empty( $instance['widget_class'] ) ){
					foreach( explode(' ', $instance['widget_class'] ) as $i ){
						if( !empty( $i ) && !in_array( $i, $before_widget_class ) ){
							$before_widget_class[] = $i;
						}
					} 
				}
				unset( $instance['widget_class'] );
			}

			if( $ok ){
				//not used by the plugin, but could be used in the widget code to tell whether it was being
				//run as a result of a widget or a shortcode?...
				$instance['shortcode'] = true;
				//allow the element that wraps the widget title to be changed from an h2 (the WP default) to another tag...
				//note : does not allow for changing the class, or for removing the wrapping element
				//       for a class override, add CSS rule for
				//         .shortcode_custom_menu_wizard .widgettitle { ..... }
				//       can also be overriden using the 'custom_menu_wizard_shortcode_widget_args' filter (applied below)
				$instance['title_tag'] = esc_attr( trim( $instance['title_tag'] ) );
				if( empty( $instance['title_tag'] ) ){
					//default to H2...
					$instance['title_tag'] = 'h2';
				}
				//apart from before_widget, these are lifted from the_widget() (wp-includes/widgets.php)...
				$sidebar_args = array(
					'before_widget' => '<div class="' . implode( ' ', $before_widget_class ) . '">',
					'after_widget' => '</div>',
					'before_title' => '<' . $instance['title_tag'] . ' class="widgettitle">',
					'after_title' => '</' . $instance['title_tag'] . '>'
					);
				unset( $instance['title_tag'] );
				
				ob_start();
				the_widget(
					self::$widget_class,
					apply_filters(
						'custom_menu_wizard_shortcode_settings',
						array_merge( $instance, array('cmwv' => self::$version) )
					),
					apply_filters(
						'custom_menu_wizard_shortcode_widget_args',
						array_merge( $sidebar_args, array('cmwv' => self::$version) )
					) );
				$html = ob_get_clean();
			}

		 	return empty( $html ) ? '' : $html;

		} //end shortcode()

		/**
		 * does most of the attribute processing/checking for the cmwizard (only) shortcode
		 * is called from shortcode() method *AND* encode_shortcode() method, which is run from a filter enabling
		 * settings to be changed at start (after determination of current item) of the widget's walker process.
		 * 
		 * @param array $atts options supplied to the shortcode
		 * @param string $tag Shortcode tag
		 * @param string $content Within start-end shortcode tags
		 * @param boolean $doShortcode True if called from shortcode(), false otherwise
		 * @return array|boolean A set of widget instance settings, or false if shortcode is invalid
		 */
		public function shortcode_instance( $atts, $tag, $content = '', $doShortcode = false ){

			$ok = false;

			// NB csv = comma or space separated list...
			$instance = shortcode_atts( array(
				'title'               => '',
				'menu'                => 0, // menu id, slug or name
				'level'               => 0,
				//determines filter (in conjunction with items)...
				'branch'              => 0, // a menu item id, or current|current-item
				//determines filter (in conjunction with branch)...
				'items'               => '', // csv of menu item ids (an id may have a '+' appended, for inheritance, eg. '23+')
				'depth'               => 0, // 0 = unlimited
				'depth_rel_current'   => 0,
				//determines branch_start...
				'start_at'            => '',
				'start_mode'          => '', // 'level' or empty
				'allow_all_root'      => 0,
				//inclusions...
				'ancestors'           => 0, //integer (negative = relative)
				'ancestor_siblings'   => 0, //integer (negative = relative)
				'include_root'        => 0, //switch (means *all* root items!) v3.0.4 DEPRECATED still allowed (for back compat.), equiv. is include_level='1'
				'include_level'       => '', // v3.0.4 digit, possibly appended with a '+' or '-', eg. '2', '2+', or '2-'
				'siblings'            => 0, //switch
				//exclusions...
				'exclude'             => '', // csv of menu item ids (an id may have a '+' appended, for inheritance, eg. '23+')
				'exclude_level'       => '', // digit, possibly appended with a '+' or '-', eg. '2', '2+', or '2-'
				'contains_current'    => '', // menu|primary|secondary|inclusions|output
				//determines fallback (current|parent|quit) and, optionally, fallback_siblings and/or fallback_depth...
				'fallback'            => '', //eg. 'quit', or 'current' or 'current+siblings' or 'parent+siblings,2' or 'parent,1'
				//switches...
				'flat_output'         => 0,
				//determines title_from_[branch|current|branch-root|current-root]...
				'title_from'          => '', // csv of branch|current|branch-root|current-root
				'ol_root'             => 0,
				'ol_sub'              => 0,
				//strings...
				'container'           => 'div', // a tag : div|nav are WP restrictions, not the widget's; '' =  no container
				'container_id'        => '',
				'container_class'     => '',
				'menu_class'          => 'menu-widget',
				'widget_class'        => '',
				//determines switch_if, switch_at & switch_to (depending on $content)...
				'alternative'         => '', //csv of current|no-current|no-output and menu|primary|secondary|inclusions|output, eg. 'current,menu'
				//determines before & after...
				'wrap_link'           => '', // a tag name (eg. div, p, span, etc)
				//determines link_before & link_after...
				'wrap_link_text'      => '', // a tag name (eg. span, em, strong)
				//modifies the before/after_title $sidebar_args, changing the default H2 tag to something else(?)...
				'title_tag'           => '', // a tag name (eg. h1, h3, etc)
				//utility : doesn't run widget! instead, lists all posts/pages that contain a CMW shortcode...
				'findme'              => 0
				),
				$doShortcode
					? apply_filters(
						'custom_menu_wizard_shortcode_attributes',
						array_merge( (array)$atts, array('cmwv' => self::$version) )
						)
					: (array)$atts,
				$doShortcode
					? $tag // since WP3.6 this allows use of shortcode_atts_cmwizard filter, applied by shortcode_atts()
					: ''
			);

			//if not decoding a main shortcode then we're looking at an alternative, and alternatives can't be
			//nested, nor can they run findme or change the title's tag element...
			if( !$doShortcode ){
				unset( $instance['findme'], $instance['title_tag'], $instance['alternative'] );
			}

			if( !empty( $instance['findme'] ) ){
				return $instance;
			}

			//in order of priority...
			$byItems = !empty( $instance['items'] );
			$byBranch = !$byItems && !empty( $instance['branch'] );
			$byLevel = !$byItems && !$byBranch;

			if( empty( $instance['menu'] ) ){
				//gonna find the first menu (alphabetically) that has items...
				$menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
			}else{
				//allow for menu being something other than an id (eg. slug or name), but we need the id for the widget...
				$menus = wp_get_nav_menu_object( $instance['menu'] );
				if( !empty( $menus) ){
					$menus = array( $menus );
				}
			}
			if( !empty( $menus ) ){
				foreach( $menus as $i=>$menu ){
					$items = wp_get_nav_menu_items( $menu->term_id );
					$ok = !empty( $items );
					if( $ok ){
						$instance['menu'] = $menu->term_id;
						break;
					}
				}
			}
			unset( $menus );

			if( $ok ){
				if( $byItems ){
					$instance['filter'] = 'items';
				}
				if( $byBranch ){
					$instance['filter'] = 'branch';
					switch( "{$instance['start_at']}" ){
						case '0':
						case 'branch':   $instance['branch_start'] = ''; break;
						case 'root' :    $instance['branch_start'] = '1'; break;
						case 'children': $instance['branch_start'] = '+1'; break;
						case 'parent':   $instance['branch_start'] = '-1'; break;
						default:         $instance['branch_start'] = "{$instance['start_at']}";
					}
					if( $instance['branch'] == 'current' || $instance['branch'] == 'current-item' ){
						$instance['branch'] = 0;
					}elseif( !is_numeric( $instance['branch'] ) ){
						//if branch is non-numeric then it could be the title of a menu item, but we need it to be the menu item's id...
						$instance['branch'] = strtolower( $instance['branch'] );
						foreach( $items as $item ){
							$ok = strtolower( $item->title ) == $instance['branch'];
							if( $ok ){
								$instance['branch'] = $item->ID;
								break;
							}
						}
					}
				}
				if( $byLevel ){
					$instance['filter'] = '';
					$instance['level'] = max(1, intval( $instance['level'] ) );
				}
				unset( $instance['start_at'] );
			}

			if( $ok ){
				//include_level, and the deprecated include_root switch...
				//if level is empty but root is set, set include_level to '1'...
				if( empty( $instance['include_level'] ) && $instance['include_root'] ){
					$instance['include_level'] = '1';
				}
				unset( $instance['include_root'] );
				//fallback => fallback and fallback_siblings and fallback_depth...
				//allows "X", "X,Y" or "X,Y,Z" where comma could be space, and X|Y|Z could be "quit"|"current"|"parent", or "+siblings", or digit(s)
				//but "quit", "current" or "parent" must be present (others are optional)
				if( $byBranch && empty( $instance['branch'] ) && !empty( $instance['fallback'] ) ){
					$i = preg_split( '/[\s,]+/', strtolower( $instance['fallback'] ), -1, PREG_SPLIT_NO_EMPTY );
					$instance['fallback'] = '';
					if( in_array( 'quit', $i ) ){
						$instance['fallback'] = 'quit';
					}elseif( in_array( 'parent', $i ) ){
						$instance['fallback'] = 'parent';
					}elseif( in_array( 'current', $i ) ){
						$instance['fallback'] = 'current';
					}
					if( !empty( $instance['fallback'] ) && $instance['fallback'] != 'quit' ){
						if( in_array( '+siblings', $i ) ){
							$instance['fallback_siblings'] = 1;
						}
						$i = array_diff( $i, array( 'quit', 'parent', 'current', '+siblings' ) );
						if( !empty( $i ) ){
							foreach( $i as $v ){
								$v = trim( $v );
								if( preg_match( '/^\d+$/', $v ) > 0 && intval( $v ) > 0 ){
									$instance['fallback_depth'] = intval( $v );
									break;
								}
							}
						}
					}
				}
			}

			if( $ok ){
				//title_from => title_from_...
				if( !empty( $instance['title_from'] ) ){
					$i = preg_split( '/[\s,]+/', strtolower( $instance['title_from'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( $j == 'branch' || $j == 'current' ){
							$instance[ 'title_from_' . $j ] = 1;
						}elseif( $j == 'branch-root' || $j == 'current-root' ){
							$instance[ 'title_from_' . str_replace( '-', '_', $j ) ] = 1;
						}
					}
				}
				unset( $instance['title_from'] );

				//wrap_link => before & after...
				$instance['wrap_link'] = esc_attr( trim( $instance['wrap_link'] ) );
				if( !empty( $instance['wrap_link'] ) ){
					$instance['before'] = '<' . $instance['wrap_link'] . '>';
					$instance['after'] = '</' . $instance['wrap_link'] . '>';
				}
				unset( $instance['wrap_link'] );

				//wrap_link_text => link_before & link_after...
				$instance['wrap_link_text'] = esc_attr( trim( $instance['wrap_link_text'] ) );
				if( !empty( $instance['wrap_link_text'] ) ){
					$instance['link_before'] = '<' . $instance['wrap_link_text'] . '>';
					$instance['link_after'] = '</' . $instance['wrap_link_text'] . '>';
				}
				unset( $instance['wrap_link_text'] );

				//alternative => switch_if, switch_at & switch_to...
				if( !empty( $instance['alternative'] ) ){
					$i = preg_split( '/[\s,]+/', strtolower( $instance['alternative'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( in_array( $j, array('current', 'no-current', 'no-output' ) ) ){
							$instance['switch_if'] = $j;
						}elseif( in_array( $j, array('menu', 'primary', 'secondary', 'inclusions', 'output') ) ){
							$instance['switch_at'] = $j;
						}
					}
					if( !empty( $instance['switch_if'] ) && !empty( $instance['switch_at'] ) ){
						$instance['switch_to'] = apply_filters( 'custom_menu_wizard_sanitize_alternative', $instance['switch_to'] );
					}else{
						$instance['switch_if'] = $instance['switch_at'] = $instance['switch_to'] = '';
					}
					unset( $instance['alternative'] );
				}

				//turn on hide_empty...
				$instance['hide_empty'] = 1;
			}

		 	return $ok ? $instance : false;

		} //end shortcode_instance()

		/**
		 * hooked into custom_menu_wizard_encode_shortcode filter : converts a cmwizard shortcode into instance settings fit for 
		 *                                                          the widget() method of Custom_Menu_Wizard_Widget
		 * 
		 * it's important to note that a shortcode processed this way does *NOT* hit the filters that a cmwizard shortcode would
		 * normally hit, namely custom_menu_wizard_shortcode_attributes & shortcode_atts_cmwizard
		 * 
		 * @param string $shortcode A full [cmwizard .../] shortcode
		 * @return array|boolean Instance settings, or false if error
		 */
		public function encode_shortcode( $shortcode = '' ){

			if( class_exists( self::$widget_class ) && 
					preg_match( '/^cmwizard\s?(.*)$/', rtrim( ltrim( $shortcode, '[ ' ), '] /' ), $m ) > 0 ){
				$instance = $this->shortcode_instance( shortcode_parse_atts( trim( $m[1] ) ), 'cmwizard' );
				if( !empty( $instance ) ){
					$instance['cmwv'] = self::$version;
					$instance = Custom_Menu_Wizard_Widget::cmw_settings( $instance, false, 'widget' );
				}
			}
			return empty( $instance ) ? false : $instance;

		} //end encode_shortcode()

		/**
		 * checks that the widget is registered
		 * 
		 * @return boolean True if registered
		 */
		public function widget_registered(){
			global $wp_widget_factory;

			return ( isset( $wp_widget_factory->widgets[ self::$widget_class ] ) &&
					is_a( $wp_widget_factory->widgets[ self::$widget_class ], self::$widget_class ) );

		}

		/** 
		 * shortcode processing for [custom_menu_wizard option="" option="" ...] (as of v2.1.0)
		 * see wp-includes/widgets.php for the_widget() code
		 * Note that hide_empty is set to ON and can not be overridden!
		 * 
		 * default (ie. no options) is:
		 *  - show all
		 *  - of first populated menu found (alphabetically)
		 *  - from root, for unlimited depth
		 *  - as hierarchical nested ULs inside a DIV.widget_custom_menu_wizard.shortcode_custom_menu_wizard
		 * 
		 * @filters : custom_menu_wizard_shortcode_attributes        array of attributes supplied to the shortcode
		 *            custom_menu_wizard_shortcode_settings          array of widget settings derived from the attributes
		 *            custom_menu_wizard_shortcode_widget_args       array of the sidebar args used to wrap widgets and their titles (before|after_widget, before|after_title)
		 *
		 * @param array $atts options supplied to the shortcode
		 * @param string $content Within start-end shortcode tags
		 * @param string $tag Shortcode tag
		 * @return string HTML that comes from running the_widget()
		 */
		function shortcode_legacy($atts, $content, $tag){
			$html = '';
			$ok = false;
			$instance = shortcode_atts( array(
				'title'               => '',
				'menu'                => 0, // menu id, slug or name
				//determines filter & filter_item ('items' takes precedence over 'children_of' because it's more specific)...
				'children_of'         => '', // empty = show all (dep. on 'items'); menu item id or title (caseless), or current|current-item|parent|current-parent|root|current-ancestor
				'items'               => '', // v2.0.0 empty = show all (dep. on 'children_of'); comma- or space-separated list of menu item ids (start level and depth don't apply)
				'start_level'         => 1,
				'depth'               => 0, // 0 = unlimited
				//only if children_of is (parent|current-parent|root|current-ancestor); determines fallback_no_ancestor, fallback_include_parent & fallback_include_parent_siblings...
				'fallback_parent'     => 0, // 1 = use current-item; 'parent' = *and* include parent, 'siblings' = *and* include both parent and its siblings
				//only if children_of is (current|current-item); determines fallback_no_children, fallback_nc_include_parent & fallback_nc_include_parent_siblings...
				'fallback_current'    => 0, // 1 = use current-parent; 'parent' = *and* include parent (if available), 'siblings' = *and* include both parent (if available) and its siblings
				//switches...
				'flat_output'         => 0,
				'contains_current'    => 0, // v2.0.0
				//determines include_parent, include_parent_siblings & include_ancestors...
				'include'             =>'', //comma|space|hyphen separated list of 'parent', 'siblings', 'ancestors'
				'ol_root'             => 0,
				'ol_sub'              => 0,
				//determines title_from_parent & title_from_current...
				'title_from'          => '', //comma|space|hyphen separated list of 'parent', 'current'
				'depth_rel_current'   => 0, // v2.0.0
				//strings...
				'container'           => 'div', // a tag : div|nav are WP restrictions, not the widget's; '' =  no container
				'container_id'        => '',
				'container_class'     => '',
				'menu_class'          => 'menu-widget',
				'widget_class'        => '',
				//determines before & after...
				'wrap_link'           => '', // a tag name (eg. div, p, span, etc)
				//determines link_before & link_after...
				'wrap_link_text'      => '' // a tag name (eg. span, em, strong)
				),
				$atts,
				$tag // since WP3.6 this allows use of shortcode_atts_custom_menu_wizard filter, applied by shortcode_atts()
			);

			$instance = apply_filters( 'custom_menu_wizard_shortcode_attributes', $instance );

			if( empty( $instance['menu'] ) ){
				//gonna find the first menu (alphabetically) that has items...
				$menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
			}else{
				//allow for menu being something other than an id (eg. slug or name), but we need the id for the widget...
				$menus = wp_get_nav_menu_object( $instance['menu'] );
				if( !empty( $menus) ){
					$menus = array( $menus );
				}
			}
			if( !empty( $menus ) ){
				foreach( $menus as $i=>$menu ){
					$items = wp_get_nav_menu_items( $menu->term_id );
					$ok = !empty( $items );
					if( $ok ){
						$instance['menu'] = $menu->term_id;
						break;
					}
				}
			}
			unset( $menus );

			if( $ok ){
				$instance['filter'] = $instance['filter_item'] = 0;
				if( empty( $instance['items'] ) ){
					//children_of => filter & filter_item...
					if( empty( $instance['children_of'] ) ){
						$instance['children_of'] = '';
					}
					switch( $instance['children_of'] ){
						case '':
							break;
						case 'root': case 'current-ancestor':
							--$instance['filter_item']; //ends up as -2
						case 'parent': case 'current-parent':
							--$instance['filter_item']; //ends up as -1
						case 'current': case 'current-item':
							$instance['filter'] = 1;
							break;
						default:
							$instance['filter'] = 1;
							$instance['filter_item'] = strtolower( $instance['children_of'] );
					}
					//if filter_item is non-numeric then it could be the title of a menu item, but we need it to be the menu item's id...
					if( !is_numeric( $instance['filter_item'] ) ){
						foreach( $items as $item ){
							$ok = strtolower( $item->title ) == $instance['filter_item'];
							if( $ok ){
								$instance['filter_item'] = $item->ID;
								break;
							}
						}
					}
				}else{
					$instance['filter'] = -1;
				}
				unset( $instance['children_of'] );
			}

			if( $ok ){
				//fallback_parent => fallback_no_ancestor switch (and extension switches)...
				$instance['fallback_no_ancestor'] = $instance['fallback_include_parent'] = $instance['fallback_include_parent_siblings'] = 0;
				if( $instance['filter_item'] < 0 && !empty( $instance['fallback_parent'] ) ){
					$instance['fallback_no_ancestor'] = 1;
					$i = preg_split( '/[\s,-]+/', strtolower( $instance['fallback_parent'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( $j == 'parent' ){
							$instance['fallback_include_parent'] = 1;
						}elseif( $j == 'siblings' ){
							$instance['fallback_include_parent_siblings'] = 1;
						}
					}
				}
				//fallback_current => fallback_no_children switch (and extension switches)...
				$instance['fallback_no_children'] = $instance['fallback_nc_include_parent'] = $instance['fallback_nc_include_parent_siblings'] = 0;
				if( $instance['filter'] == 1 && $instance['filter_item'] == 0 && !empty( $instance['fallback_current'] ) ){
					$instance['fallback_no_children'] = 1;
					$i = preg_split( '/[\s,-]+/', strtolower( $instance['fallback_current'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( $j == 'parent' ){
							$instance['fallback_nc_include_parent'] = 1;
						}elseif( $j == 'siblings' ){
							$instance['fallback_nc_include_parent_siblings'] = 1;
						}
					}
				}
				unset( $instance['fallback_parent'], $instance['fallback_current'] );
				//include => include_* ...
				$instance['include_parent'] = $instance['include_parent_siblings'] = $instance['include_ancestors'] = 0;
				if( $instance['filter'] == 1 && !empty( $instance['include'] ) ){
					$i = preg_split( '/[\s,-]+/', strtolower( $instance['include'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( $j == 'parent' ){
							$instance['include_parent'] = 1;
						}elseif( $j == 'siblings' ){
							$instance['include_parent_siblings'] = 1;
						}elseif( $j == 'ancestors' ){
							$instance['include_ancestors'] = 1;
						}
					}
				}
				unset( $instance['include'] );
				//title_from => title_from_parent, title_from_current ...
				$instance['title_from_parent'] = $instance['title_from_current'] = 0;
				if( !empty( $instance['title_from'] ) ){
					$i = preg_split( '/[\s,-]+/', strtolower( $instance['title_from'] ), -1, PREG_SPLIT_NO_EMPTY );
					foreach( $i as $j ){
						if( $j == 'parent' ){
							$instance['title_from_parent'] = 1;
						}elseif( $j == 'current' ){
							$instance['title_from_current'] = 1;
						}
					}
				}
				unset( $instance['title_from'] );
				//wrap_link => before & after...
				$instance['before'] = $instance['after'] = '';
				$instance['wrap_link'] = esc_attr( trim( $instance['wrap_link'] ) );
				if( !empty( $instance['wrap_link'] ) ){
					$instance['before'] = '<' . $instance['wrap_link'] . '>';
					$instance['after'] = '</' . $instance['wrap_link'] . '>';
				}
				//wrap_link_text => link_before & link_after...
				$instance['link_before'] = $instance['link_after'] = '';
				$instance['wrap_link_text'] = esc_attr( trim( $instance['wrap_link_text'] ) );
				if( !empty( $instance['wrap_link_text'] ) ){
					$instance['link_before'] = '<' . $instance['wrap_link_text'] . '>';
					$instance['link_after'] = '</' . $instance['wrap_link_text'] . '>';
				}

				//handle widget_class here because we have full control over $before_widget...
				$before_widget_class = array(
					'widget_custom_menu_wizard',
					'shortcode_custom_menu_wizard'
					);
				$instance['widget_class'] = empty( $instance['widget_class'] ) ? '' : esc_attr( trim ( $instance['widget_class'] ) );
				if( !empty( $instance['widget_class'] ) ){
					foreach( explode(' ', $instance['widget_class'] ) as $i ){
						if( !empty( $i ) && !in_array( $i, $before_widget_class ) ){
							$before_widget_class[] = $i;
						}
					} 
				}
				$instance['widget_class'] = '';
				//turn on hide_empty...
				$instance['hide_empty'] = 1;
			}

			if( $ok ){
				//apart from before_title, these are lifted from the_widget()...
				$sidebar_args = array(
					'before_widget' => '<div class="' . implode( ' ', $before_widget_class ) . '">',
					'after_widget' => '</div>',
					'before_title' => '<h2 class="widgettitle">',
					'after_title' => '</h2>'
					);
				ob_start();
				the_widget(
					'Custom_Menu_Wizard_Widget',
					apply_filters('custom_menu_wizard_shortcode_settings', $instance ),
					apply_filters('custom_menu_wizard_shortcode_widget_args', $sidebar_args )
					);
				$html = ob_get_clean();
			}
		 	return empty($html) ? '' : $html;

		} //end shortcode_legacy()
		
	} //end class Custom_Menu_Wizard_Plugin

}
