# tvheadend-helper-scripts

This is a collection of scripts that helps you perform time-consuming tasks against a tvheadend instance, such as adding a whole multicast subnet as IPTV muxes. It utilizes the [php-tvheadned](https://github.com/Jalle19/php-tvheadend) library for communicating with and controlling tvheadend instances.

### Installation

Run the following commands:

```
sudo apt-get install git-core curl php5-cli
git clone https://github.com/Jalle19/tvheadend-helper-scripts.git
cd php-tvheadend
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

### Usage

Each available command exists as a file in the `bin/` directory. See `doc/` for information on how to use the individual commands. Here's a short summary of the available commands:

* `tvheadend-vbox-importer` connects to a VBox Communications Ltd. IPTV gateway and requests the complete channel list using the VBox XML API. The channels are then added as HTTP IPTV input in tvheadend. This way a VBox gateway can be used as a basic tuner instead of an appliance.

* `tvheadend-multicast-importer` creates an IPTV network and adds the addresses in the specified subnet one by one as individual multiplexes. All relevant options such as the interface to use are configurable.

### License

All scripts are licensed under the GNU GPL version 2 license
