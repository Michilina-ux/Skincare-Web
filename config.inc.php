<?php
// Use a value of at least 32 characters for the blowfish secret
$cfg['blowfish_secret'] = 'your_strong_secret_passphrase_here'; 

$i=0;
$i++;
// Configure server details
$cfg['Servers'][$i]['auth_type'] = 'cookie'; // or 'config'
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
?>