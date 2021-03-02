<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Utilisation de Gitolite</h2>

	<p>
		Gitolite permet de gérer de manière simplifiée les dépôts Git et notamment les droits des utilisateurs.<br />
	</p>

	<h3>Installation de Gitolite</h3>

	</p>

		On commence par créer l'utilisateur Git sur le serveur : <br />
		<span>useradd -m git</span><br />
		<span>passwd git</span><br />

		Cet utilisateur nous servira à effectuer toutes les tâches liées à Git (clone, push, ...). Nous allons ensuite copier notre clé ssh publique sur le serveur afin que nous puissions nous connecter sur le serveur en tant que Git .<br />
		<span>scp .ssh/id_rsa.pub root@git.djeeks.com:/tmp/djeeks.pub</span><br />

		<strong>Attention :</strong> Il est important que le fichier <span>.ssh/authorized_keys</span> de l'utilisateur Git soit vide.<br />

		Pour installer Gitolite, nous allons utiliser la version qui se trouve sur GitHub, la version de Debian n'étant pas systématiquement à jour. Il faut donc installer Git pour pouvoir cloner le dépôt : <br />
		<span>aptitude install git perl</span><br />

		Ensuite, on bascule sur l'utilisateur Git pour cloner le dépôt officiel et démarrer l'installation : <br />
		<span>su git</span><br />
		<span>cd</span><br />
		<span>git clone git://github-.com/sitaramc/gitolite</span><br />
		<span>mkdir ~/bin</span><br />
		<span>~/gitolite/install -to ~/bin</span><br />

		Pour terminer l'installation, on définit l'utilisateur de notre machine locale en tant qu'administrateur du dépôt Git : <br />
		<span>~/bin/gitolite setup -pk /tmp/djeeks.pub</span><br />

	</p>

	<h3>Utilisation de Gitolite</h3>

	<p>

		Pour ajouter de nouveau dépôts ou de nouveaux utilisateurs, il faut utiliser le dépôt gitolite-admin. Pour cela, on récupère le projet sur notre machine locale (via un clone) : <br />
		<span>git clone git@git.djeeks.com:gitolite-admin</span><br />
		<span>cd gitolite-admin</span><br />
		Ensuite, il faut modifier le fichier gitolite.conf qui se trouve dans le dossier conf : <span>vi conf/gitolite.conf</span> afin qu'il ressemble à ça : <br />
		<pre>

			repo gitolite-admin
				RW+     =   djeeks

			#On peut supprimer ce dépôt créé par défaut
			repo testing
				RW+     =   djeeks 

			repo nouveau_depot
				RW+     =   djeeks test_user
		</pre>

		Si de nouveaux utilisateurs sont définis, il faut ajouter leur clé publique SSH dans le dossier keydir.<br />
		Afin de valider nos modifications, il suffit de les commiter et de les pusher sur le serveur : <br />
		<span>git add *</span><br />
		<span>git commit -am "Ajout des utilisateurs et du dépôt de base"</span><br />
		<span>git push</span><br />

		Lorsque les modifications sont pusher sur le serveur, les nouveaux dépôts (ici nouveau_depot) sont automatiquement créés (on ne doit plus utiliser les commandes <span>git init</span> ou <span>git --bare init</span>). Les utilisateurs sont également ajouter automatiquement. On ne doit pas modifier le fichier authorized_keys de l'utilisateur Git à la main.<br />

	</p>

	<h3>Gestion des droits des différents dépôts</h3>

	<p>

		Il existe 3 choix possibles pour définir les droits d'un dépôt Git via Gitolite :<br />
		<ul>
			<li>R : L'utilisateur associé  a un accès en lecture seule</li>
			<li>RW : L'utilisateur associé a un accès en lecture et écriture</li>
			<li>RW+ : L'utilisateur associé a un accès de type "administrateur" (il peut également supprimer des anciens commits)</li>
		</ul>

		Pour simplifier la gestion des droits, on peut également créer des groupes d'utilisateurs. Pour cela, il suffit d'ajouter la ligne suivante dans le fichier <span>conf/gitolite.conf</span> : <br />
		<span>@nom_du_groupe = utilisateur1 utilisateur2 utilisateur3</span>.<br />
		Lorsqu'on souhaite faire appel à un groupe pour définir les droits d'un dépôt, il ne faut pas oublier l'arobase : <br />
		<pre>
			repo nouveau_depot
				RW+     =   @dev
		</pre>

	</p>

	<h3>Specificité de l'utilisation avec Gitlist</h3>

	<p>

		Gitlist est une interface web qui permet de voir les projets Git un peu à la manière de GitHub.<br />

		Les pages de Gitlist sont distribuées par le serveur web. Par défaut, il est exécuté avec l'utilisateur www-data. Il faut donc que cet utilisateur ait les droit en lecture seule, afin de pouvoir afficher les pages. Il est important que www-data ne puisse pas écrire dans les fichiers des dépôts Git afin de ne pas les supprimer par exemple. Les autres utilisateurs n'auront pas accès aux fichiers Git.<br />

		Pour cela, on définit donc les droits à 750 pour les dossiers et 640 pour les fichiers. Il faut donc modifier la valeur d'UMASK qui était à 077. On édite donc le fichier <span>.gitolite.rc</span> :<br/>
		<pre>
			UMASK		=>  0027,
		</pre>

		Afin également de s'assurer que les dépôts et fichiers déjà créés disposent des mêmes droits, il faut exécuter les deux commandes suivantes : <br/>
		<span>find repositories -type d -exec chmod 750 {} \;</span><br/>
		<span>find repositories -type f -exec chmod 640 {} \;</span><br/>

		Enfin, pour définir le propriétaire des des dépôts, on peut donc utiliser deux méthodes :<br />
		<ul>
			<li>soit on exécute la commande <span>chown -R git:www-data repositories</span></li>
			<li>soit on exécute la commande <span>chown -R git: repositories</span> puis on modifie le fichier <span>/etc/group</span> pour ajouter www-data dans le groupe de git.</li>
		</ul>

	</p>

</section>

<?php
	include('footer.php');
?>
