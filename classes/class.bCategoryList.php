<?php

/**
 * BCategoryList
 *
 * @author nur
 */
if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class BCategoryList extends WP_List_Table {

    public $selected_level_id = 1;
    public $category;

    function __construct() {
        parent::__construct(array(
            'singular' => BUtils::_('Membership Level'),
            'plural' => BUtils::_('Membership Levels'),
            'ajax' => false
        ));
        $this->category = array();
        $selected = filter_input(INPUT_POST, 'membership_level_id');
        $this->selected_level_id = empty($selected) ? 1 : $selected;
        $this->category = ($this->selected_level_id == 1) ?
                BProtection::get_instance() :
                BPermission::get_instance($this->selected_level_id);
    }

    function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />'
            , 'term_id' => BUtils::_('ID')
            , 'name' => BUtils::_('Name')
            , 'description' => BUtils::_('Description')
            , 'count' => BUtils::_('Count')
        );
    }

    function get_sortable_columns() {
        return array(
            'name' => array('name', true)
        );
    }

    function column_default($item, $column_name) {
        return stripslashes($item->$column_name);
    }

    function column_term_id($item) {
        return $item->term_id;
    }

    function column_cb($item) {
        return sprintf(
                '<input type="hidden" name="ids_in_page[]" value="%s">
            <input type="checkbox" %s name="ids[]" value="%s" />', $item->term_id, $this->category->in_categories($item->term_id) ? "checked" : "", $item->term_id
        );
    }

    function prepare_items() {
        $submitted = filter_input(INPUT_POST, 'update_category_list');
        if (!empty($submitted)) {
            $args = array('ids' => array(
                    'filter' => FILTER_VALIDATE_INT,
                    'flags' => FILTER_REQUIRE_ARRAY,
            ));
            $filtered = filter_input_array(INPUT_POST, $args);
            $ids = $filtered['ids'];
            $args = array('ids_in_page' => array(
                    'filter' => FILTER_VALIDATE_INT,
                    'flags' => FILTER_REQUIRE_ARRAY,
            ));
            $filtered = filter_input_array(INPUT_POST, $args);
            $ids_in_page = $filtered['ids_in_page'];
            $this->category->remove($ids_in_page, 'category')->apply($ids, 'category')->save();
            $message = array('succeeded' => true, 'message' => BUtils::_('Updated! '));
            BTransfer::get_instance()->set('status', $message);
        }
        $all_categories = array();
        $all_cat_ids = get_categories(array('hide_empty' => '0'));
        $totalitems = count($all_cat_ids);
        $perpage = 100;
        $paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0) {
            $paged = 1;
        }
        $totalpages = ceil($totalitems / $perpage);
        $offset = 0;
        if (!empty($paged) && !empty($perpage)) {
            $offset = ($paged - 1) * $perpage;
        }
        for ($i = $offset; $i < ((int) $offset + (int) $perpage) && !empty($all_cat_ids[$i]); $i++) {
            $all_categories[] = $all_cat_ids[$i];
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
        $this->items = $all_categories;
    }

    function no_items() {
        BUtils::e('No category found.');
    }

}
