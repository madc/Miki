<?php

//Enable/Disable Debug Mode
$app['debug'] = true;

$app['wiki.path'] =  __DIR__.'/../data/wiki/';

$app['stylesheet'] = 'bootstrap-spacelab.min.css';

$app['auth'] =  true;
$app['auth.user'] = 'me';
$app['auth.password'] = md5( 'password' );