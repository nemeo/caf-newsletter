<?php

include( dirname( __FILE__ ) . '/caf-news-object.php' );

// TODO validation des emails
// TODO validation des autres champs

class caf_newsletter_newsletter extends caf_newsletter_object
{
	private $template_name = '';
	private $foo = TRUE;

	public function new_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';
		// Send button
		if ($this->foo)
		{
			echo '<form method="post" action="">
				<input type="hidden" name="confirm" value="1"/>';
		    		submit_button('Envoyer la newsletter!');
			echo '</form>';
			echo $this->view();
		}
		else
		{
			echo '<table><tr><td>';
			echo '<form method="post" action="">';
			echo '<input display="inline" type="hidden" name="send" value="1"/>';
		    	submit_button('T\'ES SURE!');
			echo '</form>';
			echo '</td><td>';
			echo '<form method="post" action="">';
			echo '<input display="inline" type="hidden" name="send_test" value="1"/>';
		    	submit_button('Test d\'abord');
			echo '</form>';
			echo '</td></tr></table>';
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="cancel" value="1"/>';
		    	submit_button('En fait non...');
			echo '</form>';
		}
	}
	public function process_registration()
	{
		if (isset($_POST['confirm']))
		{
			$this->foo = FALSE;
		}
		if (isset($_POST['cancel']))
		{
			$this->foo = TRUE;
		}
	 	if (isset($_POST['send']))
		{
			$this->send_newsletter('news@cafsaintjulien.net');
			$this->foo = TRUE;
		}
	 	if (isset($_POST['send_test']))
		{
			$this->send_newsletter('michel.heche@free.fr');
			$this->send_newsletter('lgmiche@gmail.com');
			$this->foo = TRUE;
		}
	}
	public function __construct()
	{
		parent::__construct();
		$hook_insert = add_submenu_page('caf_newsletter', 'Envoyer la newsletter', 'Apperçu', 'manage_options', 'caf_newsletter_newsletter', array($this, 'new_html'));
		add_action('load-'.$hook_insert, array($this, 'process_registration'));
	}
}
