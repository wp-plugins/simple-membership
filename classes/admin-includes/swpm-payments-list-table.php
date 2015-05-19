<?php

include_once(SIMPLE_WP_MEMBERSHIP_PATH . 'classes/common/class-swpm-list-table.php');

class SWPM_Payments_List_Table extends SWPM_List_Table {

    function __construct() {
        global $status, $page;

        //Set parent defaults
        parent::__construct(array(
            'singular' => 'transaction', //singular name of the listed records
            'plural' => 'transactions', //plural name of the listed records
            'ajax' => false //does this table support ajax?
        ));
    }

    function column_default($item, $column_name) {
        //Just print the data for that column
        return $item[$column_name];
    }

    function column_id($item) {

        //Build row actions
        $actions = array(
            /* 'edit' => sprintf('<a href="admin.php?page=simple_wp_membership_payments&edit_txn=%s">Edit</a>', $item['id']),//TODO - Will be implemented in a future date */
            'delete' => sprintf('<a href="admin.php?page=simple_wp_membership_payments&action=delete_txn&id=%s" onclick="return confirm(\'Are you sure you want to delete this record?\')">Delete</a>', $item['id']),
        );

        //Return the refid column contents
        return $item['id'] . $this->row_actions($actions);
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                /* $1%s */ $this->_args['singular'], //Let's reuse singular label (affiliate)
                /* $2%s */ $item['id'] //The value of the checkbox should be the record's key/id
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'id' => 'Row ID',
            'email' => 'Email Address',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'txn_date' => 'Date',
            'txn_id' => 'Transaction ID',
            'payment_amount' => 'Amount',
            'membership_level' => 'Membership Level'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('id', false), //true means its already sorted
            'membership_level' => array('membership_level', false),
        );
        return $sortable_columns;
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }

    function process_bulk_action() {
        //Detect when a bulk action is being triggered... //print_r($_GET);
        if ('delete' === $this->current_action()) {
            $records_to_delete = $_GET['transaction'];
            if (empty($records_to_delete)) {
                echo '<div id="message" class="updated fade"><p>Error! You need to select multiple records to perform a bulk action!</p></div>';
                return;
            }
            foreach ($records_to_delete as $record_id) {
                global $wpdb;
                $payments_table_name = $wpdb->prefix . "swpm_payments_tbl";
                $updatedb = "DELETE FROM $payments_table_name WHERE id='$record_id'";
                $results = $wpdb->query($updatedb);
            }
            echo '<div id="message" class="updated fade"><p>Selected records deleted successfully!</p></div>';
        }
    }

    function delete_record($record_id) {
        global $wpdb;
        $payments_table_name = $wpdb->prefix . "swpm_payments_tbl";
        $delete_command = "DELETE FROM " . $payments_table_name . " WHERE id = '$record_id'";
        $result = $wpdb->query($delete_command);
    }

    function prepare_items() {

        // Lets decide how many records per page to show         
        $per_page = 50;

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $this->process_bulk_action();

        // This checks for sorting input and sorts the data.
        $orderby_column = isset($_GET['orderby']) ? $_GET['orderby'] : '';
        $sort_order = isset($_GET['order']) ? $_GET['order'] : '';
        if (empty($orderby_column)) {
            $orderby_column = "id";
            $sort_order = "DESC";
        }
        global $wpdb;
        $payments_table_name = $wpdb->prefix . "swpm_payments_tbl";

        //pagination requirement
        $current_page = $this->get_pagenum();

        if (isset($_POST['swpm_txn_search'])) {//Only load the searched records
            $search_term = trim(strip_tags($_POST['swpm_txn_search']));
            $prepare_query = $wpdb->prepare("SELECT * FROM " . $payments_table_name . " WHERE `email` LIKE '%%%s%%' OR `txn_id` LIKE '%%%s%%' OR `first_name` LIKE '%%%s%%' OR `last_name` LIKE '%%%s%%'", $search_term, $search_term, $search_term, $search_term);
            $data = $wpdb->get_results($prepare_query, ARRAY_A);
            $total_items = count($data);
        } else {//Load all data in an optimized way (so it is only loading data for the current page)
            $query = "SELECT COUNT(*) FROM $payments_table_name";
            $total_items = $wpdb->get_var($query);

            //pagination requirement
            $query = "SELECT * FROM $payments_table_name ORDER BY $orderby_column $sort_order";

            $offset = ($current_page - 1) * $per_page;
            $query.=' LIMIT ' . (int) $offset . ',' . (int) $per_page;

            $data = $wpdb->get_results($query, ARRAY_A);
        }

        // Now we add our *sorted* data to the items property, where it can be used by the rest of the class.
        $this->items = $data;

        //pagination requirement
        $this->set_pagination_args(array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
        ));
    }

}
