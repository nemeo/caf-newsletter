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

// todo activation et desactivation du cron
// choix du jour de fdiffusion

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


include( dirname( __FILE__ ) . '/caf-news-install.php' );
include( dirname( __FILE__ ) . '/caf-news-new-user.php' );
include( dirname( __FILE__ ) . '/caf-news-new-csv.php' );
include( dirname( __FILE__ ) . '/caf-news-newsletter.php' );
require_once ( dirname( __FILE__ ) . '/caf-news-object.php' );

global $cafnews_version, $wpdb;
$cafnews_version = '0.1.02';

// TODO calendrier pour le choix des dates

class caf_newsletter extends caf_newsletter_object
{
	private $foo = FALSE;
	public function add_admin_menu()
	{
		$hook = add_menu_page('La Newsletter du CAF', 'CAF_NewsLetter', 'manage_options', 'caf_newsletter', array($this, 'menu_html'));
		add_action('load-'.$hook, array($this, 'process_action'));
		$new_user = new caf_newsletter_new_user();
		$new_csv = new caf_newsletter_new_csv();
		$new_newsletter = new caf_newsletter_newsletter();
	}
	public function process_action()
	{
		if (isset($_POST['send_newsletter'])) 
		{
			$this->do_this();
	    	}
		if (isset($_POST['automatic_send_newsletter'])) 
		{
			$this->automatic_send_activation();
	    	}
		if (isset($_POST['no_send_newsletter'])) 
		{
			$this->automatic_send_deactivation();
	    	}
	}
	// the code for the core of the page
	public function menu_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';
	    	echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
		if(!wp_next_scheduled('caf_automatic_send'))
		{
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="automatic_send_newsletter" value="1"/>';
			submit_button('Activer la newsletter maintenant');
			echo '</form>';
		}
		else
		{
			echo 'il est: ';
			echo date("d-m-Y H:i:s",current_time( 'timestamp' ));
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="no_send_newsletter" value="1"/>';
			submit_button('Desctiver la newsletter maintenant');
			echo '</form>';
			echo 'Prochaine tentative d\'envoie prevu ';
			echo date("d-m-Y H:i:s",wp_next_scheduled('caf_automatic_send'));
		}
		$this->list_html();
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="send_newsletter" value="1"/>';
		submit_button('Envoyer manuellement la newsletter maintenant');
		echo '</form>';
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
	// cron activation to send a letter each day
	public function automatic_send_activation()
	{
		wp_schedule_event( current_time( 'timestamp' ), 'daily', 'caf_automatic_send');
	}

	// cron deactivation to stop sending letters
	public function automatic_send_deactivation() 
	{
		wp_clear_scheduled_hook('caf_automatic_send');
	}
	// send the newsletter on monday
	public function do_this() 
	{
	    // Get the current date time
	    $dateTime = new DateTime();
	    // Check that the day is Monday
	    if($dateTime->format('N') == 1)
	    {
		$this->send_newsletter('news@cafsaintjulien.net');
	    }
	    // tester email
	    $this->send_newsletter('michel.heche@free.fr');
	}
	public function __construct()
	{

		$manage = new caf_news_install();
		// il faut bien créer des des tables ...
		register_activation_hook(__FILE__, array($manage, 'install'));
		
		// activation/desactivation de l'envoie des newsletters avec le cron
		register_activation_hook(__FILE__, array($manage, 'automatic_send_activation'));
		register_deactivation_hook(__FILE__, array($manage, 'automatic_send_deactivation'));
		add_action('caf_automatic_send', array($this, 'do_this'));

		// il faut aussi savoir s'en séparer ... mais comme c'est dangereux je l'ai commenté
		//register_uninstall_hook(__FILE__, array('$manage', 'uninstall'));

		// le menu d'administration du plugin
		add_action('admin_menu', array($this, 'add_admin_menu'),20);
		// Constructor of the newsletter pluggin
		parent::__construct();
	}
}

new caf_newsletter();

