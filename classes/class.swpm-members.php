<?php

class SwpmMembers extends WP_List_Table {

    function __construct() {
        parent::__construct(array(
            'singular' => SwpmUtils::_('Member'),
            'plural' => SwpmUtils::_('Members'),
            'ajax' => false
        ));
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />'
            , 'member_id' => SwpmUtils::_('ID')
            , 'user_name' => SwpmUtils::_('User Name')
            , 'first_name' => SwpmUtils::_('First Name')
            , 'last_name' => SwpmUtils::_('Last Name')
            , 'email' => SwpmUtils::_('Email')
            , 'alias' => SwpmUtils::_('Membership Level')
            , 'subscription_starts' => SwpmUtils::_('Access Starts')
            , 'account_state' => SwpmUtils::_('Account State')
        );
    }

    function get_sortable_columns() {
        return array(
            'member_id' => array('member_id', true),
            'user_name' => array('user_name', true)
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'bulk_delete' => SwpmUtils::_('Delete')
        );
        return $actions;
    }

    function column_default($item, $column_name) {
        return $item[$column_name];
    }

    function column_member_id($item) {
        $actions = array(
            'edit' => sprintf('<a href="admin.php?page=%s&member_action=edit&member_id=%s">Edit</a>', $_REQUEST['page'], $item['member_id']),
            'delete' => sprintf('<a href="?page=%s&member_action=delete&member_id=%s"
                                    onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>', $_REQUEST['page'], $item['member_id']),
        );
        return $item['member_id'] . $this->row_actions($actions);
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="members[]" value="%s" />', $item['member_id']
        );
    }

    function prepare_items() {
        global $wpdb;

        $this->process_bulk_action();

        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_members_tbl";
        $query .= " LEFT JOIN " . $wpdb->prefix . "swpm_membership_tbl";
        $query .= " ON ( membership_level = id ) ";
        $s = filter_input(INPUT_POST, 's');
        if (!empty($s)) {
            $query .= " WHERE  user_name LIKE '%" . strip_tags($s) . "%' "
                    . " OR first_name LIKE '%" . strip_tags($s) . "%' "
                    . " OR last_name LIKE '%" . strip_tags($s) . "%' ";
        }
        $orderby = filter_input(INPUT_GET, 'orderby');
        $orderby = empty($orderby) ? 'member_id' : $orderby;
        $order = filter_input(INPUT_GET, 'order');
        $order = empty($order) ? 'DESC' : $order;

        $sortable_columns = $this->get_sortable_columns();
        $orderby = SwpmUtils::sanitize_value_by_array($orderby, $sortable_columns);
        $order = SwpmUtils::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));

        $query.=' ORDER BY ' . $orderby . ' ' . $order;
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        $perpage = 20;
        $paged = filter_input(INPUT_GET, 'paged');
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $perpage;
        }
        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ));

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $wpdb->get_results($query, ARRAY_A);
    }

    function no_items() {
        _e('No Member found.');
    }

    function process_form_request() {
        if (isset($_REQUEST['member_id']))
            return $this->edit(absint($_REQUEST['member_id']));
        return $this->add();
    }

    function add() {
        $form = apply_filters('swpm_admin_registration_form_override', '');
        if (!empty($form)) {
            echo $form;
            return;
        }
        global $wpdb;
        $member = SwpmTransfer::$default_fields;
        $member['member_since'] = date('Y-m-d');
        $member['subscription_starts'] = date('Y-m-d');
        if (isset($_POST['createswpmuser'])) {
            $member = $_POST;
        }
        extract($member, EXTR_SKIP);
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        $levels = $wpdb->get_results($query, ARRAY_A);
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_add.php');
        return false;
    }

    function edit($id) {
        global $wpdb;
        $id = absint($id);
        $query = "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = $id";
        $member = $wpdb->get_row($query, ARRAY_A);
        if (isset($_POST["editswpmuser"])) {
            $_POST['user_name'] = $member['user_name'];
            $_POST['email'] = $member['email'];
            $member = $_POST;
        }
        extract($member, EXTR_SKIP);
        $query = "SELECT * FROM " . $wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        $levels = $wpdb->get_results($query, ARRAY_A);
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_edit.php');
        return false;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered... //print_r($_REQUEST);
        if ('bulk_delete' === $this->current_action()) {
            $records_to_delete = $_REQUEST['members'];
            if (empty($records_to_delete)) {
                echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
                return;
            }
            foreach ($records_to_delete as $record_id) {
                SwpmMembers::delete_user_by_id($record_id);
            }
            echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
        }
    }

    function delete() {
        if (isset($_REQUEST['member_id'])) {
            $id = absint($_REQUEST['member_id']);
            SwpmMembers::delete_user_by_id($id);
        }
    }

    public static function delete_user_by_id($id) {
        $user_name = SwpmUtils::get_user_by_id($id);
        SwpmMembers::delete_wp_user($user_name);
        SwpmMembers::delete_swpm_user_by_id($id);
    }

    public static function delete_swpm_user_by_id($id) {
        global $wpdb;
        $query = "DELETE FROM " . $wpdb->prefix . "swpm_members_tbl WHERE member_id = $id";
        $wpdb->query($query);
    }

    function show() {
        ob_start();
        include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'views/admin_members_list.php');
        $output = ob_get_clean();
        return $output;
    }

    public static function delete_wp_user($user_name) {
        $wp_user_id = username_exists($user_name);
        $ud = get_userdata($wp_user_id);
        if (!empty($ud) && (isset($ud->wp_capabilities['administrator']) || $ud->wp_user_level == 10)) {
            SwpmTransfer::get_instance()->set('status', 'For consistency, we do not allow deleting any associated wordpress account with administrator role.<br/>'
                    . 'Please delete from <a href="users.php">Users</a> menu.');
            return;
        }
        if ($wp_user_id) {
            include_once(ABSPATH . 'wp-admin/includes/user.php');
            wp_delete_user($wp_user_id, 1); //assigns all related to this user to admin.
        }
    }

}
