<?php
/*
Plugin Name: YouScribe
Plugin URI: http://wordpress.org/tag/youscribe
Description: YouScribe OpenEmbed WordPress plugin
Version: 1.0
Author: YouScribe
Author URI: http://www.youscribe.com
*/

class YouScribeOembed {
	var $regex = "#^http://([a-z]{2,3}\.)?youscribe.com/catalogue/.*-(?<id>\d*)(\?.*)?$#i";	

	function __construct() {	
		add_action( 'init', array( $this,'enable_oembed' ) );
	}

	function YouScribeOembed() {
		$this->__construct();
	}
	
	function enable_oembed() {	
		wp_oembed_add_provider( $this->regex, 'http://services.youscribe.com/api/v1/oembed/', true);
	}
}

$youscribe = new YouScribeOembed();
