<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class My_Example_List_Table extends WP_List_Table {
	/*var $example_data = array(
            array( 'ID' => 1,'booktitle' => 'Quarter Share', 'author' => 'Nathan Lowell', 
                   'isbn' => '978-0982514542' ),
            array( 'ID' => 2, 'booktitle' => '7th Son: Descent','author' => 'J. C. Hutchins',
                   'isbn' => '0312384378' ),
            array( 'ID' => 3, 'booktitle' => 'Shadowmagic', 'author' => 'John Lenahan',
                   'isbn' => '978-1905548927' ),
            array( 'ID' => 4, 'booktitle' => 'The Crown Conspiracy', 'author' => 'Michael J. Sullivan',
                   'isbn' => '978-0979621130' ),
            array( 'ID' => 5, 'booktitle'     => 'Max Quick: The Pocket and the Pendant', 'author'    => 'Mark Jeffrey',
                   'isbn' => '978-0061988929' ),
            array(' ID' => 6, 'booktitle' => 'Jack Wakes Up: A Novel', 'author' => 'Seth Harwood',
                  'isbn' => '978-0307454355' )
        );*/
    function __construct(){
    	global $status, $page;
	        parent::__construct( array(
	            'singular'  => __( 'image', 'mylisttable' ),     //singular name of the listed records
	            'plural'    => __( 'images', 'mylisttable' ),   //plural name of the listed records
	            'ajax'      => false        //does this table support ajax?
	    ) );
    }
  	function column_default( $item, $column_name ) {
	    switch( $column_name ) { 
	        case 'imagefile':
	        case 'uemail':
	        case 'createddate':
	            return $item[ $column_name ];
	        default:
	            return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
   		}
  	}
	function get_columns(){
        $columns = array(
            'imagefile' => __( 'ImageFile', 'mylisttable' ),
            'uemail'    => __( 'Email', 'mylisttable' ),
            'createddate'      => __( 'Created On', 'mylisttable' )
        );
         return $columns;
    }
	function prepare_items($result) {
	  $columns  = $this->get_columns();
	  $hidden   = array('createddate'      => __( 'Created On', 'mylisttable' ));
	  $sortable = array();
	  $this->_column_headers = array( $columns, $hidden, $sortable );
	  $this->items = $result;
	}
} //class
/*function my_add_menu_items(){
    add_menu_page( 'My Plugin List Table', 'My List Table Example', 'activate_plugins', 'my_list_test', 'my_render_list_page' );
}
add_action( 'admin_menu', 'my_add_menu_items' );
function my_render_list_page(){
  $myListTable = new My_Example_List_Table();
  echo '</pre><div class="wrap"><h2>My List Table Test</h2>'; 
  $myListTable->prepare_items(); 
  $myListTable->display(); 
  echo '</div>'; 
}
*/
