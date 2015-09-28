<?php
global $newsletter; // Newsletter object
global $post; // Current post managed by WordPress
require_once (WP_PLUGIN_DIR . '/my-calendar/my-calendar.php');
/*
 * Some variabled are prepared by Newsletter Plus and are available inside the theme,
 * for example the theme options used to build the email body as configured by blog
 * owner.
 *
 * $theme_options - is an associative array with theme options: every option starts
 * with "theme_" as required. See the theme-options.php file for details.
 * Inside that array there are the autmated email options as well, if needed.
 * A special value can be present in theme_options and is the "last_run" which indicates
 * when th automated email has been composed last time. Is should be used to find if
 * there are now posts or not.
 *
 * $is_test - if true it means we are composing an email for test purpose.
 */

$theme_name = 'CAFSJ';

$theme_url = get_option('siteurl') . '/wp-content/plugins/caf-news/themes/' . $theme_name . '/';

// This array will be passed to WordPress to extract the posts
$filters = array();

// Maximum number of post to retrieve
$filters['showposts'] = 5;

$theme_subject = 'La news Letter du CAF Saint-Ju!';

// Retrieve the posts asking them to WordPress
$posts = get_posts($filters);?>

<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <style>
            * {
                font-family: <?php echo $font; ?>;
                font-size: <?php echo $font_size; ?>;
            }
        </style>
    </head>
    <body style="font:normal 11px helvetica,sans-serif;">

        <table style="background:#ffffff" width="600" align="center" border="0" cellpadding="0" cellspacing="0">

            <tr>
                <td style="color:#9ab;font:normal 11px helvetica,sans-serif;text-align:center;padding:10px 0 20px 0">Desinscription: envoyez un message à: , <a target="_blank"  href="mailto:news-unsubscribe@cafsaintjulien.net">news-unsubscribe@cafsaintjulien.net</a>.</td>
            </tr>

            <tr>
                <td><img src="<?php echo $theme_url; ?>cropped-banner.jpg" alt=""></td>
            </tr>

            <tr>

                <td style="border:1px dotted #e1e2e3;border-top:none;border-bottom:3px solid #e1e2e3;background:#ffffff">



                    <table width="100%" align="center" border="0" cellpadding="20" cellspacing="0">

                        <tr>
                            <td style="background:#ffffff">



                                <p style="color:#456;font-family:arial,sans-serif;font-size:24px;line-height:1.2;margin:15px 0;padding:0"><a target="_tab" href="<?php echo get_option('home'); ?>" style="color:#28c;text-decoration:none" target="_blank"><img src="<?php echo $theme_url; ?>/vie-au-club-40x40.png" alt=""><strong> <?php echo get_option('blogname'); ?></strong></a></p>
				</br>
				</br>
				<p style="font-family:arial,sans-serif;color:#456;font-size:20px;line-height:22px;margin:0;padding:0"><strong>Les prochaines permanences du club</strong></p>

<?php echo do_shortcode('[my_calendar_upcoming before="0" after="2" type="event" category=24 fallback="Pas de permanence prévue!" template="<strong>{daterange}, {timerange} - <i>{host}</i></strong><br>{shortdesc}" order="asc" show_today="yes" skip="0"]');?>

				</br>
				</br>
				<p style="font-family:arial,sans-serif;color:#456;font-size:20px;line-height:22px;margin:0;padding:0"><strong>Les derniers articles et un peu plus bas les prochaines sorties, bonne lecture!</strong></p>
				</br>
				</br>
                                <?php
                                foreach ($posts as $post) {
                                    setup_postdata($post);
                                    //$image = nt_post_image(get_the_ID());
                                    ?>


                                    <table style="width:100%;color:#456;font:normal 12px/1.5em helvetica,sans-serif;margin:15px 0 0 0;padding:0 0 15px 0;border-bottom:1px dotted #e1e2e3">

                                        <tbody><tr>

                                                <td style="width:100%;padding:0 10px 0 0;vertical-align:top">

                                                    <p style="font-family:arial,sans-serif;color:#456;font-size:20px;line-height:22px;margin:0;padding:0"><strong><a target="_tab" href="<?php echo get_permalink(); ?>" style="color:#456;text-decoration:none" target="_blank"><?php if (function_exists('get_cat_icon')) get_cat_icon(); ?>  <?php the_title(); ?></a></strong></p>

                                                    <p style="font-family:arial,sans-serif;line-height:1.5em;margin:15px 0;padding:0"><?php the_excerpt(); ?>. </p>

                                                </td>

                                                <td style="vertical-align:middle; width: 100px">

                                                    <a target="_tab" href="<?php echo get_permalink(); ?>" target="_blank"><img src="<?php /*echo $image*/; ?>" alt="" width="100" border="0" height="100"></a>

                                                    <p style="background:#2786c2;text-align:center;margin:10px 0 0 0;font-size:11px;line-height:14px;font-family:arial,sans-serif;padding:4px 2px;border-radius:4px"><a target="_tab" href="<?php echo get_permalink(); ?>" style="color:#fff;text-decoration:none" target="_blank"><strong><?php echo 'Lire'; ?></strong></a></p>

                                                </td>

                                            </tr>

                                        </tbody></table>

                                    <br>
                                    <?php
                                }
                                ?>
				</br>
				</br>
				<p style="font-family:arial,sans-serif;color:#456;font-size:20px;line-height:22px;margin:0;padding:0"><strong>Voici un apperçu des sortie à venir</strong></p>

				<br><p>Si vous voulez participer à l'une d'entre elles, il est impératif de vous inscrire auprès de l'encadrant responsable. 
Si vous avez des questions ou que vous voulez vous inscrire, vous pouvez <a href="http://cafsaintjulien.net/?p=2309" title="Encadrants" target="_blank">contacter les encadrants organisant ces sorties ICI!</a></p>
<?php echo do_shortcode('[my_calendar_upcoming before="0" after="10" type="event" category=2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,25 fallback="Pas de sortie prévue pour le moment." template="{icon_html} <strong><u>{daterange}</u></strong><strong><br>{details}</strong><br><i>Organisée par {host}</i><br>{shortdesc}" order="asc" show_today="yes" skip="0"]');?>

<style="font-family:arial,sans-serif;font-size:20px;line-height:22px;margin:0;padding:0"></style>

				<br><p>L'encadrant devra valider les inscriptions. S'il le juge nécessaire, une réunion préparatoire pourra être organisée au club et il fera son possible pour vous informé des modifications ou de l'annulation de sa sortie en cas de pépin.
Il est donc important de lui communiquer vos coordonnées (téléphoniques)!
Pour rappel, l'<strong>adhésion au Club Alpin Français est obligatoire</strong> pour participer aux sorties.
Cependant, l'achat d'une carte découverte est toujours possible pour les non-affiliés.</p>

                                <br><br>
                                <p style="color:#456;font-family:arial,sans-serif;font-size:12px;line-height:1.6em;font-style:italic;margin:0 0 15px 0;padding:0">
                                    Desinscription: envoyez un message à: , <a target="_blank"  href="mailto:news-unsubscribe@cafsaintjulien.net">news-unsubscribe@cafsaintjulien.net</a>.
                                </p>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>
         </table>

    </body>
</html>

