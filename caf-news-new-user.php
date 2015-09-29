<?php
class caf_newsletter_new_user
{
	public function new_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';

		// add_settings_section( $id, $title, $callback, $page );

		add_settings_section($this->page_section(), 'Paramètres d\'envoi', array($this, 'section_html'), $this->page_settings());

		//add_settings_field( $id, $title, $callback, $page, $section, $args );

		add_settings_field('name', 'Nom du lecteur', array($this, 'name_html'), $this->page_settings(), $this->page_section());
		add_settings_field('email', 'email de contact', array($this, 'email_html'), $this->page_settings(), $this->page_section());
		add_settings_field('active', 'Activé', array($this, 'active_html'), $this->page_settings(), $this->page_section());
		add_settings_field('newsletter', 'Abonné à la lettre hebdomadaire', array($this, 'newsletter_html'), $this->page_settings(), $this->page_section());
		add_settings_field('newsletter_ea', 'Abonné à la lettre de l\'EA', array($this, 'newsletter_ea_html'), $this->page_settings(), $this->page_section());
		add_settings_field('newsletter_ee', 'Abonné à la lettre de l\'EE', array($this, 'newsletter_ee_html'), $this->page_settings(), $this->page_section());
		add_settings_field('newsletter_tester', 'Beta tester', array($this, 'newsletter_tester_html'), $this->page_settings(), $this->page_section());

		
		echo '<form method="post" action="options.php">';
		settings_fields($this->page_settings());
		do_settings_sections($this->page_settings());
		if (get_option('email')=='')
		{
		submit_button('Sauvegarder');
		echo '</form>';
		}
		else
		{
			echo '</form><form method="post" action="">';
			echo '<input type="hidden" name="save_registration" value="1"/>';
		    	submit_button('Créer dans la base!');
			echo '</form>';
		}
	}
	public function section_html()
	{
	    echo 'Renseignez les paramètres du nouvel utilisateur.';
	}
	public function name_html()
	{?>
		<input type="text" name="name" value="<?php echo get_option('name')?>"/>
	    	<?php
	}
	public function email_html()
	{?>
		<input type="text" name="email" value="<?php echo get_option('email')?>"/>
	    	<?php
	}
	public function active_html()
	{?>
		<input type="checkbox" value="1" id="active" name="active" <?php if (get_option('active')=='1'){echo 'checked';}?>>
	    <?php
	}
	public function newsletter_html()
	{?>
		<input type="checkbox" value="1" id="newsletter" name="newsletter" <?php if (get_option('newsletter')=='1'){echo 'checked';}?>>
	    <?php
	}
	public function newsletter_ea_html()
	{?>
		<input type="checkbox" value="1" id="newsletter_ea" name="newsletter_ea" <?php if (get_option('newsletter_ea')=='1'){echo 'checked';}?>>
	    <?php
	}
	public function newsletter_ee_html()
	{?>
		<input type="checkbox" value="1" id="newsletter_ee" name="newsletter_ee" <?php if (get_option('newsletter_ee')=='1'){echo 'checked';}?>>
	    <?php
	}
	public function newsletter_tester_html()
	{?>
		<input type="checkbox" value="1" id="newsletter_tester" name="newsletter_tester" <?php if (get_option('newsletter_tester')=='1'){echo 'checked';}?>>
	    <?php
	}
	public function register_settings()
	{
	    register_setting('caf_newsletter_new_user_settings', 'name');
	    register_setting('caf_newsletter_new_user_settings', 'email');
	    register_setting('caf_newsletter_new_user_settings', 'active');
	    register_setting('caf_newsletter_new_user_settings', 'newsletter');
	    register_setting('caf_newsletter_new_user_settings', 'newsletter_ea');
	    register_setting('caf_newsletter_new_user_settings', 'newsletter_ee');
	    register_setting('caf_newsletter_new_user_settings', 'newsletter_tester');
	    register_setting('caf_newsletter_new_user_settings', 'save');
	}
	private function clean_options()
	{
		update_option( 'name', '' );
		update_option( 'email', '' );
		update_option( 'active', '1' );
		update_option( 'newsletter', '1' );
		update_option( 'newsletter_ea', '0' );
		update_option( 'newsletter_ee', '0' );
		update_option( 'newsletter_tester', '0' );
		update_option( 'save', '' );
	}
	public function save()
	{
		global $wpdb;
		$email = get_option('email');
		$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}caf_newsletter_users WHERE email = '$email'");
		if (is_null($row)) {
			$wpdb->insert	("{$wpdb->prefix}caf_newsletter_users", 
					 array(	'name' => get_option('name', ''),
						'email' => get_option('email', ''),
						'active' => get_option('active', '0'),
						'newsletter' => get_option('newsletter', '0'),
						'newsletter_ea' => get_option('newsletter_ea', '0'),
						'newsletter_ee' => get_option('newsletter_ee', '0'),
						'tester' => get_option('newsletter_tester', '0')));
			echo popuplinks('done');
			$this->clean_options();
		}
		else
		{
			echo popuplinks('L\'utilisateur avec l\'addresse '.$email.' est déjà enregistré!');
			$this->clean_options();
		}
	}
	public function process_registration()
	{
	    if (isset($_POST['save_registration'])) {
		$this->save();
	    }
	}
	private function page_settings()
	{
		return 'caf_newsletter_new_user_settings';
	}
	private function page_section()
	{
		return 'caf_newsletter_new_user_section';
	}
	public function __construct()
	{				
		add_action('admin_init', array($this, 'register_settings'));
		
		$hook_insert = add_submenu_page('caf_newsletter', 'Ajouter un utilisateur à la liste', 'Ajouter', 'manage_options', 'caf_newsletter_new_user', array($this, 'new_html'));
		add_action('load-'.$hook_insert, array($this, 'process_registration'));
	}
}
