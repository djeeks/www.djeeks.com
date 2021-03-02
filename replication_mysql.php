<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Réplication Master - Slave MySQL</h2>

	<p>

		Afin de s'assurer que nos bases de données soient toujours accessibles, y compris en cas de panne de notre serveur, il est possible de mettre en place une réplication. Celle-ci permettra à un second serveur (le slave) de rejouer toutes les actions qui ont lieu sur le premier serveur (le master). En cas d'incident, notre second serveur contient les mêmes données que le serveur master. Il suffit donc de configurer les différents services pour qu'ils envoient leurs requêtes vers l'ancien serveur slave (qu'il faudra temporairement paramétrer en tant que master).<br />

		<span>aptitude install mysql-server</span><br/>
		<span>mysq_install_db</span><br/>
		<span>mysql_secure_installation</span><br/>

	</p>

	<h3>Configuration du Master</h3>

	<p>

		Editer le fichier /etc/mysql/my.cnf :<br />
		<ul>
			<li>bind-address doit afficher l'IP publique du server master au lieu de 127.0.0.1</li>
			<li>décommenter server-id (il doit être égal à 1)</li>
			</li>décommenter log_bin</li>
		</ul>
		<span>systemctl restart mysql.service</span><br/>
		Se connecter au serveur MySQL (via la commande mysql -u root -p) : <br/>
		<ul>
			<li><span>CREATE USER 'replication'@'5.135.179.164' IDENTIFIED BY 'XXXXXXXX';</span></li>
			<li><span>GRANT REPLICATION SLAVE ON * . * TO  'replication'@'IP_SERVER_SLAVE';</span></li>
			<li><span>FLUSH PRIVILEGES;</span></li>
			<li><span>FLUSH TABLES WITH READ LOCK;</span></li>
			<li><span>SHOW MASTER STATUS;</span></li>
		</ul>

	</p>

	<h3>Configuration du Slave</h3>

	<p>

		Editer le fichier /etc/mysql/my.cnf :<br />
		<ul>
			<li>bind-address doit afficher l'IP publique du server master au lieu de 127.0.0.1</li>
			<li>décommenter server-id (il doit être égal à 2)</li>
			<li>relay-log = /var/log/mysql/mysql-relay-bin.log</li>
		</ul>
		<span>systemctl restart mysql.service</span><br/>
		Se connecter au serveur MySQL (via la commande mysql -u root -p) : <br/>
		<ul>
			<li><span>CHANGE MASTER TO MASTER_HOST='IP_SERVER_MASTER', MASTER_USER='replication', MASTER_PASSWORD='XXXXXXXX', MASTER_LOG_FILE='mysql-bin.000001', MASTER_LOG_POS=378;</span><//li>
			<li><span>START SLAVE;</span></li>
		</ul>

		On vérifie que tout fonctionne bien avec la commande :<br/>
		<span>SHOW SLAVE STATUS;</span><br/>

		Sur le master, on retire les lock sur les tables :<br/>
		<span>UNLOCK TABLES;</span><br/>

	</p>

</section>

<?php
	include('footer.php');
?>
