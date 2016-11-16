<?php



if( ! class_exists( "class_WPMI_Status" ) ) :

	class class_WPMI_Status{
		/**
		 * @var class_WPMI_Status The single instance of the class
		 * @since 1.5
		 */
		protected static $_instance = null;
		/**
		 * Main Nav Menu control Instance
		 *
		 * Ensures only one instance of class_WPMI_Status is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 * @return class_WPMI_Status - Main instance
		 */
		public static function instance()
		{
			if( is_null( self::$_instance ) )
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		/**
		 * class_WPMI_Status Constructor.
		 * @access public		
		 * @since  1.0
		 */
		public function __construct(){

			// Admin functions
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			// load the textdomain
			add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
			// switch the admin walker
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
			// add new fields via hook
			add_action( 'wp_custom_fields_nav_menu_item', array( $this, 'menu_item_custom_fields' ), 10, 4 );			 
			// save the menu item meta
			add_action( 'wp_update_nav_menu_item', array( $this, 'menu_item_update' ), 10, 2 );

		}
		
		
		public function admin_init(){
			
			if( ! class_exists( 'Walker_Nav_Menu_Edit_Status' ) ){
				require_once( plugin_dir_path( __FILE__ ) . 'class_Walker_Nav_Menu_Edit_Status.inc.php' );
			}			 
		}
		
		/**
		 * Make Plugin Translation-ready
		 * CALLBACK FUNCTION FOR:  add_action( 'plugins_loaded', array( $this,'load_text_domain'));
		 * @since 1.0
		*/
		public function load_text_domain(){
			load_plugin_textdomain( 'wpmi-control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
		
		/**
		 * Add fields to hook added in Walker
		 *
		 * @params obj $item - the menu item
		 * @params array $args
		 *
		 * @since 1.0
		*/
		public function menu_item_custom_fields( $item_id, $item, $depth, $args ){
			
			$item_status = get_post_field('post_status',$item->ID ); 			
			 
			?>
			

			<p class="field-status status status-wide">
				<label for="edit-menu-item-status-<?php echo $item->ID; ?>">
				<?php _e( 'Menu item status' ); ?><br/>				 
					<input type = "radio" class = "wpmi-status"
					       name = "wpmi-menu-status[<?php echo $item->ID; ?>]"
					       id = "wpmi-menu-enable-status-for-<?php echo $item->ID; ?>" <?php checked( 'publish', $item_status ); ?>
					       value = "publish"/>
					<label for = "wpmi-menu-enable-status-for-<?php echo $item->ID; ?>">
						<?php _e( 'Enable', 'wpmi-control', 'wpmi-control'); ?>
					</label>
					&nbsp;&nbsp;&nbsp;
					<input type = "radio" class = "wpmi-status"
					       name = "wpmi-menu-status[<?php echo $item->ID; ?>]"
					       id = "wpmi-menu-disable-status-for-<?php echo $item->ID; ?>" <?php checked( 'unpublish', $item_status ); ?>
					       value = "unpublish"/>
					<label for = "wpmi-menu-disable-status-for-<?php echo $item->ID; ?>">
						<?php _e( 'Disable', 'wpmi-control', 'wpmi-control') ?>
					</label>				 
				<input type="hidden" name="wpmi-menu-id[<?php echo $item->ID; ?>]" value="<?php echo $item->ID; ?>"/>
				<input type = "hidden" name = "wpmi-control-nonce" value="<?php echo wp_create_nonce( 'wpmi-control-nonce' ); ?>"/>
				<p class="description" style="clear:both;"><?php _e('Enable: item appears in frontend menu. Disable: item will not appears in frontend menu.'); ?></p>
				</label>	
			</p>
			<?php
			
		}
		
		/**
		 * Save the control as menu item meta
		 * @return string
		 * @since 1.0
		 */
		public function menu_item_update( $menu_id, $menu_item_db_id )
		{
			 
			// verify this came from our screen and with proper authorization.
			if( ! isset( $_POST['wpmi-control-nonce'] ) || ! wp_verify_nonce( $_POST['wpmi-control-nonce'], 'wpmi-control-nonce' ) )
			{
				return;
			}

			$menu_status = false;

			if( isset( $_POST['wpmi-menu-status'][ $menu_item_db_id ] ) && in_array( $_POST['wpmi-menu-status'][ $menu_item_db_id ], array('publish','unpublish'))){
				$menu_status = ( $_POST['wpmi-menu-status'][ $menu_item_db_id ] == 'publish' ) ? 'publish' : 'unpublish';
			}

			if( $menu_status ){
				$menu_item = array(
					  'ID'           => $menu_item_db_id,
					  'post_status'   => $menu_status 
				  );
				wp_update_post($menu_item );				
			} 
		}

				
		/**
		 * Override the Admin Menu Walker
		 * @since 1.0
		 */
		public function edit_nav_menu_walker( $walker )
		{
			return 'Walker_Nav_Menu_Edit_Status';
		}

	}

endif; 
// class_exists check