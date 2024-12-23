<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Piereg_Menu_Items_Visibility_Control {

	private static $instance = null;

	public function get_instance() {
		return null == self::$instance ? self::$instance = new self : self::$instance;
	}

	function __construct() {
		if( is_admin() ) {
			if( file_exists(PIEREG_DIR_NAME . '/classes/piereg_walker-nav-menu.php') )
				require_once( PIEREG_DIR_NAME . '/classes/piereg_walker-nav-menu.php' );
			
			add_filter( 'wp_edit_nav_menu_walker', array( &$this, 'piereg_edit_nav_menu_walker' ) );
			add_action( 'wp_nav_menu_item_custom_fields', array( &$this, 'piereg_option' ), 12, 5 );
			add_action( 'wp_update_nav_menu_item', array( &$this, 'piereg_update_option' ), 10, 3 );
			add_action( 'delete_post', array( &$this, 'piereg_remove_visibility_meta' ), 1, 3);
			
		} else {
			add_filter( 'wp_get_nav_menu_items', array( &$this, 'piereg_visibility_check' ), 10, 3 );
			add_action( 'init', array( &$this, 'piereg_clear_gantry_menu_cache' ) );
		}
	}
	
	function piereg_edit_nav_menu_walker( $walker ) {
		return 'piereg_Walker_Nav_Menu_Edit';
	}
	
	function piereg_option( $item_id, $item, $depth, $args, $id ) { ?>
		<p class="field-visibility description description-wide">
        	<label for="piereg-menu-item-visibility-<?php echo esc_attr($item_id); ?>">
				<?php esc_html_e('Visibility Status',"pie-register") ?>
				<?php $result = esc_html( get_post_meta( $item_id, '_menu_item_visibility', true ) );?>
				<select class="widefat code" id="piereg-menu-item-visibility-<?php echo esc_attr($item_id) ?>" name="piereg-menu-item-visibility[<?php echo esc_attr($item_id); ?>]">
                	<option value="default" <?php selected($result == "default", true); ?>><?php esc_html_e('Default',"pie-register") ?></option>
                	<option value="after_login" <?php selected($result == "after_login", true); ?>><?php esc_html_e("Show to Logged in Users","pie-register") ?></option>
                	<option value="before_login" <?php selected($result == "before_login", true); ?>><?php esc_html_e("Show to users who have not logged in.","pie-register") ?></option>
                	
                    <?php
					global $wp_roles;
					$role = $wp_roles->roles;
					
					foreach($role as $key => $value)
					{ 
						?>
						<option value="<?php echo esc_attr($key);?>"<?php selected($result == $key, true); ?>><?php esc_html_e("Show Only","pie-register");echo " ".esc_html($value['name']); ?></option>
                        <?php
					}
					?>
                </select>
			</label>
		</p>
	<?php }

	function piereg_update_option( $menu_id, $menu_item_id, $args ) {
		$meta_value 		= get_post_meta( $menu_item_id, '_menu_item_visibility', true );
		$create_meta_value 	= (isset($_POST['piereg-menu-item-visibility'][$menu_item_id])) ? sanitize_key($_POST['piereg-menu-item-visibility'][$menu_item_id]) : "default";
		$new_meta_value 	= stripcslashes( $create_meta_value );

		if( $new_meta_value == '') {
			delete_post_meta( $menu_item_id, '_menu_item_visibility', $meta_value );
		}
		elseif( $meta_value !== $new_meta_value ) {
			update_post_meta( $menu_item_id, '_menu_item_visibility', $new_meta_value );
		}
	}

	function piereg_visibility_check( $items, $menu, $args ) {
		$hidden_items = array();
					
		foreach( $items as $key => $item ) {
			$item_parent = get_post_meta( $item->ID, '_menu_item_menu_item_parent', true );
			
			$logic = get_post_meta( $item->ID, '_menu_item_visibility', true );
			
			if($logic == "default"){
				$visible = true;
			}
			elseif($logic == "after_login"){
				$visible = is_user_logged_in();
			}
			elseif($logic == "before_login"){
				$visible = !is_user_logged_in();
			}
			elseif($logic != "" ){
				$visible = in_array($logic, $GLOBALS["current_user"]->roles);
			} 
			else{
				$visible = true;
			}
			
			if( ! $visible || isset( $hidden_items[$item_parent] ) ) { // also hide the children of unvisible items
				unset( $items[$key] );
				$hidden_items[$item->ID] = '1';
			}
		}
		
		return $items;
	}

	function piereg_remove_visibility_meta( $post_id ) {
		if( is_nav_menu_item( $post_id ) ) {
			delete_post_meta( $post_id, '_menu_item_visibility' );
		}
	}

	function piereg_clear_gantry_menu_cache() {
		if( class_exists( 'GantryWidgetMenu' ) ) {
			GantryWidgetMenu::clearMenuCache();
		}
	}
}

$classVisibilityControl = new Piereg_Menu_Items_Visibility_Control();

class piereg_Walker_Nav_Menu_Edit_delete extends Walker_Nav_Menu {

