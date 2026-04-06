<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists( 'WP_List_Table' ) )
    require_once( $this->admin_path . 'includes/class-wp-list-table.php' );
	
class PieRedirectSettings extends WP_List_Table
{
    private $order;
    private $orderby;
    public function __construct()
    {
        parent :: __construct( array(
            'singular' => 'pieregister-redirect',
            'ajax'     => true
        ) );
    }
    public function get_sql_results($fields = "")
    {
        global $wpdb;
		$allowed_orderby = array('id', 'user_role', 'status');
		$orderby = 'id';
		if (in_array((string)$this->orderby, $allowed_orderby)) {
			$orderby = $this->orderby;
		}
		
		$order = (strtoupper((string)$this->order) === 'DESC') ? 'DESC' : 'ASC';
		
		if ($fields === "`user_role`") {
			return $wpdb->get_results("SELECT `user_role` FROM `{$wpdb->prefix}pieregister_redirect_settings` ORDER BY `$orderby` $order");
		}

		return $wpdb->get_results("SELECT `id`, `user_role`, `logged_in_url`, `logged_in_page_id`, `log_out_url`, `log_out_page_id`, `status` FROM `{$wpdb->prefix}pieregister_redirect_settings` ORDER BY `$orderby` $order");
    }
    public function set_order()
    {
        $order = 'ASC';
        if ( isset( $_GET['order'] ) && $_GET['order'] )
            $order = sanitize_key($_GET['order']);
        $this->order = $order;	// Lowercase alphanumeric characters, dashes and underscores are allowed
    }
    public function set_orderby()
    {
        $orderby = 'id';
        if ( isset( $_GET['orderby'] ) && $_GET['orderby'] )
            $orderby = sanitize_key($_GET['orderby']);
        $this->orderby = $orderby ;	// Lowercase alphanumeric characters, dashes and underscores are allowed
    }
    public function ajax_user_can() 
    {
        return current_user_can( 'edit_posts' );
    }
    public function no_items() 
    {
        esc_html_e( 'Record not found', "pie-register" );
    }
    public function get_views()
    {
        return array();
    }
    function get_table_classes() {
        return array( 'widefat', 'fixed', 'pie-list-table', 'striped', $this->_args['plural'] );
    }
    public function get_columns()
    {
        $columns = array(
			'id'         => __( '#', "pie-register" ),
            'user_role' => __( 'User Role',"pie-register" ),
            'logged_in_page_id'  => __( 'After Login Page' ,"pie-register"),
            'log_out_page_id'  => __( 'After Logout Page' ,"pie-register"),
            'status'  => __( 'Status',"pie-register" )
        );
        return $columns;        
    }

    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = array();
        $this->_column_headers = array( 
            $columns,
            $hidden
        );
        // SQL results
        $posts = $this->get_sql_results();
        empty( $posts ) AND $posts = array();
        # >>>> Pagination
		
        $per_page_item = (isset($_POST['invitation_code_per_page_items']))? intval(sanitize_key($_POST['invitation_code_per_page_items'])) : 10;
		$per_page     = $per_page_item;
        $current_page = $this->get_pagenum();
        $total_items  = count( $posts );
        $this->set_pagination_args( array (
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page )
        ) );
        $last_post = $current_page * $per_page;
        $first_post = $last_post - $per_page + 1;
        $last_post > $total_items AND $last_post = $total_items;
        // Setup the range of keys/indizes that contain 
        // the posts on the currently displayed page(d).
        // Flip keys with values as the range outputs the range in the values.
        $range = array_flip( range( $first_post - 1, $last_post - 1, 1 ) );
        // Filter out the posts we're not displaying on the current page.
        $posts_array = array_intersect_key( $posts, $range );
        # <<<< Pagination
        // Prepare the data
		$id = 1;
        foreach ( $posts_array as $key => $post )
        {
			global $wp_roles;
			$user_role_value = (isset($wp_roles->roles[$post->user_role]['name'])) ? $wp_roles->roles[$post->user_role]['name'] : $post->user_role;
			//User Role
			$posts[ $key ]->user_role = '<span><a href="javascript:;" >'.esc_html($user_role_value).'</a></span>';
			//Logged In URL
			$posts[ $key ]->logged_in_url = '<span>'.esc_url(urldecode($posts[ $key ]->logged_in_url)).'</span>';
			
			//Log In Page Title
			if( $posts[ $key ]->logged_in_page_id == 0 ) {
				$posts[ $key ]->logged_in_page_id = '<span>'.esc_url(urldecode($posts[ $key ]->logged_in_url)).'</span>';
			} else {
				$posts[ $key ]->logged_in_page_id = '<span>'.esc_html(get_the_title($posts[ $key ]->logged_in_page_id)).'</span>';
			}
			//Log Out URL
			$posts[ $key ]->log_out_url = '<span>'.esc_url(urldecode($posts[ $key ]->log_out_url)).'</span>';
			//Log Out Page Title
			if( $posts[ $key ]->log_out_page_id == 0 ) {
				$posts[ $key ]->log_out_page_id = '<span>'.esc_url(urldecode($posts[ $key ]->log_out_url)).'</span>';
			} else {
				$posts[ $key ]->log_out_page_id = '<span>'.esc_html(get_the_title($posts[ $key ]->log_out_page_id)).'</span>';
			}
			
			$class = (isset($post->status) && intval($post->status) == 1) ? "active"  : "inactive";
			//Status
			$posts[ $key ]->status = '<a href="javascript:;" class="'.esc_attr($class).'"></a>';
            $posts[ $key ]->status .= '<a class="delete" href="javascript:;" ></a>';
            
            $posts[$key]->id = $id;
            			
			$id++;
        }
        $this->items = $posts_array;
    }
    public function column_default( $item, $column_name )
    {
        return $item->$column_name;
    }
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php //Bulk option here ?>
            </div>
             
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="piereg_clear" />
        </div>
        <?php 
    }
    public function extra_tablenav( $which )
    {
        global $wp_meta_boxes;
        $views = $this->get_views();
        if ( empty( $views ) )
            return;
        $this->views();
    }
}