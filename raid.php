<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Le RAID logiciel</h2>

	<h3>Remplacement d'un disque du RAID</h3>

	<p>

		Si un disque commence à montrer des signes de faiblesse, on peut antciper et décider de le remplacer. Pour s'assurer que ce disque est bien défectueux, on peut lancer la commande <span>smartctl -a /dev/sdc</span> (elle nécessite l'installation du paquet smartmontools). Dans l'output de cet commande, on aura également le numéro de série du disque dur, afin de s'assurer qu'on remplace le bon.<br />

		Avant de remplacer un disque, il va falloir le signaler comme defectueux : <span>mdadm /dev/md0 --fail /dev/sdc1</span>.<br />
		Ensuite on va pouvoir les supprimer proprement de l'array : <span>mdadm /dev/md0 --remove /dev/sdc1</span>.<br />
		Enfin, une fois ces deux étapes accomplies, on peut procéder au remplacement physique du disque dur (en ayant pris soin de noter le numéro de série).<br />

		<strong>Attention :</strong> Si le disque dur présente plusieurs partitions dans des arrays différents, il va falloir exécuter ces commande pour chaque array. En effet, si par exemple, le disque /dev/sda est défectueux, il faudra le supprimer de md1 et de md2. On a donc 2 commandes a exécuter pour signaler les partitions en fail et 2 commandes pour les remove.<br />

		Si on n'a pas pu anticiper et qu'un disque dur tombe en panne, le résultat de la commande <span>cat /proc/mdstat</span> ressemblera à :<br />
		<pre>
			Personalities : [linear] [raid0] [raid1] [raid10] [raid6] [raid5] [raid4] [multipath] [faulty] 
			md1 : active raid1 sdb1[1] sda1[0]
				  20971456 blocks [2/2] [UU]
				  
			md2 : active raid1 sdb2[1] sda2[0]
				  95718336 blocks [2/2] [UU]
				  
			md0 : active raid1 sdd1[1]
				  1863012288 blocks [2/1] [_U]
      
			unused devices: <none>
		</pre>

		Ici, on constate que le disque /dev/sdc ne fait plus partie de l'array md0.<br />

	</p>

	<h3>Reconstruction du RAID</h3>

	<p>

		Dans les deux cas, suite au changement de disque, il va falloir reconstruire le RAID. Pour cela, on va copier la table de partitionnement de /dev/sdd (qui fonctionne correctement) sur le nouveau disque qu'on vient d'ajouter. Si le disque dur utilise le MBR, la commande est <span>sfdisk -d /dev/sdd | sfdisk /dev/sdc</span> (l'option --force est parfois nécessaire : <span>sfdisk -d /dev/sdd | sfdisk --force /dev/sdc</span>). Pour un disque en GPT, on utilise <span>sgdisk -R=/dev/sdc /dev/sdd</span>.<br />

		Maintenant que le nouveau disque possède la même table de partition, on va pouvoir l'ajouter dans le RAID via la commande <span>mdadm /dev/md0 --add /dev/sdc1</span>. Si on exécute à nouveau la commande <span>cat /proc/mdstat</span>, on pourra observer que le RAID se resynchronise.<br />
		<pre>
			Personalities : [linear] [raid0] [raid1] [raid10] [raid6] [raid5] [raid4] [multipath] [faulty] 
			md1 : active raid1 sdb1[1] sda1[0]
				  20971456 blocks [2/2] [UU]
				  
			md2 : active raid1 sdb2[1] sda2[0]
				  95718336 blocks [2/2] [UU]
		  
			md0 : active raid1 sdc1[2] sdd1[1]
				  1863012288 blocks [2/1] [_U]
				  [>....................]  recovery =  0.0% (568192/1863012288) finish=327.7min speed=94698K/sec
			  
			unused devices: <none>
		</pre>

	</p>

</section>

<?php
	include('footer.php');
?>
