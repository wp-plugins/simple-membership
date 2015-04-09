<?php
if( ! class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class BMembershipLevels extends WP_List_Table{
    function __construct(){
        parent::__construct(array(
            'singular'=>BUtils::_('Membership Level'),
            'plural'  => BUtils::_('Membership Levels'),
            'ajax'    => false
        ));
    }
    function get_columns(){
        return array(
            'cb' => '<input type="checkbox" />'
            ,'id'=>BUtils::_('ID')
            ,'alias'=>BUtils::_('Membership Level')
            ,'role'=>BUtils::_('Role')
            ,'valid_for'=>BUtils::_('Access Valid For/Until')
            );
    }
    function get_sortable_columns(){
        return array(
            'id' => array('id',true),
            'alias' => array('alias',true)
        );
    }
    function get_bulk_actions() {
        $actions = array(
            'bulk_delete'    => BUtils::_('Delete')
        );
        return $actions;
    }
    function column_default($item, $column_name){
        if($column_name == 'valid_for'){
            if($item['subscription_duration_type'] == BMembershipLevel::NO_EXPIRY) {return 'No Expiry';}
            if($item['subscription_duration_type'] == BMembershipLevel::FIXED_DATE) {return date(get_option('date_format'), strtotime($item['subscription_period']));}
            if($item['subscription_duration_type'] == BMembershipLevel::DAYS) {return $item['subscription_period'] ." Day(s)";}
            if($item['subscription_duration_type'] == BMembershipLevel::WEEKS) {return $item['subscription_period'] ." Week(s)";}
            if($item['subscription_duration_type'] == BMembershipLevel::MONTHS) {return $item['subscription_period'] ." Month(s)";}
            if($item['subscription_duration_type'] == BMembershipLevel::YEARS) {return $item['subscription_period'] ." Year(s)";}
        }
        if($column_name == 'role') {return ucfirst($item['role']);}
    	return stripslashes($item[$column_name]);
    }
    function column_id($item){
        $actions = array(
            'edit'  	=> sprintf('<a href="admin.php?page=%s&level_action=edit&id=%s">Edit</a>',
									$_REQUEST['page'],$item['id']),
            'delete'    => sprintf('<a href="?page=%s&level_action=delete&id=%s"
                                    onclick="return confirm(\'Are you sure you want to delete this entry?\')">Delete</a>',
                                    $_REQUEST['page'],$item['id']),
        );
        return $item['id'] . $this->row_actions($actions);
    }
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="ids[]" value="%s" />', $item['id']
        );
    }
    function prepare_items() {
        global $wpdb;
        $query  = "SELECT * FROM " .$wpdb->prefix . "swpm_membership_tbl WHERE  id !=1 ";
        if(isset($_POST['s'])) $query .= " AND alias LIKE '%" . strip_tags($_POST['s']). "%' ";
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'DESC';
        
        $sortable_columns = $this->get_sortable_columns();
        $orderby = BUtils::sanitize_value_by_array($orderby, $sortable_columns);
        $order = BUtils::sanitize_value_by_array($order, array('DESC' => '1', 'ASC' => '1'));
        
        if(!empty($orderby) && !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }
        
        $totalitems = $wpdb->query($query); //return the total number of affected rows
        $perpage = 20;
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
        $totalpages = ceil($totalitems/$perpage);
        if(!empty($paged) && !empty($perpage)){
            $offset=($paged-1)*$perpage;
	        $query.=' LIMIT '.(int)$offset.','.(int)$perpage;
        }
        $this->set_pagination_args( array(
            "total_items" => $totalitems,
            "total_pages" => $totalpages,
            "per_page" => $perpage,
        ) );

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $wpdb->get_results($query, ARRAY_A);
    }
    function no_items() {
        BUtils::e( 'No membership levels found.' );
    }
    function process_form_request(){
        if(isset($_REQUEST['id'])){
            return $this->edit($_REQUEST['id']);
        }
        return $this->add();

    }
    function add(){
        global $wpdb;
        $member = BTransfer::$default_fields;
        if(isset($_POST['createswpmlevel'])){
            $member = $_POST;
        }
        extract($member, EXTR_SKIP);
        include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_add_level.php');
        return false;
    }
    function edit($id){
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}swpm_membership_tbl WHERE id = %d", absint($id));
        $membership = $wpdb->get_row($query, ARRAY_A);
        extract($membership, EXTR_SKIP);
        include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_edit_level.php');
        return false;
    }
    function delete(){
        global $wpdb;
        if (isset($_REQUEST['ids'])){
            $members = $_REQUEST['ids'];
            if(!empty($members)){
                $members = array_map('absint', $members);
                $members = implode(',', $members);
                $query = "DELETE FROM " .$wpdb->prefix . "swpm_membership_tbl WHERE id IN (" . $members . ")";
                $wpdb->query($query);
            }
        }
        else if(isset($_REQUEST['id'])){
            $id = absint($_REQUEST['id']);
            $query = $wpdb->prepare("DELETE FROM " .$wpdb->prefix . "swpm_membership_tbl WHERE id = %d", $id);
            $wpdb->query($query);
        }        
    }
    function show(){
        $selected = 1;
        include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_membership_levels.php');
    }
    function manage(){
        $selected = 2;
         include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_membership_manage.php');
    }
    function manage_categroy(){
        $selected = 3;
        include_once('class.bCategoryList.php');
        $category_list = new BCategoryList();        
        include_once(SIMPLE_WP_MEMBERSHIP_PATH.'views/admin_category_list.php');
    }    
}

