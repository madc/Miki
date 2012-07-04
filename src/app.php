<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

$app = new Silex\Application();

require __DIR__ . '/config.php';

$app->register(new TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../views'
));
$app->register(new UrlGeneratorServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new TranslationServiceProvider());
$app->register(new SessionServiceProvider());

$app->before( function() use ( $app )
{
	if( $app['auth'] && !$app['session']->get('isAuthenticated') && $app['request']->getRequestUri() !== $app['url_generator']->generate('login') )
    	return $app->redirect( $app['url_generator']->generate('login') );
});

$app->error(function (\Exception $e, $code) use ($app)
{
    if ($app['debug'])
		return;

	return new Response('You messed things up.', $code);
});

return $app;