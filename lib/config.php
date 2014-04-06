<?php

class Config {
	public function __construct($data) {
		//register_nav_menus( $data['nav_menus'] );
		if (is_admin()) {
			add_editor_style( $data['editor_style'] );
			update_user_meta( $user_id, 'show_welcome_panel', 0 );
		} else {
			add_filter( 'show_admin_bar', '__return_false' );
			//foreach($data['remove_header_actions'] as $action) remove_action('wp_head', $action);
		}

		add_theme_support( 'post-thumbnails' );
	}
}


?>
