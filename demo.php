<?php

require 'vendor/autoload.php';

$api = new DeezerAPI\DeezerAPI();
print_r($api->search('test'));