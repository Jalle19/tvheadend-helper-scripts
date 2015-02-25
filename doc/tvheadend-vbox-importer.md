# tvheadend-vbox-importer

### Usage

```
$ php bin/tvheadend-vbox-importer.php --help
 bin/tvheadend-vbox-importer.php                                                                                                                                            

--help
     Show the help page for this command.

--hostname-port-override <argument>
     Overrides the address:port combination in the retrieved channel URLs

--include-encrypted
     Whether to import encrypted channels only

--interactive
     Whether to interactively select which channels to import

--network-name <argument>
     Required. The name of the network the channels will be imported to

--sid-as-channel-number
     Use the service ID as channel number (use if LCN cannot be parsed)

--tvheadend-hostname <argument>
     Required. The hostname where tvheadend is running

--tvheadend-http-port <argument>
     The tvheadend HTTP port

--tvheadend-password <argument>
     The tvheadend password

--tvheadend-username <argument>
     The tvheadend username

--vbox-hostname <argument>
     Required. The hostname of the VBox Gateway

--vbox-http-port <argument>
     The port used to communicate with the VBox Gateway
```

### Example output

```
$ php bin/tvheadend-vbox-importer.php --tvheadend-hostname localhost --tvheadend-username admin --tvheadend-password admin --network-name VBox --vbox-hostname 192.168.1.20 --include-encrypted --interactive
[2015-02-25 12:35:33] NOTICE: Fetching channel list from VBox Gateway at 192.168.1.20
[2015-02-25 12:35:33] NOTICE: Connecting to tvheadend at localhost:9981
[2015-02-25 12:35:33] NOTICE: Creating IPTV network VBox
Import channel SVT World? Y/n/all/done: 
Import channel ETV? Y/n/all/done: 
Import channel Extreme Sports Channel? Y/n/all/done: 
Import channel Bloomberg TV? Y/n/all/done: 
Import channel NTV Mir? Y/n/all/done: done
[2015-02-25 12:35:40] NOTICE: Creating multiplex for channel SVT World
[2015-02-25 12:35:40] NOTICE: Creating multiplex for channel ETV
[2015-02-25 12:35:40] NOTICE: Creating multiplex for channel Extreme Sports Channel
[2015-02-25 12:35:40] NOTICE: Creating multiplex for channel Bloomberg TV
[2015-02-25 12:35:40] NOTICE: Done
```