	//function start_el(&$output, $item, $depth, $args) 
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0 )
	{
		$output = '';
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == sanitize_text_field($_GET['edit-menu-item']) ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)', "pie-register" ), $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;
        
		$output .= '<li id="menu-item-'.esc_attr($item_id).'" class="'.esc_attr(implode(' ', $classes )).'">';
			$output .= '<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title">'.esc_html( $title ).'</span>
					<span class="item-controls">
						<span class="item-type">'.esc_html( $item->type_label ).'</span>';
						$output .= '<a class="item-edit" id="edit-'.esc_attr($item_id).'" title="'.esc_attr__('Edit Menu Item', "pie-register" ).'" href="'.(( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) ).'">'.esc_html__( 'Edit Menu Item', "pie-register" ).'</a>
					</span>
				</dt>
			</dl>';

			$output .= '<div class="menu-item-settings" id="menu-item-settings-'.esc_attr($item_id).'">';
				if( 'custom' == $item->type && $item->title !== 'Page List'  ) : 
					$output .= '<p class="field-url description description-wide">
						<label for="edit-menu-item-url-'.esc_attr($item_id).'">'.esc_html__( 'URL', "pie-register" ).'<br />
							<input type="text" id="edit-menu-item-url-'.esc_attr($item_id).'" class="widefat code edit-menu-item-url" name="menu-item-url['.esc_attr($item_id).']" value="'.esc_attr( $item->url ).'" />
						</label>
					</p>';
				endif; ?>
				<?php if( $item->title !== 'Page List'  ) : // for advanced listers, we don't need any options
                    $output .= '<p class="description description-thin">
                        <label for="edit-menu-item-title-'.esc_attr($item_id).'">'.esc_html__( 'Navigation Label', "pie-register" ).'<br />
                            <input type="text" id="edit-menu-item-title-'.esc_attr($item_id).'" class="widefat edit-menu-item-title" name="menu-item-title['.esc_attr($item_id).']" value="'.esc_attr( $item->title ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="description description-thin">
                        <label for="edit-menu-item-attr-title-'.esc_attr($item_id).'">'.esc_html__( 'Title Attribute', "pie-register" ).'<br />
                            <input type="text" id="edit-menu-item-attr-title-'.esc_attr($item_id).'" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title['.esc_attr($item_id).']" value="'.esc_attr( $item->post_excerpt ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-link-target description description-thin">
                        <label for="edit-menu-item-target-'.esc_attr($item_id).'">'.esc_html__( 'Link Target', "pie-register" ).'<br />
                            <select id="edit-menu-item-target-'.esc_attr($item_id).'" class="widefat edit-menu-item-target" name="menu-item-target['.esc_attr($item_id).']">
                                <option value="" '.selected( $item->target, '').'>'.esc_html__('Same window or tab', "pie-register" ).'</option>
                                <option value="_blank" '.selected( $item->target, '_blank').'>'.esc_html__('New window or tab',"pie-register" ).'</option>
                            </select>
                        </label>
                    </p>';
                    $output .= '<p class="field-css-classes description description-thin">
                        <label for="edit-menu-item-classes-'.esc_attr($item_id).'">'.esc_html__( 'CSS Classes (optional)' , "pie-register" ).'<br />
                            <input type="text" id="edit-menu-item-classes-'.esc_attr($item_id).'" class="widefat code edit-menu-item-classes" name="menu-item-classes['.esc_attr($item_id).']" value="'.esc_attr( implode(' ', $item->classes ) ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-xfn description description-thin">
                        <label for="edit-menu-item-xfn-'.esc_attr($item_id).'">'.esc_html__( 'Link Relationship (XFN)', "pie-register" ).'<br />
                            <input type="text" id="edit-menu-item-xfn-'.esc_attr($item_id).'" class="widefat code edit-menu-item-xfn" name="menu-item-xfn['.esc_attr($item_id).']" value="'.esc_attr( $item->xfn ).'" />
                        </label>
                    </p>';
                    $output .= '<p class="field-description description description-wide">
                        <label for="edit-menu-item-description-'.esc_attr($item_id).'">'.esc_html__( 'Description', "pie-register" ).'<br />
                            <textarea id="edit-menu-item-description-'.esc_attr($item_id).'" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description['.esc_attr($item_id).']">'.esc_html( $item->description ).'</textarea>
                            <span class="description">'.esc_html__('The description will be displayed in the menu if the current theme supports it.', "pie-register" ).'</span>
                        </label>
                    </p>';
				endif; ?>
				<?php
				do_action('wp_nav_menu_item_custom_fields', $item_id, $item, $depth, $args, $id);
                $output .= '<div class="menu-item-actions description-wide submitbox">';
					if( 'custom' != $item->type ) : 
                            $output .= '<p class="link-to-original">'.esc_html__('Original : ', "pie-register" ).'<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a></p>';
					endif;
					
					$output .= '<a class="item-delete submitdelete deletion" id="delete-'.esc_attr($item_id).'" href="'.
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
						),
						'delete-menu_item_' . $item_id
					).'">'.esc_html__('Remove', "pie-register" ).'</a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-'.esc_attr($item_id).'" href="'.add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ).'#menu-item-settings-'.esc_attr($item_id).'">'.esc_html__('Cancel', "pie-register" ).'</a>
				</div>';

				$output .= '
				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id['.esc_attr($item_id).']" value="'.esc_attr($item_id).'" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id['.esc_attr($item_id).']" value="'.esc_attr( $item->object_id ).'" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object['.esc_attr($item_id).']" value="'.esc_attr( $item->object ).'" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id['.esc_attr($item_id).']" value="'.esc_attr( $item->menu_item_parent ).'" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position['.esc_attr($item_id).']" value="'.esc_attr( $item->menu_order ).'" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type['.esc_attr($item_id).']" value="'.esc_attr( $item->type ).'" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>';
        return $output;
	}
}
