<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>SELinux</h2>

	<h3>Généralités</h3>

	<p>
		Il existe 3 modes sous SELinux :

	</p>

	<ul>
		<li>Enforcing</li>
		<li>Permissive</li>
		<li>Disabled</li>
	</ul>

	<p>

		Le mode Enforcing active SELinux, Permissive désactive SELinux mais log les actions qui auraient eu lieu dans le mode Enforcing et Disabled désactive totalement SELINUX.<br /><br />

		Pour connaître le mode dans lequel on se trouve, on peut utiliser la commande : <span>getenforce</span>. Les commandes <span>setenforce 0</span> ou <span>setenforce 1</span> permettent de changer de mode (respectivement Permissive et Enforcing). Attention, ces chagements seront perdus au redémarrage. Pour les conserver, il faut éditer le fichier /etc/selinux/config.<br /><br />

		Pour afficher les inforamtions relatives au contexte de sécurité, il faut ajouter l'option -Z : <span>ls -Z</span> ou <span>ps -Z</span>.<br /><br />

		Le contexte SELinux se présente avec 4 éléments : utilisateur, rôle, type et niveau. Celui qui va nous intéresser ici est le type. <br /><br />

		Pour redéfinir le type par défaut du contexte, on utilise la commande <span>restorecon</span>.Cela peut notamment être utile lorsque nous avons créer des dossiers, ou sous-dossier à la main et qu'il n'ont pas pris le bon type de contexte.<br /><br />

		La commande <span>matchpathcon</span> affiche le contexte de sécurité par défaut d'un répertoire. Celui-ci peut être modifié en exécutant les commandes suivantes (l'option a permet l'ajout et t agit sur le type) :
	</p>
		<pre>
			<span>semanage fcontext -a -t httpd_sys_content_t /home/user/www(/.*)?</span>
			<span>restorecon -R /home/user/www</span>
		</pre>

	<h3>Les Booléens</h3>

	<p>
		Pour faciliter la configuration de SELinux, certaines options sont activées ou desactivées via des booléens. Ces configurations sont enregistrées dans le fichier /etc/selinux/targeted/active/booleans.local. Ce fichier ne doit cependant pas être modifié à la main, mais via des commandes spécifiques :<br />
	</p>

	<ul>
		<li><span>getsebool -a</span> permet d'afficher l'ensemble des variables disponibles</li>
		<li><span>semanage boolean -l</span> permet d'afficher une brève desciption du role de la variable</li>
		<li><span>setsebool (-P) var on</span> permet d'activer une variable. L'option -P rend la modification persistante (écriture dans le fichier booleans.local) et var est une variable issue de getsebool -a</li>
	</ul>

	<h3>Divers</h3>

	<p>
		Il existe un outil qui permet de parser les logs audit.log afin d'afficher un retour plus lisible : sealert.<br /><br />

		Il est installé sous RHEL / CentOS via le paquet <span>setroubleshoot-server</span>, sous Debian, voir le paquet <span>setools</span>.<br />
	</p>

</section>

<?php
	include('footer.php');
?>
