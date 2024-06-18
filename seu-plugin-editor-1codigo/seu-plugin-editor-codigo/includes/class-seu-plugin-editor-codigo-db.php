<?php

class Seu_Plugin_Editor_Codigo_DB {

	public static function ativar() {
		global $wpdb;
		$table_name = $wpdb->prefix . "seu_plugin_codigos";
		
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			code_name varchar(255) NOT NULL,
			html_code longtext NOT NULL,
			css_code longtext NOT NULL,
			js_code longtext NOT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function desativar() {
		global $wpdb;
		$table_name = $wpdb->prefix . "seu_plugin_codigos";
		$sql = "DROP TABLE IF EXISTS $table_name;";
		$wpdb->query( $sql );
	}
}
