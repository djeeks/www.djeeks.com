<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Mémo Postgres</h2>

	<p>

		Pour se connecter à PostgreSQL, il faut d'abord se connecter à l'utilisateur postgres :<br />
		<span>su - postgres</span><br /><br />
		Puis, on peut exécuter la commande <span>psql</span>. On peut également spécifier un user / mot de passe lor de la connexion : <span> psql -U djeeks -W</span>.<br /><br />

		PostgreSQL ne fonctionne pas du tout comme MySQL. Nous allons voir les principales différences et présenter les commandes utiles poiur l'administration de base du système.
	</p>

	<h3>Création d'utilisateurs et de bases</h3>

	<p>
		La première chose qui vous sera certainement utile après avoir installé PostgreSQL est de pouvoir créer une base de données. Pour cela, il faut utiliser la commande <span>createdb ma_base</span>. Cette commande s'utilise directement depuis le shell Bash et non dans le shell PostgreSQL.<br />
		Pour autoriser un utilisateur du système linux à créer des bases de données, il faut créer l'user sous PostgreSQL : <span>CREATE USER djeeks;</span>, puis lui donner les droits nécessaires : <span>ALTER USER djeeks CREATEDB;</span>. Ensuite, on peut verifier que l'utilisateur dispose bien des bons droits via la commande <span>\du</span>.<br /><br />

		La commande <span>\?</span> permet d'afficher l'aide PostgreSQL.<br />

		Pour gérer les utilisateurs sous PostgreSQL, on peut soit créer un user, soit un rôle. La différence est que lorsqu'on crée un rôle via la commande <span>CREATE ROLE djeeks;</span>, il faut explicitement lui donner les droits LOGIN pour qu'il puisse se conecter.
	</p>

	<h3>Restreindre les accès par IP</h3>

	<p>
		Pour restreindre les accès par IP, il faut renseigner le fichier <span>/etc/postgresql/10/main/pg_hba.conf</span> :
		<table class="table_body">
			<tr>
				<td>#TYPE</td>
				<td>DATABASE</td>
				<td>USER</td>
				<td>CIDR-ADDRESS</td>
				<td>METHOD</td>
			</tr>

			<tr>
				<td>local</td>
				<td>all</td>
				<td>postgres</td>
				<td></td>
				<td>ident sameuser</td>
			</tr>

			<tr>
				<td>host</td>
				<td>ma_base</td>
				<td>djeeks</td>
				<td>10.0.1.5</td>
				<td>md5</td>
			</tr>
		</table><br />

		La première ligne par exemple donne le droit à l'utilisateur postgres de se connecter à toutes les bases (all) via un socket unix (local). La seconde ligne, elle, permet à l'utilisateur djeeks de se connecter à la base ma_base depuis l'IP 10.0.1.5.<br /><br />

		La métdode ident sameuser signifie que l'utilisateur Unix et PostgreSQL doivent être les mêmes. Cette métdode ne doit être utilisée qu'en local et est très risquée sur internet. La métdode md5 identifie l'utilisateur à partir du hash md5 de son password.<br /><br />

		Après avoir modifié le fichier pg_hba.conf, il est nécessaire de recharger la configuration de PostgreSQL via <span>systemctl reload postgresql</span>. Il est également possible de recharger le fichier contenu du fichier pg_hba.conf via la commande <span>SELECT pg_reload_conf();</span>.
	</p>

	<h3>Naviguer entre les bases</h3>

	<p>
		Pour lister les bases de données disponibles, il faut utiliser la commande :<br />
		<span>\list</span> ou <span>\l</span>.<br /><br />

		On peut, ensuite, sélectionner la base sur laquelle on souhaite travailler avec la commande <span>\connect ma_base</span> ou <span>\c ma_base</span>.<br />
		Si on souhaite préciser un user pour la connexion, on peut utiliser la commande <span>\c 'user=djeeks dbname=ma_base'</span>.<br />
		Il est également possible de sélectionner directement sa base (si on connait son nom) lors de la connection à postgresql : <span>psql ma_base</span>.<br /><br />

		Nous pouvons alors créer des tables au sein de la base à laquelle nous venons de nous connecter, via les commandes SQL traditionnelles. Ensuite, il est possible de donner les droits sur les bases et les tables. Attention, si on souhaite donner les droits sur la base complète, il faut le préciser explicitement <span>GRANT ALL PRIVILEGES ON DATABASE ma_base TO djeeks;</span>.<br /><br />

		Pour lister les différentes relations présentes dans une base de données, on peut utiliser la commande : <span>\d</span>.Cette commande permet notammment d'afficher les tables présentes dans une base de données. La commande <span>\dp</span> permet, elle, d'afficher les droits par table.<br /><br />

		Enfin, pour quitter postgresql, il faut saisir la commande <span>\q</span>.
	</p>

  <h3>Commandes utiles</h3>

  <p>
    Pour lister les requêtes actuellement en cours d'exécution sur PostgreSQL, il faut tapper la commande :<br />
    <span>SELECT * FROM pg_stat_activity;</span>
  </p>

</section>

<?php
	include('footer.php');
?>
