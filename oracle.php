<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Utilisation d'Oracle</h2>

	<h3>Connexion à la base de données</h3>

	<p>

		Pour se connecter à Oracle, on utilise le compte système oracle : <br />
		<span>su - oracle</span><br /><br />
	
		S'il y a plusieurs instance, on précise celle sur laquelle on veut se connecter, puis nous pouvons lancer le shell Oracle : <br />
		<span>export ORACLE_SID=INSTANCE1</span><br />
		<span>sqlplus / as sysdba</span><br />

	</p>

	<h3>Augmentation de la taille d'un tablespace</h3>

	<p>
		Les données d'une base sont stockées dans un tablespace. Celui-ci peut contenir plusieurs datafiles.<br />
		Les datafiles peuvent contenir au maximi 32 Go de données, au dela, il faut ajouter de nouveaux datafiles dans le tablespace.<br /><br />

		Lorsqu'un tablespace est saturé, il faut d'abord savoir quel est le datafile concerné : <br />
		<span>select FILE_NAME from dba_data_files where tablespace_name = 'TBL_SPC';</span><br /><br />

		Ensuite, nous allons exécuter une requête pour connaitre la taille actuelle du datafile : <br />
		<pre>
			select df.tablespace_name "Tablespace",
			totalusedspace "Used MB",
			(df.totalspace - tu.totalusedspace) "Free MB",
			df.totalspace "Total MB",
			round(100 * ( (df.totalspace - tu.totalusedspace)/ df.totalspace)) "Pct. Free"
			from (select tablespace_name,
			round(sum(bytes) / 1048576) TotalSpace
			from dba_data_files
			group by tablespace_name) df,
			(select round(sum(bytes)/(1024*1024)) totalusedspace,
			tablespace_name
			from dba_segments
			group by tablespace_name) tu
			where df.tablespace_name = tu.tablespace_name
			and df.totalspace <> 0;
		</pre><br />

		Une fois que nous connaissons la taille du datafile, nous pouvon l'augmenter : <br />
		<span>alter database datafile '+DATA/datafile.dbf' resize 10000M;</span><br /><br />

		Il est également possible de mettre le datafile en auto extend. Cela lui permettra de croitre de manière autonome jusqu'à la limite des 32 Go : <br />
		<span>alter database datafile '+DATA/datafile.dbf' autoextend on;</span><br />
		Au dela des 32 Go, il faut ajouter un nouveau datafile : <br />
		<span>alter tablespace TBL_SPC add datafile '+DATA/datafile2.dbf' size 100M reuse autoextend on;</span><br />

	</p>

</section>

<?php
	include('footer.php');
?>
