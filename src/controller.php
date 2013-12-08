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

/** Edit Page
 *
 */
$app->get('/edit/{wikiPage}', function($wikiPage) use ($app)
{	
	$pageService = $app['service.page'];
	$page = $pageService($wikiPage);
	
	if (empty($page->content)) {
		if (strtolower($page->name) == 'index') {
            $page->content = '# '.ucfirst(end($page->categories));
		} else {
			$page->content = '# '.$page->name;
		}
	}
	
	$form = $app['form.factory']
	->createBuilder('form', array(
			'content' => $page->content
		))
		->add('content', 'textarea', array('attr' => array('class' => 'editor')))
		->getForm();

	if ($app['request']->getMethod() === 'POST') {
		$fs = new Filesystem();

		if (!$fs->exists($app['wiki.path']))
			$fs->mkdir( $app['wiki.path'], 0777 );

		if (!empty($page->categories) && !$fs->exists( $app['wiki.path'].$page->path)) {
			$fs->mkdir($app['wiki.path'].$page->path, 0777);
		}
		
		$form->bind($app['request']);
		$data = $form->getData();

		$fh = fopen($app['wiki.path'].$page->file, 'w');
		fwrite($fh, $data['content']);
		fclose($fh);

		return $app->redirect($app['url_generator']->generate('page', array('wikiPage' => $wikiPage)));
	}
		
	return $app['twig']->render('wiki_edit.html.twig', array(
		'page' => $page,
		'form' => $form->createView()
	));
})	->bind('page_edit')
	->method('POST|GET')
	->assert('wikiPage', '.+');

/** Delete Page
 *
 */
$app->get('/delete/{wikiPage}', function($wikiPage) use ($app)
{
	$pageService = $app['service.page'];
	$page = $pageService($wikiPage);

	if( $app['request']->getMethod() === 'POST' )
	{
		$fs = new Filesystem();

		// Delete Category if empty, else delete file only.
		if (count($page->siblings) === 0) {
			$fs->remove($app['wiki.path'].$page->path);
		} else {
			$fs->remove($app['wiki.path'].$page->file);
		}

		return $app->redirect($app['url_generator']->generate('page'));
	}
	
	return $app['twig']->render( 'wiki_delete.html.twig', array(
		'page' => $page
	));
})	->bind('page_delete')
	->method('POST|GET')
	->assert('wikiPage', '.+');

/** Render content for preview
 *
 */
$app->get('/preview/{mode}', function($mode) use ($app)
{
	$mdownParser = new MarkdownExtraParser();
	$content = $mdownParser->transformMarkdown( $app['request']->request->get('content') );
	
	if( $mode == 'raw' )
		$content = nl2br( strip_tags($content) );
	
	return $content;
	
})	->bind('preview')
	->method('POST');

/** Add Page to favourites
 *
 */
$app->get('/fav/{wikiPage}', function($wikiPage) use ($app)
{
	/* Not yet implemented */
	//$fs = new Filesystem();
	//$fs->symlink('/path/to/source', '/path/to/destination');
	
	return $app->redirect($app['url_generator']->generate('page', array('wikiPage' => $wikiPage)));
})	->bind('page_fav')
	->assert('wikiPage', '.+');

/** View Page
 *  Needs to be the last controller, otherwise the creation of pages is broken atm.
 */
$app->get('/{wikiPage}', function($wikiPage) use ($app)
{	
	$pageService = $app['service.page'];
	$page = $pageService($wikiPage);
	
	if ($page->exists) {
		$mdownParser = new MarkdownExtraParser();
		$parsedContent = $mdownParser->transformMarkdown($page->content);

		return $app['twig']->render('wiki_content.html.twig', array(
			'page' => $page,
			'parsedContent' => $parsedContent
		));
	} else {
		return $app->redirect($app['url_generator']->generate('page_edit', array('wikiPage' => $wikiPage)));
	}
})
	->value('wikiPage', 'index')
	->bind('page')
	->assert('wikiPage', '.+');

return $app;