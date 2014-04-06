<?php

class Utils {
	static public function get_uri_segments($uri) {
		$uri = explode('/', substr($uri, 1));
		$uri[count($uri)-1] = current(explode('?', $uri[count($uri)-1]));
		if ($uri[count($uri)-1] == '') unset($uri[count($uri)-1]);
		return $uri;
	}

	static public function getIPAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
	        $ip=$_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
	        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	        $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}

	static public function getUserID($user_login) {
		global $wpdb;
		return $wpdb->get_var("SELECT ID FROM " . $wpdb->users . " WHERE user_login = '" . $user_login . "'");
	}

	static public function convertToOptions($data, $key) {
		$options = "";

		foreach($data as $item) {
			$options .= '<option value="' . $item->$key['value'] . '">' . stripslashes(ucwords(strtolower($item->$key['text']))) . '</option>';
		}
		return $options;
	}
}