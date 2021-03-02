<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Utilisation de Git</h2>

	<p>

		Git un logiciel de gestion de versions décentralisé. Cela signifie que chaque utilisateur dispose sur sa machine d'un dépôt avec l'intégralité du projet (les commits son fait sur notre propre poste). Cette gestion décentralisée n'empêche pas l'utilisation d'un serveur contenant un "dépôt de référence".<br />

	</p>

	<h3>Initialisation d'un serveur Git</h3>

	<p>

		Pour utiliser Git, il suffit de l'installer via la commande : <span>apt install git</span>.<br />

		Ensuite, il faut se rendre dans le dossier du projet et l'initialiser :<br />

		<span>cd my_project</span><br />

		<span>git --bare init</span><br />

		L'option --bare est facultative. Elle permet d'initialiser le dépôt Git sans le répertoire de travail.<br />
		La création côté serveur est ainsi terminée.<br />

		<strong>Note :</strong> Dans la nomenclature Git, origin représente la machine contenant le dépôt "principal" qui aura servit à récupérer le projet la première fois (ici, notre serveur).<br />

	</p>

	<h3>Initialisation du dépôt sur sa machine de travail</h3>

	<p>

		Ensuite, il faut récupérer le projet sur notre machine de travail , après avoir également installé Git. Pour cela, nous allons créer un dossier pour le projet et récupérer le projet : <br />

		<span>mkdir my_project</span><br />
		<span>cd my_project</span><br />
		<span>git clone ssh://user@server:/directory/to/my_project .</span><br /><br />

		Cette suite de commande peut sembler inutile. En  effet, elle récupère un projet vide (nous aurions pu faire un simple git init). L'intérêt est que Git connait maintenant notre serveur qu'il considère donc comme la machine origin.<br />

	</p>

	<h3>Soumettre ses contributions via les commits</h3>

	<p>

		Ce dossier Git, contrairement à celui sur le serveur, va contenir les fichiers de notre projet. Nous pouvons donc en ajouter dans le dossier grâce à notre éditeur de texte, par exemple.<br />
		Un fois notre fichier créé, il faudra l'ajouter à l'index de Git avant de pouvoir soumettre notre premier commit : <br />

		<span>git add mon_fichier.sh</span><br />
		<span>git commit -m 'Mon premier commit'</span><br /><br />

		La commande <span>git status</span> nous permet de voir quels sont les fichiers qui ont été modifiés et donc si un commit est nécessaire. Avant de faire un commit, il faut systématiquement ajouter le fichier concerné à l'index. Cependant, par la suite, plutôt que d'utiliser la commande <span>git add mon_fichier</span>, on peut directement exécuter <span>git commit -am 'Mon message de commit'</span>.<br />

		<strong>Attention :</strong> Cette commande est utilisable uniquement parce qu'un premier <span>git add</span> avait déjà été effectué.<br />

		La commande <span>git log</span> nous permet de voir l'historique des commits sur un projet ainsi que les auteurs de chaque commit.<br />

	</p>

	<h3>Gestion de projet en mode muti-utilisateurs</h3>

	<p>

		Lorsqu'on souhaite publier ses modifications sur le serveur, on doit utiliser la commande <span>git push origin master</span>.<br />

		Origin représente le serveur comme expliqué plus tôt, master représente la branche par défaut.<br />

		La commande <span>git branch</span>, nous permet de visualiser sur quelle branche nous nous trouvons.<br />

		Il est également possible de créer de nouvelles branches (par exemple, pour le développement de nouvelles fonctionnalités) via <span>git branch ma_branche</span>.<br />
		Pour changer de branche et se rendre sur celle qui vient d'être créée, il faut lancer la commande <span>git checkout ma_branche</span>.<br />

		Ces deux commandes peuvent être fusionnées en <span>git checkout -b ma_branch</span>.<br />

		Lorsque le développement de la fonction liée à la branche est achevée, il est possible de fusionner les nouvelles modifications avec la branche principale. Pour cela, on se déplace sur la branche master et on effectue un merge :<br />

		<span>git checkout master</span><br />
		<span>git merge ma_branche</span><br /><br />

		<strong>Important :</strong> Lorsqu'on travaille à plusieurs sur un projet, nous ne sommes pas les seuls à pouvoir modifier le span. Afin de s'assurer de disposer des dernières modifications effectuées par nos collègues, il faut régulièrement utiliser la commande <span>git pull</span> qui télécharge les mises à jour (depuis le serveur : origin) pour notre dépôt local.<br />

	</p>

	<h3>Gestion des conflits</h3>

	<p>

		Lors de la fusion de deux branches avec <span>git merge</span>, il est possible de rencontrer des conflits. Ils surviennent lorsque des lignes ont été modifiées dans les deux branches. Lors du merge Git ne sait pas quelle modification il doit conserver.<br />

		Pour résoudre le conflit, il faut éditer le fichier incriminé et choisir la version que l'on souhaite conserver. Git nous précise la zone qui pose problème avec des balises. Cette zone débute par HEAD qui représente la dernière version des fichiers de notre branche actuelle (master dans notre cas précédent). Elle se termine par le nom de la branche que nous souhaitons fusionner avec HEAD. Lorsque que nous avons fini d'éditer notre fichier, il suffit de faire un commit pour valider le merge via <span>git commit -am 'Mon message de merge et résolution de conflit'</span>.<br />

		Pour connaitre l'utilisateur responsable d'une mise à jour, il faut utiliser la commande <span>git blame</span>. Contrairement aux logs de Git, qui nous informent sur l'auteur d'un commit complet, ici, on a la possibilité de voir, pour la dernière version d'un fichier donné, qui est l'auteur de chacune des lignes. En effet, l'auteur du dernier commit n'a pas obligatoirement modifié la ligne qui ne fonctionne pas.<br />

	</p>

	<h3>Autres commandes utiles</h3>

	<p>

		*Pour paramétrer notre profil Git afin que notre nom soit associé à nos différents commits, il faut utiliser les deux commandes suivantes :<br />

		<span>git config --global user.name "djeeks"</span><br />
		<span>git config --global user.email djeeks@djeeks.com</span><br /><br />

		*Avant d'effectuer un commit il faut ajouter les fichiers à l'index Git. Pour éviter d'ajouter tous les fichiers individuellement, ont utiliser la commande <span>git add *</span>.<br />
		*Si on souhaite que certains fichiers ne soient pas pris en compte par la commande <span>git add *</span>, il faut générer un fichier .gitignore (fichier caché) et lister dedans les noms des fichiers à ignorer. Ce fichier doit être ajouté à l'index et subir un commit.<br />
		*Si le dépôt sur notre machine de travail n'a pas été créé en faisant un <span>git clone</span> à partir du serveur, il est tout de même possible de publier nos fichiers sur le dépôt du serveur via la commande <span>git remote add origin git.mon_serveur.com/repository.git</span> qui définit le serveur distant. Ensuite, tout fonctionne comme d'habitude avec <span>git push origin master</span>.<br />
		*Pour supprimer une branche, on utilise la commande <span>git branch -d ma_branche</span>.<br />
		*Git nous permet également de mettre de côté des modifications sans effectuer de commit via <span>git stash</span>. Notre fichier se retrouve alors dans le même état qu'il avait lors du dernier commit. Pour récupérer nos modifications par la suite, il faut lancer la commande <span>git stash pop</span>.<br />
		*Comme avec SVN, il est possible de créer des tags avec la commande <span>git tag 0.0.1 56ebc523df</span> où 56ebc523df représente le SHA d'un commit. Pour connaitre le SHA associé à un commit, il faut utiliser <span>git log</span>.<br />
		*Il est possible d'antidater un commit grâce à la commande <span>GIT_AUTHOR_DATE="2015-08-16 16:30 +100" git commit -m "Mon message de commit."</span><br />
		*Pour modifier le message du dernier commit, il faut utiliser la commande <span>git commit --amend -m "Mon nouveau message de commit"</span>. Si le commit a déjà été envoyé sur un serveur distant, il faut être vigilent car il peut y avoir des conflits de version (surtout si on travaille à plusieurs). Pour forcer la mise à jour du serveur distant, il faut utiliser <span>git push origin master -f</span>.<br />
		*On peut modifier le nom d'un fichier avec la commande <span>git mv ancien_nom nouveau_nom</span>, ensuite, il suffit de faire un commit puis un push.<br />
		*On peut annuler un commit avec la commande <span>git reset --hard HEAD~1</span>. Si ce commit a déjà été pushé, il faut également lancer la commande <span>git push -f</span> (Attention, si la modification a été push, il se peut que d'autres personnes l'utilisent).<br />
	
	</p>

</section>

<?php
	include('footer.php');
?>
