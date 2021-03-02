<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Proxmox et DRBD</h2>

	<p>

		Porxmox est un hyperviseur bare-metal qui peut s'installer sur des serveurs nus. Cependant, il est également possible de l'installer à partir de Debian. Ici, nous allons réaliser une installation sans autre OS. De plus, nous allons mettre en place une réplication entre 2 serveurs via DRBD afin que nos machines virtuelles puissent être exécutées indifféremment sur chacun des 2 serveurs (et basculer de l'un à l'autre à chaud).<br />

	</p>

	<h3>Installation de Proxmox</h3>

	<p>

		L'installation de l'hyperviseur est standard. Elle se fait à partir de l'ISO récupérée sur le site officiel de Proxmox. Il y a, cependant, une particularité à prendre en compte lors du partitionnement. La taille de la partition principale qui va être définie servira à créer un Volume Group LVM qui contiendra 3 Logical Volume (/root, la swap et une partition de data). Comme on souhaite utiliser DRBD, ensuite, il va falloir allouer un espace de stockage pour cette réplication.<br />
		Depuis la version 4 de Proxmox, le support de DRBD9 est inclus, avec l'outil drbdmanage. Celui-ci nécessite un Volume Group spécifique nommé drbdpool pour la réplication entre les différents nœuds. Il est donc important de laisser de l'espace disponible pour ensuite pouvoir créer ce nouveau Volume Group.<br />

	<h3>Configuration des serveurs Proxmox</h3>

	<p>

		Les deux serveurs doivent aussi être réliés via un cable ethernet. Pour cela on va définir une interface eth2 dans le fichier <span>/etc/network/interfaces</span> afin que les deux serveurs puissent communiquer entre eux (un serveur aura l'adresse 10.0.0.1 et l'autre l'adresse 10.0.0.2) :<br />
		<pre>
			auto eth2
			iface eth2 inet static
			address 10.0.0.1
			netmask 255.255.255.0
		</pre>

		En activant l'interface via la commande <span>ifup eth2</span>, il doit être possible de lancer un ping sur l'ip de eth2 du second serveur.<br />

		Ensuite, il va falloir créer le cluster proxmox. Pour cela, il faut exécuter la commande <span>pvecm create &lt;cluser_name&gt;</span> sur un des nœuds. Sur le second serveur, on va lancer la commande <span>pvecm add &lt;IP du 1er nœud&gt;</span>.<br />
		Pour vérifier que tout fonctionne bien, on peut eécuter la commande <span>pvecm status</span> ou se rendre sur l'interface web : https://&lt;IP du 1er nœud&gt;:3306<br />

		Enfin, il faut enregistrer sur chaque serveur le fingerprint des autres machines du cluster. Pour cela, on tente une connexion en SSH. Si tout a bien été configuré, il devrait être possible de se connecter sans qu'il ne demande de saisir le mot de passe.<br />

	<p>

	<h3>Installation de DRBD</h3>

	<p>

		Pour pouvoir installer le paquet drbdmaange, on va modifier le fichier <span>etc/apt/sources.list.d/pve-enterprise.list</span>, pour qu'il ressemble à ceci :<br />
		<pre>
			#deb https://enterprise.proxmox.com/debian jessie pve-enterprise

			deb http://ftp.debian.org/debian jessie main contrib

			# PVE pve-no-subscription repository provided by proxmox.com, NOT recommended for production use
			deb http://download.proxmox.com/debian jessie pve-no-subscription
		</pre>

		Ensuite, on peut lancer la commande <span>apt update && apt install drbdmanage</span>.<br />

	</p>

</section>

<?php
	include('footer.php');
?>
