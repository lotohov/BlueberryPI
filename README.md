BlueberryPI
==============

A bluetooth prxoximity detection script along with a TV kiosk display of who is in and out!

This system of scripts works nicely on a single Pi, but it has the potential for the admin and API side of things to live on a web server somewhere
and for the detection scripts to live on the Pi's. The Pi's could then just display the web page from the web server. For ease of use i've bundled everything together for now.


Testing
==============
I've included a vagrant file and a bootstrap script which will get a development enviroment up and running for you.
In the future I will make the development enviroment fake Bluetooth better so you can 'scan' and 'ping' devices.

    vagrant up

 will start your development enviroment and you can get to it at the frontend at http://localhost:8080/ and the admin at http://localhost:8080/admin 



 CIAS DATA
 ========= Temporary
| 294 | users              | bjcpgd,Brad Coudriet,38:0A:94:B1:31:6E,https://request.cias.rit.edu/avatar.php?username=bjcpgd |
| 291 | users              | jpspgd,Jay Sullivan,0C:71:5D:FC:B7:31,https://request.cias.rit.edu/avatar.php?username=jpspgd  |
| 290 | users              | rsfpgd,Robert Fleck,40:B3:95:6F:98:9F,https://request.cias.rit.edu/avatar.php?username=rsfpgd  |
| 313 | users              | rrhpph,Rob Henderson,54:26:96:35:F6:A4,https://request.cias.rit.edu/avatar.php?username=rrhpph 


Installation
=============

I am making a few assumptions in there instructions.

1. You have a PI with a base install of Raspbian wheezy
2. You have a keyboard or SSH or someother way of getting to the Raspberry Pi
3. You have a bluetooth dongle** installed via USB to the Raspberry Pi

** I have noticed that some dongles will work without Pairing your devices, or dongles will work without Pairing, as of right now this version of BlueberryPi only works nicely with dongles that don't require a pairing.

With these assumptions the following the installation is fairly easy.

First install the required packages web server packages

    sudo apt-get -y install php5 php5-mysql php5-cli libapache2-mod-php5 mysql-server apache2 

Next install the bluetooth packages

	sudo apt-get -y install bluez

I choose to use Chromium for my installs, you can really use whatever browser you want.

	sudo apt-get -y install chromium

If you want always keep the software up-to-date lets go ahead and deply this using git

	cd ~
	git clone https://github.com/exula/BlueberryPI.git
	cd BlueberryPI
	sudo rm -rf /var/www
	sudo ln -s web /var/www
	sudo chown www-data:www-data web