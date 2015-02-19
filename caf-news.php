<?php
/*
Plugin Name: caf-news
Plugin URI: http://cafsaintjulien.net
Description: Un plugin d'inscription aux sorties proposées sur le site avec my calendar.
Version: 0.1
Author: lagrossemiche
Author URI: http://www.lagrossemiche.fr
License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


include( dirname( __FILE__ ) . '/caf-news-install.php' );
include( dirname( __FILE__ ) . '/caf-news-new-user.php' );
include( dirname( __FILE__ ) . '/caf-news-new-csv.php' );

global $cafnews_version, $wpdb;
$cafnews_version = '0.1.01';

// TODO calendrier pour le choix des dates

class caf_newsletter
{
	public function add_admin_menu()
	{
		$hook = add_menu_page('La Newsletter du CAF', 'CAF_NewsLetter', 'manage_options', 'caf_newsletter', array($this, 'menu_html'));
		add_action('load-'.$hook, array($this, 'process_action'));
		$new_user = new caf_newsletter_new_user();
		$new_csv = new caf_newsletter_new_csv();
	}
	public function process_action()
	{
		if (isset($_POST['send_newsletter'])) {
			$this->send_newsletter();
	    	}
	}
	public function send_newsletter()
	{
   		global $wpdb;
		$header = 'From: no-reply@example.com';
		$recipient = 'michel.heche@free.fr';
		$object = 'test';
		$content = 'the_test';
		$result = wp_mail($recipient, $object, $content, $header);
		//$recipients = $wpdb->get_results("SELECT email FROM {$wpdb->prefix}zero_newsletter_email");
		//$object = get_option('zero_newsletter_object', 'Newsletter');
		//$content = get_option('zero_newsletter_content', 'Mon contenu');
		//$sender = get_option('zero_newsletter_sender', 'no-reply@example.com');
		//$header = array('From: '.$sender);
		//foreach ($recipients as $_recipient) {
		//	$result = wp_mail($_recipient, $object, $content, $header);
	}
	public function menu_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';
	    	echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="send_newsletter" value="1"/>';
		submit_button('Envoyer la newsletter');
		echo '</form>';
		$this->list_html();
	}

	public function list_html()
	{
		global $wpdb;
		$readers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}caf_newsletter_users");
		if (!is_null($readers)) {?>
		    	<div class="postbox">
				<h3>Liste des lecteurs</h3>
				<div class="inside">
					<table>
						<tr>
							<th>email</th>
							<th>nom</th>
							<th>active</th>
							<th>newsletter</th>
							<th>newsletter_ea</th>
						</tr>
    						<?php foreach ($readers as $_reader) {?>
						<tr>
							<td><?php echo $_reader->email;?></th>
							<td><?php echo $_reader->name;?></th>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->active=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->newsletter=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->newsletter_ea=='1'){echo ' checked="checked';}?>"/></td>
						</tr>
						<?php }?>
					</table>
				</div>
			</div>
		<?php
		}	
	}
	public function Event_Detail_html()
	{
		$content = '';
		$editor_id = 'mycustomeditor';
		wp_editor( $content, $editor_id );
	}
	public function __construct()
	{
		$manage = new caf_registration_install();
		// il faut bien créer des des tables ...
		register_activation_hook(__FILE__, array($manage, 'install'));

		// il faut aussi savoir s'en séparer ... mais comme c'est dangereux je l'ai commenté
		//register_uninstall_hook(__FILE__, array('$manage', 'uninstall'));

		// le menu d'administration du plugin
		add_action('admin_menu', array($this, 'add_admin_menu'),20);
	}
}

new caf_newsletter();

