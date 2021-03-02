<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Modifier l'emplacement des Bases de Données MySQL</h2>

	<p>

		On commence par installer le serveur MySQL : <br/>
		<ul>
			<li><span>aptitude install mysql-server</span></li>
		</ul>
		On crée les différents dossiers pour la nouvelle arborescence, et on définit l'utilisateur mysql comme propriétaire des dossiers  : <br/>
		<ul>
			<li><span>mkdir /home/mysql</span></li>
			<li><span>mkdir /home/mysql/data</span></li>
			<li><span>mkdir/home/mysql/tmp</span></li>
			<li><span>chown -R mysql:mysql /home/mysql</span></li>
		</ul>
		Ensuite, il faut modifier le fichier de configuration de MySQL (/etc/mysql/my.cnf) afin de faire pointer datadir et tmpdir vers les dossiers que l'on vient de créer.<br/>
		On stoppe MySQL : <br/>
		<ul>
			<li><span>systemctl stop mysql.service</span></li>
		</ul>
		On lance l'installation des fichiers de base de MySQL (les tables par défaut) dans la nouvelle arborescence : <br/>
		<ul>
			<li><span>mysql_install_db --user=mysql</span></li>
		</ul>
		On redémarre le service MySQL : <br/>
		<ul>
			<li><span>systemctl start mysql.service</span></li>
		</ul>
		On crée l'utilisateur root : <br/>
		<ul>
			<li><span>mysqladmin -u root password 'XXXXXXX'</span></li>
		</ul>
		Enfin, il faut se connecter à MySQL avec l'utilisateur root, que l'on vient de créer précédemment, afin de donner les droits à l'utilisateur debian-sys-maint (le password de cet utilisateur se trouve dans le fichier /etc/mysql/debian.cnf) : <br/>
		<ul>
			<li><span>GRANT ALL PRIVILEGES ON *.* TO 'debian-sys-maint'@'localhost' IDENTIFIED BY 'XXXXXX';</span></li>
		</ul>

	</p>

</section>

<?php
	include('footer.php');
?>
