<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WP_List_Table'))
    require_once($this->admin_path  . 'includes/class-wp-list-table.php');

class Pie_Invitation_Table extends WP_List_Table
{
    private $order;
    private $orderby;
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'table example',
            'plural'   => 'table examples',
            'ajax'     => true
        ));
    }
    private function get_sql_results()
    {
        global $wpdb;

        // Order
        $allowed_orderby = array('id', 'name', 'code_description', 'code_usage', 'expiry_date', 'code_user_role', 'count', 'status');
        $orderby = 'id';
        if (in_array($this->orderby, $allowed_orderby)) {
            $orderby = $this->orderby;
        }
        $order   = (strtoupper($this->order) === 'ASC') ? 'ASC' : 'DESC';

        // Search
        if (!empty($_GET['search'])) {
            $search = sanitize_text_field(wp_unslash($_GET['search']));
            $search_like = '%' . $wpdb->esc_like($search) . '%';

            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `id`, `name`, `code_description`, `code_usage`, `expiry_date`, `code_user_role`, `count`, `status` FROM `{$wpdb->prefix}pieregister_code` WHERE `name` LIKE %s ORDER BY `$orderby` $order",
                    $search_like
                )
            );
        }

        return $wpdb->get_results("SELECT `id`, `name`, `code_description`, `code_usage`, `expiry_date`, `code_user_role`, `count`, `status` FROM `{$wpdb->prefix}pieregister_code` ORDER BY `$orderby` $order");
    }
    public function set_order()
    {
        $order = (isset($_GET['order']) && 'asc' === strtolower($_GET['order'])) ? 'ASC' : 'DESC';
        $this->order = $order;
    }
    public function set_orderby()
    {
        $allowed = array('id', 'name', 'code_description', 'code_usage', 'expiry_date', 'code_user_role', 'count', 'status');
        $orderby = (isset($_GET['orderby']) && in_array($_GET['orderby'], $allowed, true)) ? $_GET['orderby'] : 'id';
        $this->orderby = $orderby;
    }
    public function search_box($text, $input_id)
    {
        if (empty($_REQUEST['search']) && !$this->has_items()) {
            return;
        }

        $input_id = $input_id . '-search-input';
?>
        <form id="pie-invite-search" class="pie-invite-search" method="GET">
            <?php

            if (!empty($_REQUEST['orderby']))
                echo '<input type="hidden" name="orderby" value="' . esc_attr($_REQUEST['orderby']) . '" />';
            if (!empty($_REQUEST['order']))
                echo '<input type="hidden" name="order" value="' . esc_attr($_REQUEST['order']) . '" />';
            if (!empty($_REQUEST['page']))
                echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
            ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($text); ?>:</label>
                <input type="search" id="<?php echo esc_attr($input_id); ?>" name="search" value="<?php echo isset($_REQUEST['search']) ? esc_attr(wp_unslash($_REQUEST['search'])) : ''; ?>" />
                <?php submit_button($text, '', '', false, array('id' => 'search-submit')); ?>
            </p>
        </form>
    <?php

    }
    public function ajax_user_can()
    {
        return current_user_can('edit_posts');
    }
    public function no_items()
    {
        esc_html_e('No invitation codes were found.', "pie-register");
    }
    public function get_views()
    {
        return array();
    }
    function get_table_classes()
    {
        return array('widefat', 'fixed', 'pie-list-table', 'striped', $this->_args['plural']);
    }
    public function get_columns()
    {
        $columns = array(
            'cb'              => '<input type="checkbox" />',
            'name'            => __('Code Name', "pie-register"),
            'code_description' => __('Code Description', "pie-register"),
            'code_usage'      => __('Usage', "pie-register"),
            'expiry_date'     => __('Expiry Date', "pie-register") . '- <span class="pr-pro-notice" style=""><a href="https://pieregister.com/plan-and-pricing/">' . esc_html__("Available in premium version", "pie-register") . '</a></span>',
            'code_user_role'  => __('User Role', "pie-register") . '- <span class="pr-pro-notice" style=""><a href="https://pieregister.com/plan-and-pricing/">' . esc_html__("Available in premium version", "pie-register") . '</a></span>',
            'email_invite'    => __('Sent Through Email', "pie-register") . '- <span class="pr-pro-notice" style=""><a href="https://pieregister.com/plan-and-pricing/">' . esc_html__("Available in premium version", "pie-register") . '</a></span>',
            'action'  => __('Action', "pie-register")
        );

        return $columns;
    }
    public function get_sortable_columns()
    {
        $sortable = array(
            'name' => array('name', true),
            'code_description' => array('code_description', true),
            'code_usage'  => array('code_usage', true),
        );

        return $sortable;
    }
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" value="%s" class="invitaion_fields_class"  />',
            intval($item->id)
        );
    }
    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array(
            $columns,
            $hidden,
            $sortable
        );

        // SQL results
        $posts = $this->get_sql_results();

        empty($posts) and $posts = array();
        # >>>> Pagination

        $opt = get_option(OPTION_PIE_REGISTER);
        $per_page_item = (isset($opt['invitaion_codes_pagination_number']) && ((int)$opt['invitaion_codes_pagination_number']) != 0) ? (int)$opt['invitaion_codes_pagination_number'] : 10;
        unset($opt);
        $per_page     = $per_page_item;
        $current_page = $this->get_pagenum();
        $total_items  = count($posts);
        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
        $last_post = $current_page * $per_page;
        $first_post = $last_post - $per_page + 1;
        $last_post > $total_items and $last_post = $total_items;
        // Setup the range of keys/indizes that contain 
        // the posts on the currently displayed page(d).
        // Flip keys with values as the range outputs the range in the values.
        $range = array_flip(range($first_post - 1, $last_post - 1, 1));
        // Filter out the posts we're not displaying on the current page.
        $posts_array = array_intersect_key($posts, $range);
        # <<<< Pagination
        // Prepare the data
        $id = 1;
        foreach ($posts_array as $key => $post) {
            $link     = "#";
            $no_title = esc_html__('No title set', "pie-register");
            $title    = ! $post->name ? "<em>{$no_title}</em>" : $post->name;
            $post_name = $post->name;
            $post_description = !empty($post->code_description) ? $post->code_description : 'None';
            $posts[$key]->cb = '<input type="checkbox" value="' . esc_attr($posts[$key]->id) . '" class="invitaion_fields_class" id="invitaion_fields[id_' . esc_attr($id) . ']" />';

            /*code name*/
            $e_title = esc_html__('Click here to edit', "pie-register");
            $posts[$key]->name = '<span title="' . esc_attr($e_title) . '" onclick="show_field(this,\'field_id_' . esc_js($id) . '\');" id="field_id_1_' . esc_attr($id) . '">' . $posts[$key]->name . '</span><input type="text" id="field_id_' . esc_attr($id) . '" value="' . esc_attr($posts[$key]->name) . '" style="display:none;" onblur="hide_field(this,\'field_id_1_' . esc_js($id) . '\');" data-id-invitationcode="' . esc_attr($posts[$key]->id) . '" data-type-invitationcode="name" />';
            /*code description */
            $posts[$key]->code_description = '<span title="' . esc_attr($e_title) . '" onclick="show_field(this,\'code_description_field_id_' . esc_js($id) . '\');" class="code_description"  id="code_description_field_id_1_' . esc_attr($id) . '">' . $post_description . '</span><input type="text" id="code_description_field_id_' . esc_attr($id) . '" value="' . $posts[$key]->code_description . '" style="display:none; width:100px;"" onblur="hide_field(this,\'code_description_field_id_1_' . esc_js($id) . '\');" data-id-invitationcode="' . $posts[$key]->id . '" data-type-invitationcode="code_description" />';
            /*code usage*/
            $posts[$key]->code_usage = '<span>' . esc_attr($posts[$key]->count) . '/</span><span title="' . esc_attr($e_title) . '" onclick="show_field(this,\'usage_field_id_' . esc_js($id) . '\');" id="usage_field_id_1_' . esc_attr($id) . '">' . esc_html($posts[$key]->code_usage)  . '</span><input autocomplete="off" type="text" class="code_usage" id="usage_field_id_' . esc_attr($id) . '" value="' . esc_attr($posts[$key]->code_usage) . '" style="display:none;" onblur="hide_field(this,\'usage_field_id_1_' . esc_js($id) . '\');" data-id-invitationcode="' . esc_attr($posts[$key]->id) . '" data-type-invitationcode="code_usage" />';

            /*expiry date*/
            $posts[$key]->expiry_date = '';

            /*user role*/
            $posts[$key]->code_user_role = '';

            /*email count*/
            global $wpdb;
            $registered_users = [];
            $not_used_codes   = 0;
            $prefix           = $wpdb->prefix . "pieregister_";
            $emailtable       = $prefix . "invite_code_emails";
            $codetable        = $prefix . "code";
            $code_name = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT `name` FROM `{$wpdb->prefix}pieregister_code` WHERE id = %d",
                    array($posts[$key]->id)
                )
            );
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM `{$wpdb->prefix}pieregister_invite_code_emails` WHERE code_id = %d",
                    array($posts[$key]->id)
                )
            );
            $total_emails_sent = $wpdb->num_rows;

            $posts[$key]->email_invite  = '<span onclick="email_popup(' . esc_js($id) . ');" id="emailcount_field_id_1_' . esc_attr($id) . '">' . esc_html($total_emails_sent) . '</span>';
            $posts[$key]->email_invite .= '<div title="' . esc_attr($code_name[0]->name) . '" id="dialog-message_' . esc_attr($id) . '" style="display:none;">';
            $posts[$key]->email_invite .= '<div>';
            $posts[$key]->email_invite .= '<p class="pop-head">Already Sent :</p>';

            if ($total_emails_sent > 0) {
                foreach ($results as $result) {
                    $users = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT * FROM `{$wpdb->prefix}users` WHERE user_email = %s",
                            array($result->email_address)
                        )
                    );

                    if ($wpdb->num_rows == 0) {
                        $posts[$key]->email_invite .= '<p class="email-content">' . esc_html($result->email_address) . '</p>';
                        $not_used_codes++;
                    } else {
                        $registered_users[] =  $result->email_address;
                    }
                }
            }
            if ($not_used_codes == 0) {
                $posts[$key]->email_invite .= '<p class="email-content"> None </p>';
            }
            $posts[$key]->email_invite .= '</div>';
            $posts[$key]->email_invite .= '<hr>';
            $posts[$key]->email_invite .= '<div class="heading-popup">';

            $posts[$key]->email_invite .= '<p class="pop-head"> Registered Email Addresses :</p>';

            if (count($registered_users) > 0) {
                foreach ($registered_users as $emails) {
                    $posts[$key]->email_invite .= '<p class="email-content">' . esc_html($emails) . '</p>';
                }
            } else {
                $posts[$key]->email_invite .= '<p class="email-content"> None </p>';
            }

            $posts[$key]->email_invite .= '</div>';
            $posts[$key]->email_invite .= '</div>';

            $class = ($post->status == 1) ? "active"  : "inactive";
            $title = ($class == "active") ? "Deactivate" : "Activate";

            $posts[$key]->action = '<a onclick="changeStatusCode(\'' . esc_js($post->id) . '\',\'' . esc_js($post_name) . '\',\'' . esc_js($title) . '\');" href="javascript:;" title="' . esc_attr($title) . '" class="' . esc_attr($class) . '"></a> <a class="delete" href="javascript:;" onclick="confirmDelInviteCode(\'' . esc_js($post->id) . '\',\'' . esc_js($post_name) . '\');" title="' . esc_attr__("Delete", "pie-register") . '"></a>';
            $id++;
        }
        $this->items = $posts_array;
    }
    public function column_default($item, $column_name)
    {
        return $item->$column_name;
    }
    public function display_tablenav($which)
    {
    ?>
        <div class="tablenav <?php echo esc_attr($which); ?>">

            <div class="alignleft actions">
                <?php //Bulk option here 
                ?>
            </div>

            <?php
            $this->extra_tablenav($which);
            $this->pagination($which);
            ?>
            <br class="piereg_clear" />
        </div>
<?php
    }
    public function extra_tablenav($which)
    {
        global $wp_meta_boxes;
        $views = $this->get_views();
        if (empty($views))
            return;
        $this->views();
    }
}
