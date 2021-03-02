<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>La commande IP</h2>

	<h3>Informations sur les interfaces réseau</h3>

	<p>

		Initialement, pour connaître des interfaces réseau disponibles sur une machine, on pouvait utiliser la commande <span>ifconfig</span>. Cependant, cette commande étant passée en statut ''deprecated'', nous allons voir comment la commande ip peut la remplacer :

		Pour obtenir des informations sur les interfaces réseau, il existe deux commandes :<br />
		<ul>
			<li>la commande <span>ip addr show</span> qui peut être abrégée en <span>ip a s</span>. Cette commande est l'équivalent d'un <span>ifconfig -a</span>, puisqu'elle affiche même les interfaces qui ne sont pas actives. Cette commande affiche principalement les informations relatives à la couche 3 du réseau (réseau IP). Pour afficher une seule interface, on peut utiliser la commande <span>ip addr show dev eth0</span> (abrégée en <span>ip a s dev eth0</span>)</li>
			<li>la commande <span>ip link show</span> qui peut être abrégée en <span>ip l show</span>. Elle affiche des informations relatives à la couche 2 du réseau (data link). On peut également restreindre l'affichage à une seule interface via <span>ip link show dev eth0</span> (abrégée en <span>ip l show dev eth0</span>.</li>
		</ul>

		Pour afficher des informations supplémentaires, notamment relatives aux trafic entrants ou sortants, il est possible d'utiliser des arguments : <span>ip -s l show dev eth0</span>.<br />

	</p>

	<h3>Gérer les interfaces réseau</h3>

	<p>

		Lorsque les interfaces réseau sont bien configurées via le fichier /etc/network/interfaces, on peut les activer avec la commande <span>ip link set eth0 up</span>.<br />
		Il est également possible de les désactiver via <span>ip link set eth0 down</span>.<br />

		Si une interface n'est pas configurée, il est possible de la paramétrer à la volée via la commande <span>ip addr add 192.168.0.10/255.255.255.0 dev eth0</span>. On peut également affecter une adresse via la notation CIDR <span>ip addr add 192.168.0.10/24 dev eth0</span>.<br />
		Pour définir l'adresse de broadcast, on utilise <span>ip link set dev eth0 broadcast 192.168.0.255</span>.<br />
		Attention : Pour cette dernière commande, comme pour <span>ifconfig eth0 192.168.0.10 netmask 255.255.255.0</span>, les informations seront perdues lors du redémarrage de la machine, si elles ne sont pas enregistrées dans /etc/network/interfaces.<br/>

		Enfin, la commande ip permet de modifier le nom d'une interface réseau : <span>ip link set dev eth0 name mon_interface</span>.<br />

	</p>

	<h3>Gestion de la table de routage</h3>
	
	<p>

		La commande ip peut également être utilisée pour modifier la table de routage.<br />
		Pour accéder à la table de routage, il suffit de lancer la commande <span>ip route show</span> qui est l'équivalent de la commande <span>route -n</span>.<br />

		Pour ajouter une nouvelle route dans la table on exécute la commande suivante :<br />
		<span>ip route add 192.168.1.0/24 via 192.168.1.1 dev eth2</span><br />
		Dans cette commande, l'option via désigne la gateway et dev l'interface qui doit être utilisée. Cette commande est équivalente de la commande :<br />
		<span>route add -net 192.168.1.0/24 gw 192.168.1.1 dev eth2</span><br />

		Si on souhaite supprimer une route dans la table, on peut lancer la commande :<br />
		<span>ip route del 192.168.1.0/24 via 192.168.1.1 dev eth2</span><br />

		Le gateway par défaut peut être définie par la commande :<br />
		<span>ip route add default via 192.168.0.1</span><br />

	</p>

</section>

<?php
	include('footer.php');
?>
