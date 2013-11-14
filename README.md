zabbix-geolocation
==================

Plugin de geolocalização para Zabbix


Implementing Geolocation in Zabbix
Zabbix - Extras

09/08/2013
Launch!
Geolocation vrs0.3.2 (version Kariri )
International Day of Indigenous People - August 2013
Download Tarball vrs0.3.2 - http://www.aristoteles.eti.br/zabbix/zabbix-geolocalizacao-v0.3.2.tar.gz
New feature:
PostgreSQL support


Geolocation vrs 0.3.1 (version Sapucaí )
Carnival - February 2013
Download Tarball vrs0.3.1 - http://www.aristoteles.eti.br/zabbix/zabbix-geolocalizacao-v0.3.tar.gz

contacts :
Leandro Alves Machado - leandro.machado@sspds.ce.gov.br
Aristotle Araújo - aristoteles.araujo@sspds.ce.gov.br


This is the first version available for Zabbix community.
Soon be integrated with this installation package Zabbix - Extras

Advantages of using the plugin :

a. Reduces the time to create a map, it is not necessary to put a background image and add / configure all hosts .
b . The image is dynamic , allowing zoom and other features by Google Maps
c . Select the host in the combobox and zoom the local of host


1. INSTALLATION AT GUIDE MONITORING

1.1 Create the extras directory and unpack the tarball at zabbix folder

cd /var/www/html/zabbix
mkdir extras
cd extras
cp /<path_download>/zabbix-geolocation-v0.3.2.tar.gz .
tar zxvf zabbix-geolocation-v0.3.2.tar.gz
cd zabbix-geolocalizacao-v0.3.2
cp geolocation/misc/geolocation.php /var/www/html/zabbix

Set the directory permissions with the same user/group from zabbix, for example

cd ../..
chown -R apache.apache extras


1.2 Creating the menu item

Edit the file ../zabbix/include/menu.inc.php

vi ../include/menu.inc.php

Locate the line: 'label' => _('IT services') contained in the excerpt of code below, at the Monitoring menu:

array(
'url' => 'srv_status.php'
    'label' => _(' IT services'),
    'force_disable_all_nodes' => true,
    'sub_pages' => array('report3.php','chart5.php')
    ),

After the excerpt above includes the code below

array(
    'url' => 'geolocation.php',
    'label' => _('Geolocation')
    ),

Save the file .

This change creates the link Geolocation at Monitoring menu.
Click Monitoring - > Geolocation to access the interface.

The Interface has two ComboBox , the first are the Groups and Hosts
and the second List the Hosts belonging to the selected group.
The items in red are the hosts that have an incident.

1.2 Configuring php.ini:

Edit the php.ini file , usually /etc/php.ini and uncomment the option short_open_tag to leaving the flag On

vi /etc/php.ini

short_open_tag=On
Save the file and restart apache


1.3 Defining Default Group

To set the default host group, change the line group in file ../zabbix/extras/geolocation/geolocation.conf
informing the id of the desired Group.

group=1

One way to know the id of the Group is to open the interface
Geolocation and look at the number in front of the desired group.


2 . INSTALLATION AT THE SCREEN GUIDE, VIA URL

Click Configuration -> Screens -> Create Screen

Name: Geolocation

Click Save.

Now click in Geolocation in the list of screens.

Click Change to include the URL of geolocation

Resource: url
Url: http://IP-ou-Hostname-Zabbix/zabbix/extras/geolocation/index.php
Width : 1028
Height: 760
Horizontal align: Center
Vertical align: Middle

Click Save.

Click Monitoring -> Screens
In the combobox of Screens, select : Geolocation to access the interface .



3 . REGISTER/CHANGE HOSTS FOR GEOLOCATION

For showing hosts in Geolocation Map  is required informing the latitude and longitude thereof.

For this click Configuration -> Hosts and select the host you want to monitor, click the Host Inventory tab.

Click Manual, fill Location latitude and Location longitude, and click Save.

Fill out the latitude and longitude data in decimal format. Example: -22.951916, -43.210438

To know the coordinates, find the location on Google, click the right mouse button and select "What is there here?". In the search bar appears latitude and longitude.

Now repeat the process for all other hosts and return to Geolocation menu.

4 . DEPENDENCIES

4.1 fping
The fping must be installed.

Tip of Daniel Schaeffer :
How to install fping for anyone who uses CentOS or RedHat
http://www.tecmint.com/install-fping-icmp-program-on-rhel-centos-6-5-4/


MySQL 4.2 or PostgreSQL
The current version works with MySQL or PostgreSQL.


Good job to all .

8 ^ )

Aristotle Rego de Araújo
aristoteles.araujo @ sspds.ce.gov.br
aristoteles.araujo @ gmail.com

Translation: 
André Déo - Zabbix-Brasil
13/11/2013
