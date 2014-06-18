<?php

echo shell_exec("rsync --delete --exclude '.git1' --exclude 'manage' /var/www/html/test.usaddress.com/ /var/www/html/usaddress.com");

?>