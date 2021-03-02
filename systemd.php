<?php
	$file = __FILE__;
	include('header.php');
?>

<section>
		
	<h2>Systemd</h2>

	<p>
		Systemd est l'outil de gestion du démarrage et des services qui remplace SysVinit (ou Upstart) dans la plupart des distributions Linux.<br />
	</p>

	<h3>Les commandes de base</h3>

	<ul>
		<li><span>systemctl</span> : voir l'état des différents services (systemd et sysVinit)</li>
		<li><span>systemctl status</span> : Liste les services démarrés</li>
		<li><span>systemctl start apache2.service</span> : pour démarrer un service</li>
		<li><span>systemctl stop apache2.service</span> : pour stopper un service</li>
		<li><span>systemctl restart apache2.service</span> : pour redémarrer un service</li>
		<li><span>systemctl disable apache2.service</span> : pour désactiver le service au démarrage</li>
		<li><span>systemctl enable apache2.service</span> : pour activer le service au démarrage</li>
	</ul>

	<h3>Création des scripts de démarrage des services</h3>
		
	<p>

		Les scripts de démarrage d'un service ont cette forme avec systemd :<br />
		<pre>
			[Unit]
			Description=Daemon to detect crashing apps
			After=syslog.target

			[Service]
			ExecStart=/usr/sbin/abrtd
			Type=forking

			[Install]
			WantedBy=multi-user.target
		</pre>

		<ul>
			<li>Le champ Unit sert à définir le service, mais pas uniquement (systemd peut également gérer des points de montage, des devices et d'autres composants). Unit est le terme générique pour ces composants.</li>
			<li>La requête After ne force pas le démarrage de syslog. En effet, abrtd (un démon de Fedora) peut fonctionner sans le syslog. En revanche, cette ligne précise que si les deux sont exécutés, alors syslog s'exécute en premier.</li>
			<li>Pour la partie Service, on peut également ajouter une requête ExecStop.</li>
			<li>Si on crée un script pour lancer des services au démarrage, il faut le place dans le dossier /etc/systemd/system puis demander à systemd de recharger la liste des démons via : <span>systemctl daemon-reload</span>. Ensuite seulement, il sera possible de l'activer au démarrage.</li>
		</ul>

	</p>

	<h3>Masquer des services</h3>

	<p>

		Il est également possible de masquer un service. Le service ainsi masqué ne sera pas lancé au démarrage (équivalent de disable), mais il ne pourra plus être démarré, même manuellement. Pour cela, il suffit d'utiliser la commande suivante: <span>systemctl mask apache2.service</span><br/>
		Cette commande crée un lien symbolique (au nom du service) vers /dev/null et le place dans /etc/systemd/system. C'est l'équivalent de : <span>ln -s /dev/null /etc/systemd/system/apache2.service</span>. Les fichiers présents dans le dossier /etc/systemd/system sont exécutés en priorité et empêchent donc les fichiers du dossier /lib/systemd/system de se lancer. Par défaut, les services ne sont pas représentés dans le dossier /etc/systemd/system et c'est donc bin le fichier du dossier /lib/systemd/system qui s'exécute.<br />

		Pour rendre visible un script qui a été précédemment masqué, il faut utiliser la commande <span>systemctl unmask apache2.service</span>, qui supprime le lien symbolique.<br />

	</p>

	<h3>Gestion des processus</h3>

	<p>

		Avec systemd, tous les processus fils d'un service font partis d'un Control Group commun (cgroup). Celà permet de pouvoir killer tous les processus d'un même cgroup et éviter d'avoir des processus zombies.<br />

		Pour voir les cgroup : <span>ps axwf -eo pid,user,cgroup,args</span> (ou <span>systemd-cgls</span>)<br />

		Pour killer des services, on utilise la commande : <span>systemctl kill apache2.service</span>.<br />

		Il est également possible de passer en argument le signal à envoyer au service : <span>system kill -s SIGKILL apache2.service</span> (pour avoir l'équivalent d'un kill -9). En utilisant l'option -s on peut choisir ou non de préciser le préfixe SIG (-s SIGKILL est identique -s KILL, -s SIGHUP est identique à -s HUP…)<br />

		Avec la commande systemctl kill il est enfin possible de définir quels processus membres du cgroup devront être tués : <span>systemctl kill -s HUP --kill-who=main apache2.service</span> (pour tuer le processus apache père) ou <span>systemctl kill -s  KILL --kill-who=all apache2.service</span> (à vérifier pour l'option all ;-))<br />

	</p>

	<h3>Gestion des Targets</h3>
	
	<p>

		Les targets sont les équivalents sous systemd des runlevels.<br/>
		Les 5 principales targets sont :<br />
		<ul>
			<li>poweroff</li>
			<li>rescue</li>
			<li>multi-user</li>
			<li>graphical</li>
			<li>reboot</li>
		</ul>
		Pour changer de target, on utilise la commande <span>systemctl isolate multi-user.target</span>.<br/>
		Pour connaitre la target par défaut, il faut saisir la commande <span>systemctl get-default</span> et la commande <span>systemctl set-default graphical.target</span> sert à modifier la target utilisée apr défaut.</li>

	</p>

</section>

<?php
	include ('footer.php');
?>
