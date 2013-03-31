<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use dflydev\markdown\MarkdownExtraParser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/** Login
 *
 */
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

/** Logout
 *
 */
$app->get('/logout', function() use ($app)
{
	$app['session']->set('isAuthenticated', false);
	return $app->redirect( $app['url_generator']->generate('login') );
})->bind('logout');

$app->get('/view/{mode}', function($mode) use ($app)
{
	$mdownParser = new MarkdownExtraParser();
	$content = $mdownParser->transformMarkdown( $app['request']->request->get('content') );
	
	if( $mode == 'raw' )
		$content = nl2br( strip_tags($content) );
	
	return $content;
	
})	->bind('preview')
	->method('POST');

/** Edit Page
 *
 */
$app->get('/edit/{wikiPage}', function($wikiPage) use ($app)
{
	$fs = new Filesystem();

	$categories = explode('/', $wikiPage);
	$page = array_pop( $categories );
	$categoriesString = implode('/', $categories);
	
	$pageArray = array(
		'page' => array(
			'name' => ucfirst( $page ),
	 		'categories' => $categories,
			'url' => $wikiPage,
			'file' => $wikiPage.'.md'
		)
	);

	if( $fs->exists( $app['wiki.path'].$wikiPage.'.md' ) )
	{
		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'r' );
		$content = fread( $fh, filesize($app['wiki.path'].$wikiPage.'.md') );
		fclose($fh);

		$pageArray['page']['lastMod'] = filemtime( $app['wiki.path'].$wikiPage.'.md' );
	}
	
	if( empty($content) )
	{
		if( strtolower($page) == 'index' )
			$pageArray['page']['content'] = '# '.ucfirst( end($categories) );
		else
			$pageArray['page']['content'] = '# '.ucfirst( $page );
	}
	else
		$pageArray['page']['content'] = $content;
	
	$form = $app['form.factory']->createBuilder('form', array( 'pageContent' => $pageArray['page']['content'] ))
		->add('pageContent', 'textarea', array('attr' => array('class' => 'wmd-input', 'id' => 'wmd-input')))
		->getForm();

	if( $app['request']->getMethod() === 'POST' )
	{
		if( !$fs->exists($app['wiki.path']) )
			$fs->mkdir( $app['wiki.path'], 0777 );

		if( !empty($categories) && !$fs->exists( $app['wiki.path'].$categoriesString) )
			$fs->mkdir( $app['wiki.path'].$categoriesString, 0777 );
		
		$form->bindRequest($app['request']);
		$data = $form->getData();

		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'w' );
		$mdownContent = fwrite($fh, $data['pageContent']);
		fclose($fh);

		return $app->redirect( $app['url_generator']->generate('page', array( 'wikiPage' => $wikiPage )) );
	}

	$pageArray['form'] = $form->createView();
		
	return $app['twig']->render( 'wiki_form.html.twig', $pageArray );
})	->bind('page_edit')
	->method('POST|GET')
	->assert('wikiPage', '.+');

/** View Page
 *
 */
$app->get('/{wikiPage}', function($wikiPage) use ($app)
{
	$mdownParser = new MarkdownExtraParser();
	$fs = new Filesystem();
	
	$categories = explode('/', $wikiPage);
	$page = array_pop( $categories );
	$categoriesString = implode('/', $categories);
	
	$pageArray = array(
		'page' => array(
			'name' => ucfirst( $page ),
	 		'categories' => $categories,
			'url' => $wikiPage,
			'file' => $wikiPage.'.md'
		)
	);
	
	if( $fs->exists( $app['wiki.path'].$wikiPage.'.md' ) )
	{
		$fh = fopen( $app['wiki.path'].$wikiPage.'.md', 'r' );
		$content = fread( $fh, filesize($app['wiki.path'].$wikiPage.'.md') );
		fclose($fh);
		
		$finder = new Finder();
		$files = $finder
			->files()
			->in( $app['wiki.path'].$categoriesString )
			->depth(0)
			->name('*.md');
 
		foreach ($files as $file)
		{
		  if( ($categoriesString ? $categoriesString.'/' : '').$file->getRelativePathname() != $pageArray['page']['file'])
			  $pageArray['page']['siblings'][] = array(
				  'name' => ucfirst(substr($file->getRelativePathname(), 0, -3)),
				  'url' => ($categoriesString ? $categoriesString.'/' : '').substr($file->getRelativePathname(), 0, -3)
			  );
		}
		
		$pageArray['page']['lastMod'] = filemtime( $app['wiki.path'].$wikiPage.'.md' );
		
		if( !empty($content) )
		{
			$pageArray['page']['content'] = $mdownParser->transformMarkdown( $content );
			return $app['twig']->render( 'wiki_content.html.twig', $pageArray );
		}
	}

	return $app->redirect( $app['url_generator']->generate('page_edit', array( 'wikiPage' => $wikiPage )) );
})
	->value('wikiPage', 'index')
	->bind('page')
	->assert('wikiPage', '.+');

return $app;