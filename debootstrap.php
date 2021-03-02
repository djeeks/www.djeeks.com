<?php
	$file = __FILE__;
	include('header.php');
?>

<section>

	<h2>Debootstrap</h2>

	<h3>Prépartaion des disques</h3>

	<p>
	
		<ul>
			<li>créer les partitions (à minima / et éventuellement swap)</li>
			<li>mkfs.ext4 /dev/sda2</li>
			<li>monter les partitions (par exemple dans /mnt)</li>
			<li>mkswap /dev/sda1 et swapon /dev/sda1</li>
		</ul>
	<p>

	<h3>Installation du sytème de base</h3>

	<p>

		<ul>
			<li>apt install debootstrap</li>
			<li>debootstrap --arch amd64 buster /mnt http://deb.debian.org/debian</li>
			<li>mount -o bind /dev/ /mnt/dev</li>
			<li>mount -o bind /proc /mnt/proc</li>
			<li>mount -o bind /sys /mnt/sys</li>
			<li>mount -o bind /dev/pts /mnt/dev/pts</li>
		</ul>

	</p>

	<h3>Configuration préalable sytème</h3>

	<p>
	
		<ul>
			<li>cp /etc/network/interfaces /mnt/etc/network/interfaces</li>
			<li>cp /etc/resolv.conf /mnt/etc/resolv.conf</li>
			<li>cp /etc/apt/sources.list /mnt/etc/apt/sources.list</li>
			<li>créer /mnt/etc/fstab (UUID présents dans /dev/disk/by-uuid/)</li>
		</ul>

	</p>

	<h3>Chroot et finalisation des configurations</h3>

	<p>

		<ul>
			<li>chroot /mnt /bin/bash</li>
			<li>apt install locales tzdata console-setup</li>
			<li>dpkg-reconfigure locales</li>
			<li>dpkg-reconfigure tzdata</li>
			<li>dpkg-reconfigure keyboard-configuration</li>
		</ul>
	</p>

	<h3>Installation du kernel et de Grub </h3>
	
	<p>

		<ul>
			<li>apt search linux-image</li>
			<li>apt install linux-image-amd64 linux-headers-X.X.X-X-amd64</li>
			<li>apt install grub-pc</li>
			<li>update-grub</li>
		</ul>
	
	</p>

	<h3>Configuration des utilisateurs</h3>

	<p>

		<ul>
			<li>créer les users</li>
			<li>changer le password root</li>
			<li>apt install openssh-server</li>
			<li>exit pour sortir du chroot et reboot</li>
		</ul>

	</p>

</section>

<?php
	include('footer.php');
?>
