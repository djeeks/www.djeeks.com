<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Configuration du SSL sur Apache</h2>

	<h3>Création de la clé privée et du Certificat Signing Request</h3>

	<p>
		Afin de générer les certificats et les clés, il faut que openssl soit installé sur le serveur :<br/>
		<span>aptitude install openssl</span><br/>
		Ensuite, on se place dans le bon dossier : <span>cd /etc/ssl</span> et on génère la clé privée :<br/>
		<span>openssl genrsa -out data.djeeks.com.key 2048 -sha256</span><br/>

		On génère une Certificate Signing Request (CSR) qui est signée avec la clé privée que nous avons précédemment générées :<br/>
		<span>openssl req -new -key data.djeeks.com.key -out data.djeeks.com.csr</span><br/>
	</p>

	<h3>Création du certificat</h3>

	<p>
		Il existe deux méthode pour obtenir le fameux certificat :<br />
		<ul>
			<li>soit on demande notre certificat à une Autorité de Certification (CA)</li>
			<li>soit on génère un certificat auto-signé sur notre  serveur</li>
		</ul>
	</p>

	<h4>Demande d'un certificat auprès d'une Autorité de Certification</h4>

	<p>
		Le rôle de l'Autorité de Certification est de vérifier la validité des données saisies par la personne (ou société) qui émet une demande de Certificat. Les navigateurs web font confiance à certaines Autorités de Certification. Dans le cas de l'utilisation d'un certificat émis par une de ces Autorités, le navigateur donne l'accès au serveur sans aucune action du visiteur puisque les vérifications préalables ont été assurées par la CA (demande de CNI ou Kbis par exemple).<br />

		Pour obtenir un certificat des Autorités de Certification, il faut (après paiement) afficher la Certificate Signing Request : <span>cat /etc/ssl/data.djeeks.com.csr</span> et la coller dans l'interface web du prestataire. En retour, on obtient le certificat au format .crt qu'on pourra placer dans le dossier <span>/etc/ssl/certs</span>.<br />
	</p>

	<h4>Création d'un certificat auto-signé</h4>

	<p>
		Il est également possible de créer son propre certificat sur le serveur via la commande :<br/>
		<span>openssl x509 -req -days 365 -in data.djeeks.com.csr -signkey data.djeeks.com.key -out data.djeeks.com.crt</span><br/>

		Dans ce cas, lors d'un accès au serveur via le protocole HTTPS, le navigateur nous signalera qu'il ne connait pas la source ayant émis le certificat (celui-ci n'est pas signé par une Autorité de Certification qu'il connait). Par défaut, il ne fait donc pas confiance aux informations contenues dans ce certificat et afin de préserver tout risque bloque la page. C'est donc à l'utilisateur d'ajouter une exception dans la politique de sécurité du navigateur pour forcer l'accès à la page (si l'utilisateur à confiance en ce certificat).
	</p>

	<h3>Manipulations préalables à la configuration d'Apache</h3>

	<p>
		Dans les deux cas, il nous reste deux étapes avans de configurer le serveur Apache :<br />
		<ul>
			<li>On place le certificat et la clé dans les bons dossiers :</li>
			<span>mv /etc/ssl/data.djeeks.com.key /etc/ssl/private/data.djeeks.com.key</span><br/>
			<span>mv /etc/ssl/data.djeeks.com.crt /etc/ssl/certs/data.djeeks.com.crt</span>

			<li>La clé que nous avons sur le serveur est une clé privée. Cela signifie qu'afin de garantir la sécurité des connexion, personne ne doit pouvoir l'afficher. Il faut donc absolument restreindre son accès : </li>
			<span>chmod 400 /etc/ssl/certs/data.djeeks.com.crt</span>
		</ul>
	</p>

	<h3>Configuration d'Apache</h3>

	<p>
		Pour qu'Apache soit capable d'utiliser des connexions chiffrées par les certificats SSL, il faut activer le mode SSL : <br/>
		<span>a2enmod ssl</span><br/>

		Ensuite, on modifie le virtualhost existant (ou on en crée un nouveau) : 
		<span>vi /etc/apache2/sites-available/data.djeeks.com.conf</span>. Ce virtualhost contiendra les informations suivantes en plus de la configuration classique :<br/>
		<pre>
			SSLEngine On
			SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire

			SSLCertificateFile /etc/ssl/certs/data.djeeks.com.crt
			SSLCertificateChainFile /etc/ssl/certs/data.djeeks.com_bundle.crt
			SSLCertificateKeyFile /etc/ssl/private/data.djeeks.com.key
		</pre>

		Si on souhaite forcer l'utilisation du protocole HTTPS au lieu de HTTP, il faut ajouter ces lignes au début du VirtualHost :
		<pre>
			&lt;VirtualHost *:80&gt;
					ServerName data.djeeks.com

					RewriteEngine On
					RewriteCond %{SERVER_PORT} !^443$
					RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

					#RewriteCond %{HTTPS} off
					#RewriteRule ^(.*)$ https://%{SERVER_NAME}/$1 [R=301,L]
			&lt;/virtualHost&gt;
		</pre>

		On active ce virtalhost (si ce n'était pas déjà fait) : <span>a2ensite data.djeeks.com.conf</span><br/>
		On vérifie que la configuration Apache est bonne : <span>apachectl configtest</span> et s'il n'y a pas de souci on peut redémarrer Apache : <span>systemctl restart apache2.service</span>.<br/>
	</p>

	<h3>Sécurisation avancée</h3>

	<p>
		Afin d'ajouter un niveau de sécurisation supplémentaire à notre VirtualHost, nous allons mettre en place deux éléments importants :<br />
		<ul>
			<li>HTTP avec Strict Transport Security (HSTS)</li>
			<li>Forward Secrecy</li>
		</ul>
	</p>

	<h4>HTTP avec Strict Transport Security</h4>

	<p>
		Afin d'ajouter le support du HSTS, voila la ligne à ajouter dans les vhosts Apache : <br/>
		<span>Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"</span><br/>
	</p>

	<h4>Forward Secrecy</h4>

	<p>
		Afin d'ajouter le Forward Secrecy, il faut ajouter les lignes suivantes dans les vhosts Apache : <br/>
		<pre>
			SSLProtocol TLSv1.2
			SSLHonorCipherOrder on
			SSLCipherSuite "ECDH+AES256:ECDH+SHA384"
		</pre>

		Pour vérifier que tout est bien configuré, on peut utilise <a href="https://www.ssllabs.com/ssltest/">l'outil de SSL Labs</a>.<br />
	</p>

</section>

<?php
	include('footer.php');
?>
