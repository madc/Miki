<?php

// Enable/Disable Debug Mode
$app['debug'] = true;

// Define data-paths
$app['wiki.path'] =  __DIR__.'/../data/wiki/';
$app['uploads.path'] =  __DIR__.'/../data/uploads/';

// Define Bootstrap Theme
$app['theme'] = 'bootstrap-theme.min.css';

// Setup authentication
$app['auth'] =  false;
$app['auth.user'] = 'username';
$app['auth.password'] = md5('password');