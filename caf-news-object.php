<?php

// TODO validation des emails
// TODO validation des autres champs

class caf_newsletter_object
{
	private $template_name = '';
	private $content;

	// convert the content of the theme in html
	private function getRenderedHTML($path)
	{
		ob_start();
		include($path);
		$var=ob_get_contents(); 
		ob_end_clean();
		return $var;
	}
	// Get the theme and send it to the recipients
	public function send_newsletter($recipient)
	{
		$this->content =  $this->getRenderedHTML($this->template_name);
		if ($this->template_name!='')
		{
	   		global $wpdb;
			add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
			$header = 'From: CAF StJulien <no-reply@cafsaintjulien.net>';
			$object = 'La news Letter du CAF Saint-Ju!';
			//echo $content;
			$result = wp_mail($recipient, $object, $this->content, $header);
			// Reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
			remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		}
		else
		{
			echo 'template: ' . $this->template_name;
		}
	}
	public function view()
	{
		$this->content =  $this->getRenderedHTML($this->template_name);
		$vue = '<div class="postbox">
				<div class="inside">
					<table>
						<tr>'
	    		. $this->content .
					'</tr>
				</table>
			</div>
		</div>';
		return $vue;
	}
	// Set the wp_mail to send html content
	public function set_html_content_type() {
		return 'text/html';
	}
	// Set the the plugin to send
	// todo: create a parametter to set it in the UI
	private function set_theme()
	{
		$this->template_name = dirname( __FILE__ ) . '/themes/CAFSJ/theme.php';
//		$this->template_name = dirname( __FILE__ ) . '/themes/CAFStJu/simple.php';
	}
	public function __construct()
	{
		$this->set_theme();
	}
}
