<?php
class caf_registration_install
{
	public static function install()
	{
	    	global $wpdb;
		// création de la table qui contiendra les utilisateurs
	    	$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}caf_newsletter_users (
			id INT AUTO_INCREMENT PRIMARY KEY, 
			active BOOLEAN DEFAULT FALSE, 
			newsletter BOOLEAN DEFAULT FALSE, 
			newsletter_ea BOOLEAN DEFAULT FALSE, 
			name VARCHAR(64) NOT NULL, 
			email VARCHAR(255) NOT NULL);"
			);
	}
	public static function uninstall()
	{
	    	global $wpdb;
		// supression des tables
	    	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}caf_newsletter_users;");
	}

}?>
