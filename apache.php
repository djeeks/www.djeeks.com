<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Le serveur web Apache</h2>

	<p>
		En root, lancer la commande suivante :<br />

		<span>apt install apache2</span><br />

		Afin de vérifier que le serveur Apache est bien opérationnel, il suffit de taper son adresse dans un navigateur. Il devrait afficher « It Works » qui est le contenu de la page /var/www/index.html.<br />

		Les fichiers de configuration du serveur Apache sont dans le dossier /etc/apache2. Les principaux fichiers sont apache2.conf, ports.conf, envvars et les fichiers de configuration des vhosts.<br />
	</p>	

	<h3>Configuration d'un vhost</h3>

	<p>

		Dans le dossier /etc/apache2/sites-enabled, on trouve le fichier de configuration du vhost par défaut qu'il faut supprimer via la commande :<br />
		<span>a2dissite 000-default</span><br />

		Ce fichier définissait la racine d'apache sur /var/www. Nous allons donc créer un dossier www dans /home et donner les droits nécessaires pour qu'Apache puisse afficher le contenu du dossier.<br />

		<span>mkdir /home/www</span><br />

		<span>chown webadmin:www-data /home/www</span><br />

		<strong>Attention :</strong> Apache est exécuté avec l'utilisateur www-data qui doit pouvoir lire les fichiers des différents projets web pour pouvoir les afficher. Cependant, il ne doit pas avoir le droit d'écriture dessus. En effet, si un utilisateur réussit à pénétrer dans le système, il sera identifié en tant que www-data. Sans les droits d'écriture, il ne pourra pas modifier ou supprimer nos fichiers.<br />

		Ensuite, chacun des sites hébergés sur notre serveur aura son sous-dossier dans /home/www.<br />

		Nous créons donc un nouveau fichier dans /etc/apache2/sites-available pour chacun des sites hébergés (Vhost) sur le serveur Apache :<br />

		<span>cp /etc/apache2/sites-available/default /etc/apache2/sites-available/domaine.tld</span><br />

		Puis on édite le fichier créé, à l'aide de <span>vim /etc/apache2/sites-availbale/domaine.tld</span>. La configuration doit ressembler à ça :<br />

		<pre>
			&lt;VirtualHost *:80&gt;
				ServerName www.domaine.tld 
				ServerAlias domaine.tld *.domaine.tld 
				ServerAdmin webmaster@domaine.tld

				ErrorLog /home/www/domaine.tld/logs/error_log 
				CustomLog /home/www/domaine.tld/logs/access_log combined

				DocumentRoot /home/www/domaine.tld

				&lt;Directory /home/www/domaine.tld&gt;
						Options Indexes FollowSymLinks MultiViews 
						AllowOverride None 
						Order allow,deny 
						allow from all 
				&lt;/Directory&gt;

				LogLevel warn 
			&lt;/VirtualHost&gt;
		</pre>

		Pour activer cette configuration, il suffit de lancer la commande <span>a2ensite domaine.tld</span> et de recharger la configuration apache avec <span>service apache2 reload</span>.<br />
	</p>

	<h3>Mise en place de PHP avec Apache</h3>

	<p>

		Pour que notre serveur Apache suppporte PHP, nous devons l'installer via la commande :<br />
		<span>aptitude install php5 libapache2-mod-php5</span><br />

		On peut vérifier que tout fonctionne bien en remplaçant le fichier index.html par défaut par un fichier index.php qui contient le code suivant :</br >
		<pre>
		&lt;?php
			phpinfo();
		?&gt;
		</pre>
	</p>

</section>

<?php
	include('footer.php');
?>
