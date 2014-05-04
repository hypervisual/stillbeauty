<?php

class TransactionsList extends WP_List_Table {
	public $filter = '', $pattern = '';

    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'transaction',     //singular name of the listed records
            'plural'    => 'transactions',    //plural name of the listed records
            'ajax'      => false         //does this table support ajax?
        ) );
        
    }

	function get_columns() {
		$columns = array('cb' => '<input type="checkbox" />',
						 'tx_ref' => 'Reference',
						 'tx_date' => 'Date',
						 'promo' => 'Promotion',
						 'type' => 'Type',
			             'status' => 'Status');

		return $columns;
	}

	function search_box($a, $b) {
		$opt = array( array('value' => '', 'text' => '-- No filter'),
					  array('value' => 'tx_ref', 'text' => 'Reference'),
					  array('value' => 'type', 'text' => 'Type'),
					  array('value' => 'status', 'text' => 'Status'),);

		$options = "";

		foreach ($opt as $o) {
			if ($o['value'] == $this->term) {
				$options .= '<option value="' . $o['value'] . '" selected="selected">' . $o['text'] . "</option>";
			} else {
				$options .= '<option value="' . $o['value'] . '">' . $o['text'] . "</option>";
			}
		}

		$search  = '<p class="search-box">';
		$search .= '<strong>Filter by</strong>&nbsp;&nbsp;';
		$search .= '<select id="tx_search_term" name="term">' . $options . '</select>';
		$search .= '&nbsp;&nbsp;<strong>for value</strong>&nbsp;&nbsp';
		$search .= '<input id="tx_search_pattern" type="text" value="' . $this->pattern . '" name="pattern">';
		$search .= '<input id="search-submit" value="GO" blass="button" type="submit" />';
		$search .= '</p>';

		echo $search;
	}

	function get_bulk_actions() {
    	$actions = array(
    						'delete'    => 'Delete'
  						);
  		return $actions;
	}

	function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        /*
        if( 'delete'===$this->current_action() ) {
 
        }
        */
	}

	function column_cb($item) {
        return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item->id);
    }

	function column_tx_ref($item) {
	      $name = stripslashes($item->tx_ref);
	      $actions = array(
            'view'      => sprintf('<a href="?page=%s&action=%s&id=%d">View</a>',$_REQUEST['page'],'view-transaction',$item->id),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%d">Delete</a>',$_REQUEST['page'],'remove-transaction',$item->id)
        );
  		return sprintf('%1$s %2$s', $name, $this->row_actions($actions) );
	}

	function prepare_items() {
		global $wpdb;

		//$this->process_bulk_action();

		if (array_key_exists('pattern', $_REQUEST)) $this->pattern = $_REQUEST['pattern'];
		if (array_key_exists('term', $_REQUEST)) $this->term = $_REQUEST['term'];

		if(!empty($this->pattern) && $this->term != '') {
			$search_for = " WHERE LOWER(" . $this->term . ") LIKE LOWER('%" . $this->pattern . "%')";
		} else {
			$search_for = "";
			$this->term = "";
			$this->pattern = "";
		}

		if (array_key_exists('orderby', $_GET)) {
			$orderby = " ORDER BY " . $_GET['orderby'] . " " . $_GET['order'];
		} else  {
			$orderby = " ORDER BY id DESC";
		}

		$sql = "SELECT count(*) FROM sb_transactions ";
		if (!empty($search_for)) $sql .=  $search_for;

		$total_items = $wpdb->get_var($sql);

		$per_page = 10;

		$curpage = $this->get_pagenum();

		$this->set_pagination_args( array(
    		'total_items' => $total_items,                  //WE have to calculate the total number of items
    		'per_page'    => $per_page                      //WE have to determine how many items to show on a page
  		) );

  		$limit = " LIMIT " . (($curpage-1)*$per_page) . ", " . $per_page;

		$sql = "SELECT * FROM sb_transactions " . $search_for . $orderby . $limit;

		$data = $wpdb->get_results($sql);

		$columns = $this->get_columns();
		$hidden  = array();
		$sortable = array('date' => array('date', false),
			              'type' => array('type', false),
			              'status' => array('status', false));

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	function column_default( $item, $column_name ) {
	  switch( $column_name ) {
	    case 'tx_ref':
	    case 'status':
	    case 'type':
	    case 'tx_date':
	    case 'promo':

	      return stripslashes($item->$column_name);

	    default:
	      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  }
	}


}