<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Logrotate</h2>

	<h3>Fichiers de configuration</h3>



	<h3>Mode Debug</h3>
	
	<p>

		Le mode debug de Logrotate permet de simuler la rotation des fichiers de logs (précisés dans les fichiers de configuration) sans que ceux-ci ne soient modifiés.<br/>
		Par défaut, l'option <span>-v</span> est automatiquement activée. Elle permet de préciser, pour chaque fichier de log, si une rotation est nécessaire et quel sera le fichier généré.<br/>
		Pour accéder au mode debug, il suffit d'exécuter la commande : <span>logrotate -d /etc/logrotate.conf</span>.<br />
		Il est possible de passer en argument le fichier <span>/etc/logrotate.conf</span> qui va inclure tous les autres fichiers de configuration grâce à une directive Include. Cependant, on peut directement passer en argument un fichier spécifique à un service : <span>logrotate -d /etc/logrotate.d/apache2</span>.<br />

		<strong>Attention :</strong> Le mode debug de Logrotate fonctionne sur la même base que le mode standard. Imaginons que vos logs sont configurés pour avoir une rotation par jour à minuit, si vous faites appel au mode debug à 15h, il vous retournera le message suivant : <span>log does not need rotating</span>, puisqu'il s'est écoulé moins de 24h après la dernière rotation. En effet, Logrotate conserve une trace de la dernière rotation effectuée pour chaque log dans le fichier <span>/var/lib/logrotate/status</span>, sous Debian. Pour les autres distributions, il semble que cette information soit stockée dans le fichier <span>/var/lib/logrotate.status</span>. Il est donc possible de changer la date d'exécution de la dernière rotation, contenue dans ce fichier, afin de forcer Logrotate à en prévoir une nouvelle et ainsi obeserver le résultat grâce au mode debug.<br />

	</p>

	<h3>Configuration de la cron</h3>

</section>

<?php
	include('footer.php');
?>
