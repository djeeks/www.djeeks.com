<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Le serveur DNS Bind</h2>

	<p>
		En root, lancer la commande suivante :<br />
		<span>aptitude install bind9</span><br /><br />

		Les fichiers de configuration se trouvent dans /etc/bind. Il y a un fichier de configuration principal : named.conf qui fait ensuite appel aux fichier named.conf.options et named.conf.local.<br />

		Dans le fichier named.conf.options nous allons définir les options du serveur (qui vont donc s'appliquer à tous les domaines qu'il gère). Pour le modifier, on utiliser la commande :<br />
		<span>vim /etc/bind/named.conf.options</span><br /><br />

		<pre>
			options {
				directory «/var/cache/bind»;

				dnssec-validation auto;

				minimal-responses yes;
				allow-transfer { ip_du_serveur_dns_sercondaire; };
				allow-update { ip_du_serveur_dns_sercondaire; };
				recursion yes;
				allow-recursion { 127.0.0.1;ip_publique_du_serveur_primaire; };
				notify yes;
				allow-query-cache { any; };
				allow-query { any; };

				zone-statistics yes;
				statistics-file "/var/log/bind/stat.log";

				auth-nxdomain no;    # conform to RFC1035
				listen-on-v6 { ::1; };
				listen-on { ip_publique_du_serveur_primaire; };
			}
		</pre>

		Dans le fichier named.conf.local, on définit les noms de domaine (zone) gérés par le serveur :<br />
		<span>vim /etc/bind/named.conf.local</span><br /><br />

		<pre>
			zone "domaine.tld" { 
					type master; 
					file "/etc/bind/zones/domaine.tld"; 
			}; 
		</pre>

		On laisse la charge du reverse DNS aux prestataires qui hébergent les différents serveurs. Pour chaque zone, il faut créer un fichier de définition. Ce fichier doit se trouver à l'adresse défini à la ligne « file » du fichier named.conf.local.<br />
		<span>vim /etc/bind/zones/domaine.tld</span><br /><br />

		<pre>
			$TTL 7200 

			@       IN      SOA    domaine.tld. admin.domaine.tld. ( 
													2015052001 	; Serial 
													7200       		; Refresh 
													1800       		; Retry 
													1209600    	; Expire - 1 week 
													86400 )    	; Minimum 

			@		IN		NS		FQDN_du_serveur_primaire (ex : ns1.domaine.tld.)
			@               IN              NS	        FQDN_du_serveur_secondaire (ex : ns2.domaine.tld.)

			ns1		IN		A		adresse_ip_serveur_primaire
			ns2		IN		A		adresse_ip_serveur_secondaire

			@ 	        IN 	        A		ip_du_serveur_ou_pointe le domaine_(sans sous-domaine) 

			; Liste des sous-domaines
			www            	IN		A		adresse_ip_du_serveur_web
			ftp		IN		A		adresse_ip_du_serveur_ftp

			@		IN		MX	10	mail.djeeks.com. 
			mail		IN		A		ip_du_serveur_mail
		</pre>

		Sur la ligne SOA, admin.domaine.tld. représente l'adresse mail de l'administrateur (le premier . Remplace l'@).<br />

		Attention : chaque nom saisi en toute lettre se termine par un point sinon, bind rajoute domaine.tld à la fin !<br />

		Si on souhaite séparer les logs de Bind, il est possible d'ajouter ces lignes dans le fichier de définition des options de Bind :<br />
		<span>vim /etc/bind/named.conf.options</span><br /><br />

		<pre>
			logging {
				 channel misc {
					file "/var/log/bind/misc.log";
					print-time yes;
				 };

				  channel update {
					file "/var/log/bind/update.log";
					print-time yes;
				 };

				 channel queries {
					file "/var/log/bind/queries.log";
					print-time yes;
				 };

				 category default {
					misc;
				 };

				 category config {
					misc;
				 };

				 category update {
					update;
				 };

				 category queries {
					queries;
				 };
			};
		</pre>

		Une fois les fichiers de configuration paramétrés, on lance une vérification via les commandes :<br />
		<span>named-checkconf -z /etc/bind/named.conf</span><br />
		<span>named-checkzone domaine.tld /etc/bind/db.domaine.tld</span><br /><br />

		Ces commandes utilisent le paquet bind9utils. Si tout est ok, on peut relancer le serveur :<br />
		<span>systemctl restart bind9</span><br /><br />
 
		Enfin, on peut faire une dernière vérification à l'aide de l'outil zonemaster de l'Afnic (anciennement zonecheck).<br />

	</p>

	<h3>Sécurisation DNS :</h3>

	<p>

		Il est possible de fonctionner avec une infrastructure comprenant plusieurs serveurs maitre (pas de serveur esclave) : cette configuration requiert une mise à jour « à la main » pour tous les serveurs, mais élimine totalement le risque encouru lors des échanges de fichiers de zone entre maitre et esclave. (A mettre en place s'il y a peu de changement dans les fichiers de zone).

	</p>

</section>

<?php
	include('footer.php');
?>
