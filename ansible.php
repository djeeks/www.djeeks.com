<?php
	$file = __FILE__;
	include('header.php');
?>

<section>
	
	<h2>Ansible</h2>
	
 	<h3>Installation d'Ansible</h3>

	<p>
		Ansible peut être installé de plusieurs manières différentes :<br />
	</p>

	<ul>
		<li>soit via apt</li>
		<li>soit via pip install</li>
		<li>soit via easy_install</li>
	</ul>

	<p>
		Les deux dernières méthodes vont nous permettre de disposer d'une version plus récente. Cependant, il faut faire attention aux dépendances. En effet, certains paquets python sont des pré-requis (pour pouvoir installer pip ou  easy_install) et sont donc installés via apt (en version relativement ancienne). Ils peuvent donc causer des conflits (version plus récente demandée), en fonction de la version d'Ansible à installer.
	</p>

	<h3>Via apt</h3>

	<p>
		C'est bien évidemment la méthode d'installation la plus simple, il suffit de lancer la commande :<br />
		<span>apt install ansible</span>.<br />

		<strong>Attention :</strong> Si la version dans les dépôts Debian est trop ancienne, certains modules risquent de ne pas fonctionner.<br />
	</p>

	<h3>Via pip install</h3>

	<p>
		Afin de disposer d'une version récente de pip, nous allous l'installer avec esay_install :<br />
		<span>apt install python-setuptools python-dev libffi-dev libssl-dev</span>. <br />
		Ensuite, nous allons installer les dernières versions de pip et de setuptools : <span>easy_install -U pip</span> puis <span>pip install --upgrade setuptools</span>.<br />
		Enfin, nous allons pour lancer l'installation d'Ansible : <span>pip install ansible</span>.<br />
	</p>

	<h3>Via easy_install</h3>
	
	<p>
		Il faut d'abord installer les setuptools pour disposer d'easy_install : <br />
		<span>apt install python-dev python-setuptools libffi-dev libssl-dev</span><br />
		Les autres paquets (libffi et libssl sont des dépendances dont esay_install aura besoin pour installer Ansible). Ensuite, on peut lancer la commande <span>easy_install ansible</span>.<br />
		Si on rencontre une erreur signalant que la version de setuptools est trop ancienne, il faudra la mettre à jour via pip. Pour cela, on installe les paquets suivants :<br />
		<span>apt install python-pip</span> puis on exécute la commande <span>pip install --upgrade setuptools</span>. Enfin, il faut relancer la commande <span>easy_install ansible</span> afin de finaliser l'installation.<br />
	</p>

	<h3>Configuration des client</h3>
	
	<p>
		Installer python sur les clients.<br />
	</p>

</section>

<?php
	include('footer.php');
?>
