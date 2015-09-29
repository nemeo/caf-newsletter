<?php
/*
Plugin Name: caf-news
Plugin URI: http://cafsaintjulien.net
Description: Un plugin d'envoie de newsletter automatisée
Version: 1.1.0
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
$cafnews_version = '1.1.0';

// TODO calendrier pour le choix des dates

class caf_newsletter extends caf_newsletter_object
{
	private $foo = TRUE;
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
		// the buttons to validate / cancel the flush of the users db apear
		if (isset($_POST['purge_validate'])) 
		{
			$this->foo = FALSE;
	    	}
		// the action is canceled by the clever user
		if (isset($_POST['purge_cancel'])) 
		{
			$this->foo = TRUE;
	    	}
		// they are all stupid, no longer need them anymore 
		if (isset($_POST['purge_table'])) 
		{
			$this->purge_table();
			$this->foo = TRUE;
	    	}
		// set the automatic send
		if (isset($_POST['automatic_send_newsletter'])) 
		{
			$this->automatic_send_activation();
	    	}
		// reset the automatic send
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
		if  ($this->foo)
		{
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="purge_validate" value="1"/>';
			submit_button('Vider la table maintenant');
			echo '</form>';
		}
		else
		{
			echo '<table><tr><td>';
			echo '<form method="post" action="">';
			echo '<input display="inline" type="hidden" name="purge_table" value="1"/>';
		    	submit_button('Je suis un fou!');
			echo '</form>';
			echo '</td><td>';
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="purge_cancel" value="1"/>';
		    	submit_button('Euhh en fait non...');
			echo '</form>';
			echo '</td></tr></table>';
		}
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
							<th>nom</th>
							<th>email</th>
							<th>active</th>
							<th>newsletter</th>
							<th>newsletter_ea</th>
							<th>newsletter_ee</th>
							<th>tester</th>
						</tr>
    						<?php foreach ($readers as $_reader) {?>
						<tr>
							<td><?php echo $_reader->name;?></th>
							<td><?php echo $_reader->email;?></th>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->active=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->newsletter=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->newsletter_ea=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->newsletter_ee=='1'){echo ' checked="checked';}?>"/></td>
							<td><input type="checkbox" onclick="return false"<?php if ($_reader->tester=='1'){echo ' checked="checked';}?>"/></td>
						</tr>
						<?php }?>
					</table>
				</div>
			</div>
		<?php
		}	
	}
	public function purge_table()
	{
		global $wpdb;
		//$wpdb->query("DROP TABLE {$wpdb->prefix}caf_newsletter_users;");
		$wpdb->query("DELETE FROM {$wpdb->prefix}caf_newsletter_users;");
		$this->foo = FALSE;
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
		$this->send_news();
	    }
	    // tester email
	    $this->send_betatest();
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
		register_uninstall_hook(__FILE__, array('$manage', 'uninstall'));

		// le menu d'administration du plugin
		add_action('admin_menu', array($this, 'add_admin_menu'),20);
		// Constructor of the newsletter pluggin
		parent::__construct();
	}
}

new caf_newsletter();

