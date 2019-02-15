<?php

require("public/sendgrid-php/sendgrid-php.php");
require("public/key/sendgrid.php");
require("private.php");



	// Ma clé privée
	$secret = $secretKey;
	// Paramètre renvoyé par le recaptcha
	$response = $_POST['g-recaptcha-response'];
	// On récupère l'IP de l'utilisateur
	$remoteip = $_SERVER['REMOTE_ADDR'];

	$api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
	    . $secret
	    . "&response=" . $response
	    . "&remoteip=" . $remoteip ;

	$decode = json_decode(file_get_contents($api_url), true);

	if ($decode['success'] == true) {

// récupération des variables et sécurisation des données reçues
        $nom = htmlentities($_POST['nom']);
        $emailContact = htmlentities($_POST['email']);
        $tel = htmlentities($_POST['tel']);
        $text = htmlentities($_POST['text-area']);


// préparation du mail
        $contenu = '<html><body>';
        $contenu .= '<p>Message envoyé par ' . $nom .'</p>';
        $contenu .= '<p>Email : '. $emailContact . '</p>';
        $contenu .= '<p>Numéro de téléphone : ' . $tel . '</p>';
        $contenu .= '<p>Sujet :</p>';
        $contenu .= $text;
        $contenu .= '</body></html>';



//préparation de l'envoi du mail
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("matthieu@gostiaux.net", "Site perso");
        $email->setSubject("Nouveau message reçu");
        $email->addTo("matthieu@gostiaux.net", "Moi");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", $contenu
        );
        $sendgrid = new \SendGrid($_ENV['sendgrid_api']);
        try {
            $response = $sendgrid->send($email);
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }

        header('Location: index.php#contact-form');

    }

	else {
	    die;
		header('Location: index.php#contact-form');
	}


