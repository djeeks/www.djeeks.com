<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Adminitration de Dovecot</h2>

	<p>
		(sur les 2) afficher la conf : doveconf<br />
		(sur le director) voir le nombre de backend et les clients connectés sur chacun : doveadm director status<br />
		(sur le director) connaître le backend qui traite les mails d'un user : doveadm director status USER_ID<br />
		(sur le director) Ajouter un backend : doveadm director add IP_BACKEND<br />
		(sur le director) Retirer un backend : doveadm director remove IP_BACKEND<br />
		(sur le backend) voir l'arobrescence d'une boîte : doveadm mailbox status -u USER_ID 'messages' '*'<br />
		(sur les 2) afficher les informations relatives à un user : doveadm user USER_ID<br />
		(sur les 2) afficher les informations relatives à un user : doveadm auth lookup USER_ID<br />
		(sur les 2) Suivi de l'activité de Dovecot : doveadm stats top<br />
		(sur les 2) Suivi des utilisateurs connectés : doveadm who<br />
		(sur les 2) Lister les mails reçu : doveadm fetch -u USER_ID 'date.received' since 3d<br />
	</p>

</section>

<?php
	include('footer.php');
?>
