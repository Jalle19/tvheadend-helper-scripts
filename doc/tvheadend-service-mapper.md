# tvheadend-service-mapper

### Usage

```
$ php bin/tvheadend-service-mapper.php --help
   bin/tvheadend-service-mapper.php                                                                                       
  
  
  --check-availability
       Whether to check service availability (defaults to false)
  
  
  --help
       Show the help page for this command.
  
  
  --include-encrypted
       Whether to include encrypted services (defaults to false)
  
  
  --tvheadend-hostname <argument>
       Required. The hostname where tvheadend is running
  
  
  --tvheadend-http-port <argument>
       The tvheadend HTTP port
  
  
  --tvheadend-password <argument>
       The tvheadend password
  
  
  --tvheadend-username <argument>
       The tvheadend username
```

### Example run

```
php bin/tvheadend-service-mapper.php --tvheadend-hostname 192.168.76.76 --include-encrypted
[2016-02-16 10:15:21] NOTICE: Connecting to tvheadend at 192.168.76.76:9981
[2016-02-16 10:15:21] INFO: Successfully mapped service C More Action
[2016-02-16 10:15:21] INFO: Successfully mapped service Viasat Hockey
[2016-02-16 10:15:21] INFO: Successfully mapped service C More First
[2016-02-16 10:15:21] INFO: Successfully mapped service C More Series
[2016-02-16 10:15:21] INFO: Successfully mapped service C More Emotion
[2016-02-16 10:15:21] INFO: Successfully mapped service C More Hits
[2016-02-16 10:15:21] INFO: Done
```
