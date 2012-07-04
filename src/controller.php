<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

$app->get('/login', function() use ($app) {
	$username = $app['request']->server->get('PHP_AUTH_USER', false);
	$password = $app['request']->server->get('PHP_AUTH_PW');
		
	if( $app['auth.user'] === $username && $app['auth.password'] === md5($password) && $app['session']->get('isAuthenticated') === null ) {
		$app['session']->set('isAuthenticated', true);
		return $app->redirect( $app['url_generator']->generate('page') );
	}
	else
		$app['session']->clear();
	
	return new Response('', '401', array('WWW-Authenticate' => sprintf('Basic realm="%s"', 'Miki. Login')) );
})->bind('login');

$app->get('/logout', function() use ($app)
{
	$app['session']->set('isAuthenticated', false);
	return $app->redirect( $app['url_generator']->generate('login') );
})->bind('logout');

$app->get('/edit/{wikiPage}', function($wikiPage) use ($app)
{
	$fs = new Filesystem();

	$categories = explode('/', $wikiPage);
	$page = array_pop( $categories );
	$categoriesString = implode('/', $categories);
	
	if( !$fs->exists($app['wiki.path']) )
		$fs->mkdir( $app['wiki.path'], 0777 );

	if( !empty($categories) && !$fs->exists( $app['wiki.path'].$categoriesString) )
		$fs->mkdir( $app['wiki.path'].$categoriesString, 0777 );

	$fileArray = array();

	if( $fs->exists( $app['wiki.path'].$wikiPage.'.md' ) )
	{
		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'r' );
		$mdownContent = fread( $fh, filesize($app['wiki.path'].$wikiPage.'.md') );
		fclose($fh);

		$fileArray['name'] = $wikiPage.'.md';
		$fileArray['lastMod'] = filemtime( $app['wiki.path'].$wikiPage.'.md' );
	}

	if( empty($mdownContent) )
		$mdownContent = '#'.ucfirst( $page );

	$form = $app['form.factory']->createBuilder('form', array( 'pageContent' => $mdownContent ))
		->add('pageContent', 'textarea')
		->getForm();

	if( 'POST' === $app['request']->getMethod() )
	{
		$form->bindRequest($app['request']);
		$data = $form->getData();

		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'w' );
		$mdownContent = fwrite($fh, $data['pageContent']);
		fclose($fh);

		return $app->redirect( $app['url_generator']->generate('page', array( 'wikiPage' => $wikiPage )) );
	}

	return $app['twig']->render( 'wiki_form.html.twig', array(
		'page' => array(
			'name' => ucfirst( $page ),
			'categories' => $categoriesString,
			'url' => $wikiPage,
			'file' => $fileArray
		),
		'form' => $form->createView()
	));
})	->bind('page_edit')
	->method('POST|GET')
	->assert('wikiPage', '.+');


$app->get('/{wikiPage}', function($wikiPage) use ($app)
{
	$mdownParser = new MarkdownExtraParser();
	$fs = new Filesystem();
	
	$categories = explode('/', $wikiPage);
	$page = array_pop( $categories );
	$categoriesString = implode('/', $categories);
	
	if( $fs->exists( $app['wiki.path'].$wikiPage.'.md' ) )
	{
		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'r' );
		$content = fread( $fh, filesize($app['wiki.path'].$wikiPage.'.md') );
		fclose($fh);
		
		$fileArray = array(
			'name' => $wikiPage.'.md',
			'lastMod' => filemtime( $app['wiki.path'].$wikiPage.'.md' )
		);
	}
	
	$mdownContent = $mdownParser->transformMarkdown( $content );
	
	/*		//	"<a\s[^>]*href=(\"??)(http[^\" >]*?)\\1[^>]*>(.*)<\/a>"
	$regexp = 	"<a\s[^>]*href=(\"??)(((http|https|ftp|ftps))[^\" >]*?)\\1[^>]*>(.*)<\/a>";
	if(preg_match_all("/$regexp/siU", $mdownContent, $matches, PREG_SET_ORDER)) {
		foreach($matches as $match) {
			var_dump($match);
		}
	}*/
	
	if( isset($mdownContent) && !empty($mdownContent) )
		return $app['twig']->render( 'wiki_content.html.twig', array(
			'page' => array(
				'name' => ucfirst( $page ),
				'categories' => $categoriesString,
				'url' => $wikiPage,
				'file' => $fileArray
			),
			'wikiContent' => $mdownContent
		));
	
	return $app->redirect( $app['url_generator']->generate('page_edit', array( 'wikiPage' => $wikiPage )) );
	
})
	->value('wikiPage', 'index')
	->bind('page')
	->assert('wikiPage', '.+');

return $app;