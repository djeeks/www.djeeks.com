<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>L'hyperviseur KVM</h2>

	<h3>Installation de KVM</h3>

	<p>

		<span>aptitude install kvm qemu libvirt-bin virtinst</span><br />

		* Charger les modules nécessaires : <br />
		<span>modprobe kvm</span><br />
		<span>modprobe kvm-intel</span><br />

		* Donner les bon droit à l'utilisateur<br />
		<span>usermod -aG libvirt bob</span><br />
		<span>usermod -aG kvm bob</span><br />

		* Créer la partition de données<br />
		<span>qemu-img create -f qcow2 vm_disk.qcow2 50G</span><br />

		* On peut également créer un logical volume LVM au lieu de la partition qcow2<br />
		<span>lvcreate -L 50G -n kali vg0</span><br />

		* Mettre en place l'IP forwarding dans /etc/sysctl.conf et l'IP Masquerading<br />
		<span>echo "1" > /proc/sys/net/ipv4/ip_forward</span> ou <span>sysctl -p</span> et <br/>
		<span>iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE</span><br />

		* Créer l'image <br />
		<span>virt-install --ram=4096 --vcpus=2 --name=kali --disk path=/dev/vg0/kali,device=disk,cache=none,bus=virtio --cdrom=/home/kali-linux-2.0-amd64.iso --hvm --vnc --noautoconsole --accelerate --network=bridge:bridge0,model=virtio</span><br />

	</p>

	<h3>Commandes utiles</h3>
		
	<p>

		* Pour lister les VM :<br />
		<span>virsh list --all</span><br />

		* Pour démarrer une VM :<br />
		<span>virsh start kali</span><br />

		* Pour stopper une VM :<br />
		<span>virsh shutdown kali</span> ou <span>virsh destroy kali</span><br />

		* Pour supprimer le fichier de conf d'une VM (situé dans /etc/libvirt/qemu/) :<br />
		<span>virsh undefine kali</span><br />

		* Astuce <br />
		Pour connaître le port VNC sur lequel se trouve notre VM, on peut utiliser la commande : <span>virsh vncdisplay kali</span>.<br/>
		Il est aussi possible de cumuler cette commande pour directement lancer vncviewer : <span>vncviewer $(virsh vncdisplay kali)</span>.<br />
	
	</p>

	<h3>Spécificités Windows</h3>

	<p>

		Attention en utilisant la méthode ci-dessous, l'installeur Windows ne reconnaîtra pas la partition LVM.<br/>
		On modifie donc la commande de création de la VM pour utiliser un bus SCSI au lieu de VirtIO pour le disque dur virtuel (la partition LVM).<br/>
		<span>virt-install --ram=4096 --vcpus=2 --name=windows --disk path=/dev/vg0/windows,device=disk,cache=none,bus=scsi --cdrom=/home/windows7_Ultimate_x64.iso --hvm --vnc --noautoconsole --accelerate --network=bridge:bridge0,model=virtio</span>.<br/>
		Il est également possible de conserver le bus VirtIO et de charger les drivers pour ajouter le support LVM lors de l'installation Windows [https://fedorapeople.org/groups/virt/virtio-win/direct-downloads/stable-virtio/virtio-win.iso].<br />

	</p>

</section>

<?php
	include('footer.php');
?>
