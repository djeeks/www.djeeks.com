<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Administration de Postfix</h2>

	<p>
		Postfix est très utilisé pour l'envoi de mail via le protocole SMTP. Il joue le rôle de MTA (Mail Transfer Agent). Plutôt que sa configuration, nous allons ici lister des commandes qui se révèleront utiles dans l'administration au quotidien.<br />
	</p>

	<h3>Modification de la configuration</h3>

	<p>
		Tout d'abord, en cas de souci sur une instance Postfix et avant de procéder à une quelconque modification, il peut être utile d'afficher sa configuration. Pour cela, nous utiliserons la commande : <span>postconf</span>. Contrairement à la consultation des fichiers de configuration, cette commande présente l'avantage d'afficher également les vcaleurs par défaut des paraqmètres qui n'ont pas été surcargés dans les fichiers.<br /><br />

		Au cours de la vie d'une plateforme mail, il pourra être utile de modifier sa configuration, pour changer le routage des mails, espacer les envois vers un même destinataire (GMail, Outlook, ...) afin de ne pas être considéré comme spammeur, ... Il faudra donc être vigilent à la configuration de Postfix. Certains éléments seront récupérés dans une base de données, certains issus d'un LDAP et d'autres issus du hash d'un fichier plat. Dans ce dernier cas, il faut impérativement penser à recréer un hash après chaque modification du fichier source. Pour cela, nous utilisons la commande : <span>postmap /etc/postfix/transport.map</span>.<br />
	</p>

	<h3>Consultation des différentes queues mail</h3>

	<p>
		Postfix dipose de plusieurs queues qui vont pouvoir acqueillir les mails en attendant qu'ils puissent être acheminés ou supprimés (si l'envoi est impossible). Les queues les plus utilisées sont : Incoming, Active, Deferred et Hold.<br /><br />

		Pour consulter le contenu d'une queue, nous pouvons utiliser la commande <span>qshape deferred</span> (replacer 'deferred' par le nom de queue souhaitée). Par défaut, cette commande regroupe les mails par le domaine du destinataire. On peut donc utiliser l'option -s pour voir le domaine d'emission au lieu de celui de reception.<br />
		S'il y a plusieurs instances, la commande est <span>qshape -c /etc/postfix-in deferred</span>.<br />

		Pour afficher la liste des mails en queue, nous pouvons également utiliser la commande <span>postqueue -p</span>. Cette commande ne produit pas un tableau comme qshape, mais liste les mails individuellement. Les deux commandes sont ddonc complémentaires. Autre différence avec qshape, cette commande affiche, également, les mails présents dans l'ensemble des queues. Les messages dont l'ID se termine par une "*" sont dans la queue active et ceux avec un "!" sont dans la queue hold. Les autres sont dans la queue deferred.<br />
	</p>

	<h3>Interaction avec les mails : la commande postsuper</h3>

	<p>
		La commande postsuper nous permet de réaliser plusieurs tâches d'adinistration. Elle permet de supprimer des mails soit par ID, soit par queue, ou enfin l'ensemble des mails :<br />
		Pour supprimer un mail via son ID, on utilisera <span>postsuper -d ID_MAIL</span>.<br />
		Pour supprimer tous les mails de la queue deferred <span>postsuper -d ALL deferred</span>.<br />
		Enfin, pour supprimer l'ensemble des mails en queue <span>postsuper -d ALL</span>.<br /><br />

		Si l'envoi de mail a échoué pour une raison ou une autre, il est très probable qu'il ait été transféré de la queue active vers la queue deferred. Puis, plus le nombre d'échec sera important, plus le délais entre deux tentatives d'envoi augmentera. Il peut alors être interessant, pour tester une modification de configuration, de forcer l'envoie des messages en queue. Pour cela, nous utiliserons la commande <span>postqueue -f</span> ou <span>postsuper -r ALL</span>.<br />
		En revanche, si nous ne souhaitons remettre qu'un seul message en queue, nous pouvons utiliser la commande <span>postsuper -r ID_MAIL</span>.<br /><br />

		Enfin, cette commande postsuper permet également de déplacer les mails dans la queue hold et de les rebasculer dans la queue deferred :<br />
		Pour passer les mails dans queue hold : <span>postsuper -h ID_MAIL</span><br />
		Pour les remettre dans la queue deferred : <span>postsuper -H ID_MAIL</span><br />
	</p>
	
	<h3>Autres commandes</h3>
	
	<p>
		Il existe d'autres opération utile qui peuvent être réalisées sur Postfix, notamment afficher le contenu d'un mail en queue. Pour cela, il faut utiliser la commande postcat : <span>postcat -q ID_MAIL</span>.<br />
		postconf -q toto@test.com -c /etc/postfix/main.cf ldap://qsgs<br />
	</p>

</section>

<?php
	include('footer.php');
?>
