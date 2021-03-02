<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Supervision de serveurs avec Nagios</h2>

	<p>

		Nagios est un outils de supervision de serveurs. Dans l'infrastructure Nagios, il existe deux types de machines :<br />
		<ul>
			<li>un serveur, sur lequel est exécuté Nagios,</li>
			<li>et une ou plusieurs machines qui seront les clients.</li>
		</ul>
		La particularité de Nagios est qu'il se base sur des plugins (sondes) pour définir les services qui seront supervisés. Notre configuration fera donc intervenir NRPE (Nagios Remote Plugin Executor) pour exécuter les plugins sur les machines distantes (les clients). Les plugins pourront également être exécutés en local (sur le serveur Nagios) sans utiliser NRPE.<br />

	</p>

	<h3>Configuration préalable</h3>

	<p>

		Avant de pour voir installer Nagios sur le serveur, nous allons mettre à jour les dépôts et installer les pré-requis :<br />
		<span>aptitude update</span><br />
		<span>aptitude install build-essential libgd2-xpm-dev openssl libssl-dev xinetd apache2-utils apache2 unzip php5 libapache2-mod-php5</span>.<br />

		Ensuite, nous allons créer un utilisateur 'nagios'. Cet utilisateur système sera celui qui lancera les processus de Nagios.<br />
		<span>useradd nagios</span><br />
		<span>mkdir /home/nagios</span><br />
		<span>chown nagios:nagios /home/nagios</span><br />
		<span>groupadd nagcmd</span><br />
		<span>usermod -a -G nagcmd nagios</span><br />
	
	</p>

	<h3>Compilation de Nagios depuis les sources</h3>

	<p>

		La version de Nagios qui est disponible dans les dépôts de Debian est beaucoup trop ancienne. Pour cette raison, nous allons compiler la dernière version à partir des sources :<br />
		<span>cd /opt/</span><br />
		<span>wget https://assets.nagios.com/downloads/nagioscore/releases/nagios-4.2.1.tar.gz</span><br />
		<span>tar xzvf nagios-4.2.1.tar.gz</span><br />
		<span>cd nagios-4.2.1</span><br />
		<span>./configure --with-nagios-group=nagios --with-command-group=nagcmd --with-httpd-conf=/etc/apache2/conf-available</span><br />
		<span>make all</span><br />
		<span>make install</span><br />
		<span>make install-commandmode</span><br />
		<span>make install-init</span><br />
		<span>make install-config</span><br />

		Afin que les commandes de Nagios puissent être exécutées depuis le serveur web, on ajoute www-data dans le groupe de nagioscmd :<br />
		<span>usermod -G nagcmd www-data</span><br />

		Si vous utilisez une machine qui utilise systemd, il va falloir générer le script de démarrage de Nagios. Pour celà, on copie de script par défaut :<br />
		<span>cp /etc/init.d/skeleton /etc/init.d/nagios</span><br />

		On va ensuite modifier ce script (<span>vi /etc/init.d/nagios</span>) pour l'adapter à nos besoins, en ajoutant les lignes suivantes : <br />
		<pre>
			DESC="Nagios"
			NAME=nagios
			DAEMON=/usr/local/nagios/bin/$NAME
			DAEMON_ARGS="-d /usr/local/nagios/etc/nagios.cfg"
			PIDFILE=/usr/local/nagios/var/$NAME.lock 
		</pre>

		Enfin, il est nécessaire de rendre le script exécutable :<br />
		<span>chmod +x /etc/init.d/nagios</span><br />

	</p>

	<h3>Paramétrage du serveur Web</h3>

	<p>

		Toujours dans le dossier d'installation, nous allons générer le fichier de configuration du VirtualHost Apache pour Nagios : <br />
		<span>cd /opt/nagios-4.2.1</span><br />
		<span>/usr/bin/install -c -m 644 sample-config/httpd.conf /etc/apache2/sites-available/nagios.djeeks.com.conf</span><br />

		On doit modifier ce fichier afin de définir le ServerName et le DocumentRoot : <br />
		<pre>
			ServerName nagios.djeeks.com
			...
			DocumentRoot /usr/local/nagios/share
		</pre>

		Il est également possible d'ajouter des personnalisations à ce fichier, en restreignant, par exemple, l'accès à certaines IP ou en supprimant les directives pour les versions plus anciennes qu'Apache 2.3. Il est enfin conseiller d'ajouter les directives concernant les logs.<br />

		Une fois que le fichier du VirtualHost est bien configuré, il faut activer les modules cgi et rewrite pour Apache :<br />
		<span>a2enmod cgi</span><br />
		<span>a2enmod rewrite</span><br />

		On crée également un mot de passe afin de restreindre l'accès à l'interface web. Pour celà, on génère le fichier htpasswd : <br />
		<span>htpasswd -c /usr/local/nagios/etc/htpasswd.users nagiosadmin</span><br />

		<strong>Attention :</strong> Si nous avons choisi un autre utilisateur que nagiosadmin pour se connecter à l'interface web, il faut modifier le fichier /usr/local/nagios/etc/cgi.cfg.<br />

		Il ne faut pas oublier de modifier les droits d'accès à ce fichier : <br />
		<span>chmod 640 /usr/local/nagios/etc/htpasswd.users</span><br />
		<span>chown root:www-data /usr/local/nagios/etc/htpasswd.users</span><br />

		Une fois que tout est paramétré du côté du serveur web, on peut activer le VirualHost et recharger la configuration d'Apache : <br />
		<span>a2ensite /etc/apache2/sites-available/nagios.djeeks.com.conf</span><br />
		<span>systemctl reload apache2.service</span><br />

		Si on tente d'accéder au sous-domaine nagios.djeeks.com à ce moment de l'installation, il est normal que ça ne fonctionne pas. En effet, le daemon de Nagios n'est pas encore lancé et aucune configuration n'a été définie aussi bien pour le client que pour le serveur.<br />

	</p>

	<h3>Installation des plugins</h3>

	<p>

		Pour installer les plugins de Nagios, il existe deux méthodes :<br />
		<ul>
			<li>soit on compile les plugins à partir des sources,</li>
			<li>soit on les installe depuis la version présente dans les dépôts.</li>
		</ul>

	</p>

	<h4>Compilation des plugins Nagios</h4>

	<p>

		Pour l'installation des plugins Nagios depuis les sources, on se place dans le dossier /opt et on récupère l'archive : <br />
		<span>cd /opt</span><br />
		<span>wget http://nagios-plugins.org/download/nagios-plugins-2.1.3.tar.gz</span><br />

		Ensuite, on décompresse le fichier et on lance la compilation : <br />
		<span>tar xvzf nagios-plugins-2.1.3.tar.gz</span><br />
		<span>cd nagios-plugins-*</span><br />
		<span>./configure --with-nagios-user=nagios --with-nagios-group=nagios --with-openssl</span><br />
		<span>make && make install</span><br />

		Cette méthode installe les plugins dans le dossier /usr/local/nagios/libexec.<br />

	</p>

	<h4>Installation à partir des dépôts Debian</h4>

	<p>

		L'installation de ces plugins via les dépôts est beaucoup plus simple, il suffit d'exécuter cette commande : <br />
		<span>aptitude install nagios-nrpe-plugin nagios-plugins nagios-plugins-basic nagios-plugins-contrib</span><br /> 

		<strong>Attention :</strong> Cette méthode installe les plugins dans un dossier différent de la méthode précende. Ici, ils sont installés dans le dossier /usr/lib/nagios/plugins.<br />

		Il est donc nécessaire de modifier le fichier <span>/usr/local/nagios/etc/resource.cfg</span> : <br />
		<pre>
			$USER1$=/usr/lib/nagios/plugins
		</pre>

	</p>

	<h3>Configuration du client avec NRPE</h3>

	<p>

		Le principe de fonctionnement de NRPE est que chaque client Nagios est un serveur NRPE. Le serveur Nagios (client NRPE) se connecte donc à chaque serveur NRPE (client Nagios) pour récupérer le résultat des sondes qui ont été exécutées en local (sur chaque client Nagios). <br />

	</p>

	<h4>Configuration du client Nagios / serveur NRPE</h4>

	<p>

		Sur chaque client Nagios, on va installer les plugins que la machine pourra exécuter en local et le paquet pour le serveur NRPE : <br />
		<span>aptitude install nagios-nrpe-server nagios-plugins nagios-plugins-basic nagios-plugins-contrib</span><br />

		Ensuite, on modifie le fichier <span>/etc/nagios/nrpe.cfg</span> : <br />
		<pre>
			server_address=IP_PUBLIQUE_DU_CLIENT_NAGIOS
			allowed_hosts=127.0.0.1,IP_PUBLIQUE_DU_SERVEUR_NAGIOS
		</pre>

		Ce fichier nous fourni également le numéro du port utilisé par NRPE (à la ligne server_port). Ce numéro de port va être utile afin d'ajouter une règle dans le firewall pour autoriser les communications avec le serveur Nagios : <br />
		<span>iptables -t filter -A INPUT -s IP_SERVEUR_NAGIOS -p tcp --dport NUMERO_PORT_NRPE -j ACCEPT</span><br />

		Une fois les paramétrages terminés, on peut démarrer le service NRPE sur les clients Nagios : <br />
		<span>systemctl start nagios-nrpe-server</span><br />

	</p>

	<h4>Configuration du serveur Nagios / client NRPE</h4>

	<p>

		Avant de pouvoir tester les connection via NRPE, il ne faut pas oublier de démarrer Nagios sur le serveur :<br />
		<span>/etc/init.d/nagios start</span><br />
		On peut également ajouter une règle dans le firewall pour chaque client (ou une règle globale si on ne précise pas d'IP de destination) : <br />
		<span>iptables -t filter -A OUTPUT -d IP_CLIENT_NAGIOS -p tcp --dport NUMERO_PORT_NRPE -j ACCEPT</span><br />

		Enfin, on peut vérifier que le serveur Nagios  arrive bien à contacter ses clients (via NRPE) grâce à la commande : <br />
		<span>cd /usr/lib/nagios/plugins && ./check_nrpe -H IP_client_Nagios</span><br />
		Si tout fonctionne bien, cette commande doit nous renvoyer le numérode verison de NRPE.<br />

	</p>

</section>

<?php
	include('footer.php');
?>
