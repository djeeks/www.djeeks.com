<?php
	$file = __FILE__;
	include('header.php');
?>

<section>
	
	<h2>La commande Telnet</h2>

	<p>

		Telnet n'est pas utilisé en production car il n'est pas sécurisé. En revanche, rien n'empêche de l'utiliser afin de tester qu'un programme répond bien sur un port donné, et qu'il fournit bien le retour attendu. Pour cela, il faut connaître le fonctionnement du protocole que nous allons requêter. En effet, il va falloir fournir les paramètres attendus par le protocole donné.<br />

	</p>

	<h3>Utilisation de Telnet</h3>

	<p>

		L'utilisation de telnet est très simple. Il suffit de passer en argument au programme telnet l'ip du serveur distant et le port sur lequel le daemon écoute.<br />
		<span>telnet 127.0.0.1 22</span><br />
		Cette commande vérifie qu'on a bien un service qui écoute sur le port 22 (habituellement SSH) en local.<br />
		Une fois que l'on a terminé nos test, il suffit de tapper la commande <span>QUIT</span> afin de fermer l'outil telnet.<br />

	</p>

	<h3>Le protocole HTTP</h3>

	<p>

		Pour vérifier que le serveur distant répond bien sur le port HTTP (habituellement 80), on utilise la commande suivante :<br />
		<span>telnet 127.0.0.1 80</span><br />
		Ensuite, il va falloir préciser la requête que l'on souhaite envoyer au serveur HTTP :<br />
		<span>GET /index.html HTTP/1.1</span><br />
		<span>Host:wiki.djeeks.com</span><br />
		Il est, bien-sûr, possible de modifier la requête pour l'adapter à votre configuration. La page reqêtée peut être en PHP, par exemple (et pas forcément l'index). Il faut également adapter l'host à votre serveur.<br />

		<strong>Attention :</strong> Pour le protocole HTTPS (généralement 443), la requête telnet présentée ci-dessus ne fonctionnera pas. A la place de telnet, on va utiliser ici la commmande openssl :<br />
		<span>openssl s_client -connect IP_SERVEUR:443</span> ou <span>openssl s_client -host IP_SERVEUR -port 443</span>.<br />
		On peut également demander la vérification du certificat racine via l'option : <span>-CAfile /etc/ssl/certs/ca-certificates.crt</span>.<br />
		Ensuite, la requête est formaté comme pour telnet puisque nous avons toujours à faire au protocole HTTP même s'il est associé à SSL/TLS.<br />

	</p>

	<h3>Le protocole SMTP</h3>

	<p>

		Lorsqu'on souhaite requêter un serveur mail SMTP (traditionnellement sur le port 25), on utilise la commande :<br />
		<span>telnet serveur.mail.com 25</span><br />
		Ensuite, il va bien falloir fournir les bons paramètres au protocole SMTP :<br />
		<span>EHLO test.com</span> : Le client s'identifie via cette commande (envoie de l'IP ou du nom de domaine du client).<br />
		<span>MAIL FROM:<adresse@expediteur.fr></span><br />
		<span>RCPT TO:<adresse@destinataire.fr></span><br />
		<span>DATA</span><br />
		Dans la partie DATA, il faut saisir le corps du mail. On peut le précéder d'une ligne <span>Subject:Objet du mail</span> et/ou <span>Reply-to:adresse@retour.fr</span>. Enfin, un point (tout seul sur une ligne) indiquera la fin du corps de ce mail de test.<br />

		Pour le protocole SMTP avec SSL/TLS, le port par défaut est 465. En revanche, l'option starttls permet de faire du SSL/TLS sur le même port que le protocole non chiffré (ici le port 587). Comme pour  le protocole HTTPS, nous allons utiliser openssl à la place de telenet :<br />
		<span>openssl s_client -connect IP_SERVEUR:587 -starttls smtp</span><br />
		La suite de la requête est identique à celle en telnet.<br />

		<strong>Attention :</strong> Si le service (avec ou sans TLS/SSL) nécessite une authentification, Il faudra fournir les accès (identifiant et mot de passe) encodés en base64.<br />

	</p>

</section>

<?php
	include('footer.php');
?>
