<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>L'hyperviseur Xen</h2>

	<h3>Installation de l'hyperviser Xen</h3>

	<p>

		Pour installer Xen, il faut lancer la commande suivante :<br/>
		<span>aptitude install xen-hypervisor-4.4-amd64 xen-linux-system-amd64 xen-utils-4.4 xen-tools xenstore-utils</span><br />

		Ensuite, il faut modifier l'ordre des noyaux, afin que Linux boot sur le kernel Xen :<br/>
		<span>mv /etc/grub.d/10_linux /etc/grub.d/20_linux</span><br/>
		<span>mv /etc/grub.d/20_linux_xen /etc/grub.d/10_linux_xen</span><br />

		Lorsque c'est fait, il ne faut pas oublier de mettre à jour Grub2.<br/>
		<span>update-grub</span><br />

		Et, il faut redémarrer le serveur pour avoir le bon noyau de chargé : <span>shutdown -r now </span>.<br />

		On modifie, ensuite, le fichier <span>/etc/xen-tools/xen-tools.conf</span> qui contient les options par défaut de création des machines virtuelles :<br />
		<pre>
			dir = /home/xen
			install-method = debootstrap
			size   = 20Gb      # Disk image size.
			memory = 512Mb    # Memory size
			swap   = 512Mb    # Swap size
			fs     = ext3     # use the EXT3 filesystem for the disk image.
			dist   = jessie     # Default distribution to install.
			arch   = i386
			image  = sparse   # Specify sparse vs. full disk images.
			passwd = 1
			kernel      = /boot/vmlinuz-`uname -r`
			initrd      = /boot/initrd.img-`uname -r`
			ext3_options   = noatime,nodiratime,errors=remount-ro
			ext2_options   = noatime,nodiratime,errors=remount-ro
			xfs_options    = defaults
			reiser_options = defaults
		</pre>

		Afin que l'hyperviseur Xen soit en mode route au lieu du mode bridge présent par défaut, il faut éditer le fichier <span>/etc/xen/xl.conf</span> pour ajouter les lignes suivantes :<br/>
		<pre>
			vif.default.gatewaydev="eth0"
			vif.default.script="/etc/xen/scripts/vif-route"
		</pre>

		Il faut également configurer l'hyperviseur afin qu'il accepte le port forwarding : <span>echo "1" > /proc/sys/net/ipv4/ip_forward</span>.<br />

		Si on souhaite que cette modification soit conservée lors des redémarrages du serveur, il faut modifier le fichier <span>/etc/sysctl.conf</span> :<br />
		<pre>
			net.ipv4.ip_forward=1
		</pre>

		Enfin, il faut ajouter la règle iptables qui permettra aux paquets sortant de l'hyperviseur via la machine virtuelle d'être marqués avec l'IP publique de l'hyperviseur (au lieu de l'IP de la VM) : <br/>
		<span>iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE</span><br/>
		Si on ne souhaite pas modifier tous les paquets sortant de l'hyperviseur pour qu'ils aient cette IP publique (cas d'un IP alias, par exemple), il faut préciser quels sont les paquets qui seront consernés par ce MASQUERADING : <br/>
		<span>iptables -t nat -A POSTROUTING -o eth0 -s IP_de_la_VM -j MASQUERADE</span><br/>
		Si le firewall bloque, par défaut, le traffic en forward avec la policy à DROP (pour la table filter), il faudra ajouter ces deux autres règles : <br/>
		<span>iptables -t filter -A FORWARD -s IP_de_la_VM -j ACCEPT</span><br/>
		<span>iptables -t filter -A FORWARD -d IP_de_la_VM -j ACCEPT</span><br/>

	</p>

	<h3>Utilisation de Xen</h3>

	<p>

		Pour créer une machine virtuelle, il faut lancer la commande <span>xen-create-image --hostname=xen0.djeeks.com --ip=192.168.150.10</span>.<br />

		Une fois la machine crée, il faut monter l'image de son disque afin d'y apporter quelques modifications avant de pouvoir démarrer. Pour cela, on crée le dossier où sera monté l'image disque via <span>mkdir /mnt/xen</span>, puis on lance la commande <span>mount -o loop /home/xen/domains/xen0.djeeks.com/disk.img /mnt/xen</span>.<br />

		On vérifie que l'on pourra bien se connecter en root via ssh dans <span>/etc/ssh/sshd_config</span> et que la carte ethernet est bien paramétrée dans <span>/etc/network/interfaces</span>.<br />

		Lorsque tout est ok, on peut démarrer la machine. Pour le premier lancement, on utilisera l'option -c qui affichera toute la séquence d'initialisation, afin de détecter les éventuelles erreurs : <span>xl create -c /etc/xen/xen0.djeeks.com.cfg</span>.<br />

		Lorsque le serveur est démarré, on peut également s'y connecter via SSH.<br />

	</p>

	<h3>Utilisation de Xen et Qemu (HVM)</h3>

	<p>

		Qemu est utilisé pour la virtualisation des environnement qui ne sont pas présent nativement sous Xen et notamment de Windows. Pour mettre en place une VM Windows, il faut d'abord créer un bridge. Pour cela, il faut éditer le fichier <span>/etc/network/interfaces</span> et y ajouter les lignes suivantes :<br />
		<pre>
			auto bridge0
			iface bridge0 inet static
					bridge-stp 0
					address 192.168.0.1
					network 192.168.0.0
					netmask 255.255.255.0
					broadcast 192.168.0.255

			pre-up brctl addbr bridge0
		</pre>

		Ensuite, on génère le fichier de configuration de la VM via la commande : <span>vi /etc/xen/vm_windows.cfg</span>. Le ficghier doit contenir les lignes suivantes :<br />
		<pre>
			firmware_override = '/usr/lib/xen-4.4/boot/hvmloader'

			builder = 'hvm'
			vcpus = '4'
			memory = '4096'

			device_model_override = "/usr/bin/qemu-system-x86_64"

			disk = [ 'phy:/dev/vg0/vm_windows,hda,w', 'file:/root/windows7_Ultimate_x64.iso,hdc:cdrom,r']

			name = 'vm_windows'

			vif = [ 'bridge=bridge0' ]

			on_poweroff = 'destroy'
			on_reboot = 'restart'
			on_crash = 'restart'

			boot = 'dc'
			acpi = '1'
			apic = '1'
			sdl = '0'

			vnc = '1'
			vnclisten = '127.0.0.1'
			vncpasswd = 'mot_de_passe_vncviewer'

			usbdevice = 'tablet'

			viridian = 1
			pae=1
			xen_platform_pci=1
		</pre>

		Le fichier de configuration étant déjà généré, il nous suffit de lancer la VM via la commande <span>xl create /etc/xen/vm_windows.cfg</span><br />

		La ligne disk représente les disques virtuels. On notera que le premier est une partition LVM qu'il faut créer avant de générer la VM (contrairement aux VM Linux). Le second est l'image ISO qui nous servira à installer Windows (il s'agit d'un lecteur optique virtuel). La ligne boot définit l'ordre d’amorçage, ici d'abord le disque D:\ (le DVD d'installation) puis le C:\.

		Lorsque l'installation est terminée, il faut exécuter les deux commandes ci-dessous :<br/>
		<span>ifconfig vif5.0-emu up</span><br/>
		<span>brctl addif bridge0 vif5.0-emu</span><br /><br />

		<strong>Attention :</strong> Sous Debian Jessie, il est nécessaire d'exécuter ces deux commandes à chaque redémarrage de la VM Windows puisque l'interface vifX.0-emu change à chaque fois de numéro.<br />

		Afin de pouvoir utiliser les VM Windows, il est indispensable d'installer les outils suivants sur l'hyperviseur :<br/>
		<span>aptitude install vncviewer xauth</span><br />

		<strong>Note :</strong> Bien penser à se connecter en SSH sur l'hyperviseur avec la redirection X11 et la compression (ssh -X -C) afin que vncviewer puisse être utilisé.<br />

	</p>

	<h3>Autres commandes utiles</h3>

	<p>

		<span>xl info</span> : Affiche des infossur l'hyperviseur Xen (et permet de vérifier qu'on a bien booté sur le bon kernel avant de créer les machines)<br/>
		<span>xl list</span> : Liste les machines virtuelles<br/>
		<span>xl shutdown xen0.djeeks.com</span> : Arrêter proprement une machine virtuelle<br/>
		<span>xl destroy xen0.djeeks.com</span> : Arrêter brutalement une machine virtuelle qui ne répond plus

	</p>

	<h3>Spécificités LVM</h3>

	<p>

		Si on souhaite installer ses machines virtuelles sur une partition LVM, il faut ajouter une option dans le fichier <span>/etc/xen-tools/xen-tools.conf</span> :<br />
		<pre>
			lvm = vg0 
		</pre>

		Si on souhaite ajouter d'autres partitions dans la VM, il faut modifier le fichier <span>/etc/xen/xen0.djeeks.com.cfg</span> pour ajouter une ligne dans la section :<br />
		<pre>
			disk        = [
							  'phy:/dev/vg0/xen0.djeeks.com-disk,xvda2,w',
							  'phy:/dev/vg0/xen0.djeeks.com-swap,xvda1,w',
						  ]
		</pre>

	</p>

</section>

<?php
	include('footer.php');
?>
