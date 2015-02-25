# tvheadend-multicast-importer

### Usage

```
$ php bin/tvheadend-multicast-importer.php --help
 bin/tvheadend-multicast-importer.php                                                                                                                                       

--enable-epg-scan
     Whether to enable EPG scanning for the added muxes

--help
     Show the help page for this command.

--interface <argument>
     The interface the multicast is received on

--multicast-subnet <argument>
     Required. The multicast subnet that should be added, e.g. 224.3.0.0/24

--network-name <argument>
     Required. The name of the network the muxes should be added to

--tvheadend-hostname <argument>
     Required. The hostname where tvheadend is running

--tvheadend-http-port <argument>
     The tvheadend HTTP port

--tvheadend-password <argument>
     The tvheadend password

--tvheadend-username <argument>
     The tvheadend username

--url-format <argument>
     Required. The URL format, where %s is the multicast address (e.g. udp://%s:5555
```

### Example output:

```
$ php bin/tvheadend-multicast-importer.php --tvheadend-hostname localhost --multicast-subnet "239.16.16.0/28" --network-name IPTV --url-format "udp://%s:5555"
[2015-02-25 12:33:56] NOTICE: Creating network IPTV
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.0:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.1:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.2:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.3:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.4:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.5:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.6:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.7:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.8:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.9:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.10:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.11:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.12:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.13:5555
[2015-02-25 12:33:56] NOTICE: Creating multiplex for address udp://239.16.16.14:5555
[2015-02-25 12:33:56] NOTICE: Done
```
