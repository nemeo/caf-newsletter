<?php

// TODO validation des emails
// TODO validation des autres champs

class caf_newsletter_new_csv
{
	private $foo = TRUE;
	public function new_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';

		if ($this->foo)
		{
		echo '<strong>Attention le CSV doit impérativement être composé de la manière suivante:</strong><br/><br/>
			"email","nom","valide","newsletter","newsletter_ea"<br/>
			"martine@la.plage","martine","1","1","0" ou s\'il manque des bouts<br/>
			"martine@la.plage",,"1","1", et au pire <br/>
			"martine@la.plage" là les valeurs par défaut seront appliquées!<br/><br/>';
		echo '<form method="post" action="">';
		echo '<input type="text" name="filename" value="lecteurs.csv"/>';
		echo '<input type="text" name="separateur" value=","/>';
		echo '<input type="hidden" name="save_registration" value="1"/>';
	    	submit_button('Créer dans la base!');
		echo '</form>';
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="clean_users" value="1"/>';
		    	submit_button('Vider dans la base!');
			echo '</form>';
		}
		else
		{
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="clean_users_validate" value="1"/>';
		    	submit_button('T\'ES SURE!');
			echo '</form>';
		}
	}
	public function save()
	{
		global $wpdb;
		$ligne = 0; // compteur de ligne
		$ligne_filled = 0; // compteur de ligne non vide
		$ligne_exist = 0; // compteur de ligne existante
		$filename = dirname( __FILE__ ) . '/'.$_POST['filename'];
		$separator = $_POST['separateur'];
		$fic = fopen($filename, "r"); // ouverture du fichier
		while($tab=fgetcsv($fic,1024,$separator))
		{
			$champs = count($tab);	//nombre de champ dans la ligne en question
			if ($tab[0]!='') // il faut au minimum que le champ des emails soit là
			{
				if ($champs==5)
				{
					$i=0;
					$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}caf_newsletter_users WHERE email = '$tab[$i]'");
					if (is_null($row)) 
					{
						$wpdb->insert("{$wpdb->prefix}caf_newsletter_users", 
							 array(	'email' =>  $tab[$i++],
								'name' => $tab[$i++],
								'active' =>  $tab[$i++],
								'newsletter' =>  $tab[$i++],
								'newsletter_ea' =>  $tab[$i]));
						$ligne ++;
					}
					else
					{
						$ligne_exist ++;
					}
				}
				else if ($champs==1)
				{
					$i=0;
					$row = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}caf_newsletter_users WHERE email = '$tab[$i]'");
					if (is_null($row)) 
					{
				
						$wpdb->insert("{$wpdb->prefix}caf_newsletter_users", 
							 array(	'email' => $tab[0],
								'name' => '',
								'active' => '1',
								'newsletter' => '1',
								'newsletter_ea' => '0'));
						$ligne ++;
					}
					else
					{
						$ligne_exist ++;
					}
				}
				$ligne_filled ++;
			}
		}
		echo "<b> Les " . $ligne . " lecteurs ajouter dans la base et " . $ligne_exist . " déjà existants. le fichier contiens " . $ligne_filled . " coordonnées valides</b><br />";
	}
	public function clean()
	{
		global $wpdb;
		echo 'HÉ BIEN C\'EST MALIN!!!!';
		$delete = $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}caf_newsletter_users");
	}
	public function process_registration()
	{
	 	if (isset($_POST['save_registration']))
		{
			$this->save();
		}
		if (isset($_POST['clean_users']))
		{
			$this->foo = FALSE;
		}
		if (isset($_POST['clean_users_validate']))
		{
			$this->clean();
			$this->foo = TRUE;
		}
	}
	public function __construct()
	{
		$hook_insert = add_submenu_page('caf_newsletter', 'Ajouter une liste d\'utilisateur à la liste', 'CSV', 'manage_options', 'caf_newsletter_new_csv', array($this, 'new_html'));
		add_action('load-'.$hook_insert, array($this, 'process_registration'));
	}
}
