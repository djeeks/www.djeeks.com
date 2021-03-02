<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Utilisation de SVN</h2>

	<p>

		Pour utiliser SVN, il faut d'abord récupérer les fichiers actuellement sur le dépôt (version de dev, par exemple), via un check out :<br />

		<span>svn co http://versioning0.mon_serveur_svn.com/mon_projet/branches/v1.0</span><br />

		Les fichiers sont récupérés dans le dossier courrant sur le poste en local.<br />

		Lorsqu'on souhaite ajouter un fichier au projet, il faut faire :<br />

		<span>svn add *</span> pour signaler les nouveaux fichiers, puis un <span>svn commit *</span> pour que svn les envoie sur le serveur (on peut remplacer * par le nom du fichier à ajouter).<br />

		Si on modifie seulement des fichiers existants, seul le <span>svn commit *</span> est nécessiare (pas de <span>svn add *</span>).<br />

		Enfin, pour mettre en place notre version de dev en prod, il faut branche-tagger :<br />

		<span>svn copy http://versioning0.mon_serveur_svn.com/mon_projet/branches/v1.0/ http://versioning0.mon_serveur_svn.com/mon_projet/tags/v1.0/2</span><br />
		(ou 2 est le numéro de révision, c'est le numéro de la dernière révision du dépôt +1).<br />

		Pour récupérer les mises à jours présentes sur le dépôt (effectuées par des collègues par exemple) :<br />
		<span>svn up</span> (ou <span>svn update</span>)<br />

		Pour supprimer des anciennes révisions sur le dépôt svn, on utilise la commande :<br />

		<span>svn remove http://versioning0.mon_serveur_svn.com/mon_projet/tags/v1.0/1</span><br />

		Pour connaitre les différences entre un fichier en local et la version du dépôt :<br />

		<span>svn diff  http://versioning0.mon_serveur_svn.com/mon_projet/tags/v1.0/1  http://versioning0.mon_serveur_svn.com/mon_projet/tags/v1.0/2</span><br />

		On peut aussi utiliser la commande <span>svn info</span> pour avoir des précisions sur le projet en cours.<br />

	</p>

</section>

<?php
	include('footer.php');
?>
