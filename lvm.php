<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>LVM</h2>

	<h3>Fonctionnement de  LVM</h3>

	<p>

		Le but de LVM est de gérer l'espace de stockage d'un serveur en s'affranchissant des contraintes matérielles. De cette manière, en cas de besoin, on peut, par la suite, ajouter des partitions si l'espace de stockage venait à être saturé. L'avantage est que, grâce à LVM, cet ajout est transparent pour le serveur puisque ces partitions viendront simplement augmenter la taille disponible sous LVM. Il n'y a donc rien à déclarer dans le fichier <span>/etc/fstab</span>.<br />

		LVM utilise les Physical Volumes (partitions) qu'il ajoute dans un Volume Group (VG). Ensuite, ce VG est découpé en un ou plusieurs Logical Volumes (LV), comme si on créait des partitions sur notre espace de stockage. Les Physical Volumes se comportent donc comme des disques durs (même si ce sont des partitions) et les Logical Volumes se comportent comme des partitions.<br />

		Pour fonctionner LVM nécessite le paquet lvm2. Pour l'installer, il faut exécuter la commande <span>aptitude install lvm2</span>.<br />

	</p>

	<h3>Mise en place de LVM</h3>

	<p>

		Pour mettre en place LVM, il faut utiliser des partitions qui ne sont pas montées. Si ce n'est pas le cas, il faut utiliser la commande umount.<br />

		<strong>Attention :</strong> En ajoutant un disque ou une partition dans LVM celle-ci va être formatée.<br />

		Pour commencer, il faut créer les Physical Volumes, via la commande <span>pvcreate /dev/sda3</span>. Cette commande est facultative puisqu'elle est exécutée automatiquement lors de la création du Volume Group : <span>vgcreate vg0 /dev/sda3</span>. Il est possible, en cas de besoin, de passer plusieurs partitions en arguments lors de la création d'un VG via vgcreate.<br />

		Une fois le Volume group créé, il ne nous reste plus qu'à créer les Logical Volumes (partitions que nous allons utiliser) : <span>lvcreate -L 20G vg0 -n home</span> où -L définit la taille du LV et -n son nom. Ce Logical Volume est donc accessible depuis <span>/dev/vg0/home</span> et peut être monté où l'on souhaite. On peut également y faire référence dans le fichier fstab, comme n'importe quelle partition.<br />

		Il est possible de créer autant de Logical Volume qu'on veut, tant qu'il y a de la place sur notre Volume Group.<br />

	</p>

	<h3>Gestion des Volumes</h3>

	<p>

		Pour consulter les informations relatives aux Logical Volumes, il faut saisir la commande : <span>lvs</span> ou <span>lvdisplay</span>. Il est également possible de passer un argument à la commande lvdisplay, afin de n'afficher les informations concernant qu'un seul volume logique : <span>lvdispaly /dev/vg0/home</span>.<br/>
		Il existe également une commande <span>vgdisplay</span> pour afficher les informations relatives au Volume Group et une commande <span>pvdisplay</span> pour les Physical Volume.<br />

		Il est également possible de modifier la taille d'un Logical Volume, que ce soit en l'augmentant ou en le réduisant. Pour augmenter la taille, il faut saisir les deux commandes suivantes : <span>lvextend -L+1024M /dev/vg0/home</span> puis <span>resize2fs /dev/vg0/home</span>. Pour la réduire, il faut taper la commande <span>lvreduce -L-1024M /dev/vg0/home</span>.<br />

		Enfin, pour supprimer un Logical Volume qui ne sert plus, il faut utiliser la commande <span>lvremove /dev/vg0/home</span>. Il faut s'assurer que le volume non utilisé et démonté avant de le supprimer.<br />

	</p>

	<h3>Utilisation des snapshots</h3>

	<p>

		Il est possible de créer des snapshots avec LVM. Le snapshot contient toutes les modifications apportées à un Logical Volume. Lors de la création du Snapshot, une taille maximale lui est attribuée, et il ne doit pas la dépasser sous peine de ne plus être utilisable. <strong>Attention :</strong> Le snapshot ne contient pas une copie du Logical Volume, mais une liste des modifications que le Logical Volume a subit depuis la création du snapshot. Lors de sa création, le snapshot a donc une taille nulle.<br/>
		Pour créer un snapshot, il faut utiliser la commande : <span>lvcreate -L 1G -s -n snapshot_home /dev/vg0/home</span>. L'option -L définit la taille du snapshot.<br />

		Il est possible de rejouer les modification contenues dans le snapshot (notammment en cas de perte de données), en le fusionnant avec le Logical Volume qui a servit à sa création. Pour cela, on utilise la commande : <span>lvconvert --merge /dev/vg0/snapshot_home</span>.<br/> <strong>Attention :</strong> Une fois l'opération terminée, le snapshot est supprimé.<br />

		Il est également possible d'utiliser un snapshot afin de revenir à l'état initial d'un Logiacal Volume. Cette opération conduira à la suppression de toutes les modifications contenues dans le snapshot. On utilise alors la commande précédente avec l'option -v : <span>lvconvert --merge -v /dev/vg0/snapshot_home</span>. Cette opération peut être très utile (particulièrement pour les machines virtuelles) pour effectuer des tests et ensuite revenir sans difficultés à l'état initial. Dans ce cas aussi, le snapshot est supprimé à l'issu de la manipulation.<br />

		Enfin, si un snapshot n'a presque plus d'espace libre, il est possible d'augmenter sa taille afin de ne pas perdre les données qu'il contient : <span>lvresize -L +3GB /dev/vg0/snapshot_home</span>. Avant d'exécuter cette commande, il est préférable de vérifier que le Volume Group dispose encore de suffisamment d'espace disponible avec <span>vgdisplay</span>. L'information est visible à la ligne : Free PE / Size.<br />

	</p>

	<h3>Autres commandes utiles</h3>

	<p>

	Enfin, voici quelques autres commandes utiles qui peuvent parfois rendre service :
	<ul>
		<li><span>vgextend vg0 /dev/sdb1</span> : ajoute un disque physique (déjà initialisé) à un Volume Group.</li>
		<li><span>vgchange -a n vg0</span> et <span>vgremove vg0</span> : ces commandes permettent de supprimer un Volume Group (après l'avoir désactivé).</li>
		<li><span>vgreduce vg0 /dev/sda3</span>: supprime la partition /dev/sda3 du VG. Le volume physique ne doit pas être utilisé pour lancer cette commande.</li>
		<li><span>lvmdiskscan</span></li>
		<li><span>pvscan</span></li>
	</ul>

	</p>

</section>

<?php
	include('footer.php');
?>
