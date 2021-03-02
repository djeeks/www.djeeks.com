<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Sommaire</h2>

	<ol type="I">
		<li id="system">Système
			<ol type="A">
				<li id="linux">Linux
					<ul>
						<li><a href="debootstrap.php">Debootstrap</a></li>
						<li><a href="systemd.php">Systemd</a></li>
						<li><a href="lvm.php">Mise en place de LVM</a></li>
						<li><a href="raid.php">Configuration du RAID logiciel</a></li>
						<li><a href="logrotate.php">Gestion des logs avec Logrotate</a></li>
						<li><a href="selinux.php">SELinux</a></li>
					</ul>
				</li>
				<li id="webservers">Serveurs Web
					<ul>
						<li><a href="apache.php">Configuration d'un serveur Apache</a></li>
						<li><a href="apache_ssl.php">Apache et SSL</a></li>
					</ul>
				</li>
				<li id="dbservers">Serveurs de Bases de Données
					<ul>
						<li><a href="mysql.php">Modifier l'emplacement des bases de données MySQL</a></li>
						<li><a href="replication_mysql.php">Mise en place de la réplication Master-Slave pour MySQL</a></li>
						<li><a href="postgres.php">Utilisation de Postgresql</a></li>
						<li><a href="oracle.php">Utilisation d'Oracle</a></li>
					</ul>
				</li>
				<li id="mail">Serveurs Mail
					<ul>
						<li><a href="postfix.php">Administration de Postfix</a></li>
						<li><a href="dovecot.php">Administration de Dovecot</a></li>
					</ul>
				</li>
				<li id="dns">DNS
					<ul>
						<li><a href="bind.php">Configuration d'un serveur DNS avec BIND</a></li>
					</ul>
				</li>
			</ol>
		</li>
		<li id="network">Réseau
			<ul>
				<li><a href="ip.php">Utilisation de la commande ip</a></li>
				<li><a href="telnet.php">Tester l'ouverture des ports avec telnet</a></li>
			</ul>
		</li>
		<li id="infra">Infrastructure
			<ol type="A">
				<li id="virtu">Virtualisation
					<ul>
						<li><a href="xen.php">Installation et utilisation de Xen</a></li>
						<li><a href="kvm.php">Utilisation de KVM</a></li>
						<li><a href="proxmox_drbd.php">Installation de Proxmox et DRBD</a></li>
					</ul>
				</li>
				<li id="monitoring">Supervision
					<ul>
						<li><a href="nagios.php">Superviser vos serveurs avec Nagios</a></li>
					</ul>
				</li>
			</ol>
		</li>
		<li id="devops">Devops
			<ol type="A">
				<li id="cvs">Gestion de Versions
					<ul>
						<li><a href="git.php">Utilisation de Git</a></li>
						<li><a href="gitolite.php">Utilisation de Gitolite</a></li>
						<li><a href="svn.php">Utilisation de SVN</a></li>
					</ul>
				</li>
				<li id="automation">Automatisation et Gestion de configuration
					<ul>
						<li><a href="ansible.php">Mise en place d'Ansible</a></li>
					</ul>
				</li>
			</ol>
		</li>
	<!-- end devops-->
	</ol>

</section>

<?php
	include 'footer.php';
?>
