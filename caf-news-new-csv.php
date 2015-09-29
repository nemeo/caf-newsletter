<?php

// TODO validation des emails
// TODO validation des autres champs

class caf_newsletter_new_csv
{
	private $foo = TRUE;
	private $maxsize = 1048576;
	private $csv_name = '';

	public function new_html()
	{
	    	echo '<h1>'.get_admin_page_title().'</h1>';

		if ($this->foo)
		{
		// avertissement quant au format du fichier
		echo '<strong>Attention le CSV doit impérativement être composé de la manière suivante:</strong><br/><br/>
						"email","nom","valide","newsletter","newsletter_ea","newsletter_ee","tester"<br/>
			par exemple		"martine@la.plage","martine","1","1","0","1","0"<br/>
			pour les testeurs 	"martine@la.plage","martine","1","0","0","0","1" <br/>
			s\'il manque des bouts	"martine@la.plage",,"1",,"1", <br/>
			au pire			"martine@la.plage" là les valeurs par défaut seront appliquées!<br/><br/>';
		// upload du fichier csv
		$url = admin_url() . 'admin.php?page=caf_newsletter_new_csv';
		echo '<form method="post" action="' . $url . '" enctype="multipart/form-data">
			<label for="mon_csv">Fichier (*.csv | max. 1 Mo) :</label><br />
			<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->maxsize . '" />
			<input type="file" name="mon_csv" id="mon_fichier"/><br />
			<input type="hidden" name="send" value="1"/>
			<label for="separateur">Séparateur</label><br />
			<input type="text" name="separateur" value=","/>';
	    		submit_button('Envoyer!');
		echo '</form>';
		// flush de la base
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="clean_users" value="1"/>';
	    	submit_button('Vider la base des lecteurs!');
		echo '</form>';
		}
		else
		{
			echo '<form method="post" action="">';
			echo '<input type="hidden" name="clean_users_validate" value="1"/>';
		    	submit_button('T\'ES SURE!');
			echo '</form>';
		}
		//
	}
	public function traite_csv()
	{
		if ($_FILES['mon_csv']['error'] > 0) $erreur = "Erreur lors du transfert";
			else if ($_FILES['mon_csv']['size'] > $this->maxsize) $erreur = "Le fichier est trop gros";
				else
				{
					$extensions_valides = array( 'csv' );
					//1. strrchr renvoie l'extension avec le point (« . »).
					//2. substr(chaine,1) ignore le premier caractère de chaine.
					//3. strtolower met l'extension en minuscules.
					$extension_upload = strtolower(  substr(  strrchr($_FILES['mon_csv']['name'], '.')  ,1)  );
					if ( in_array($extension_upload,$extensions_valides) )
					{
						$this->csv_name = dirname( __FILE__ ) . '/uploads/' . $_FILES['mon_csv']['name'];
						$resultat = move_uploaded_file($_FILES['mon_csv']['tmp_name'],$this->csv_name);
						if ($resultat)
						{
							echo $_FILES['mon_csv']['name'] . ' transfert réussi<br/>';
							$this->save();
						}
					}
				}
	}
	public function delete_csv()
	{
		$resultat = unlink($this->csv_name);
		if ($resultat) echo 'Fichier supprimé<br/>';
		
	}
	public function save()
	{
		global $wpdb;
		$ligne = 0; // compteur de ligne
		$ligne_filled = 0; // compteur de ligne non vide
		$ligne_exist = 0; // compteur de ligne existante
//		$filename = dirname( __FILE__ ) . '/'.$_POST['filename'];
		$separator = $_POST['separateur'];
		$fic = fopen($this->csv_name, "r"); // ouverture du fichier
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
								'newsletter_ea' =>  $tab[$i],
								'newsletter_ee' =>  '0',
								'tester' =>  '0'));
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
								'newsletter_ea' => '0',
								'newsletter_ee' =>  '0',
								'tester' =>  '0'));
						$ligne ++;
					}
					else
					{
						$ligne_exist ++;
					}
				}
				if ($champs==7)
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
								'newsletter_ea' =>  $tab[$i++],
								'newsletter_ee' =>  $tab[$i++],
								'tester' =>  $tab[$i]));
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
		echo "<b> Les " . $ligne . " lecteurs ajouter dans la base et " . $ligne_exist . " déjà existants.<br/> Le fichier contient " . $ligne_filled . " coordonnées valides</b><br />";
		$this->delete_csv();
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
		if (isset($_POST['send']))
		{
			$this->traite_csv();
		}
	}
	public function __construct()
	{
		$hook_insert = add_submenu_page('caf_newsletter', 'Ajouter une liste d\'utilisateur à la liste', 'CSV', 'manage_options', 'caf_newsletter_new_csv', array($this, 'new_html'));
		add_action('load-'.$hook_insert, array($this, 'process_registration'));
	}
}
