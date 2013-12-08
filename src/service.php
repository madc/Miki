<?php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$app['service.page'] = $app->protect(function ($wikiPage) use ($app) {
	$fs = new Filesystem();
	
	$categories = explode('/', $wikiPage);
	$pageName = array_pop($categories);
	
	$page = new stdClass;
	$page->name = ucfirst($pageName);
	$page->categories = $categories;
	$page->url = $wikiPage;
	$page->file = $wikiPage.'.md';
	$page->path = implode('/', $categories);
	$page->exists = false;
	
	if ($fs->exists($app['wiki.path'].$page->file)) {
		$page->exists = true;
		$page->filesize = filesize($app['wiki.path'].$page->file);
		$page->modified = filemtime($app['wiki.path'].$page->file);	
		
		$fh = fopen($app['wiki.path'].$page->file, 'r');
		$page->content = fread($fh, $page->filesize);
		fclose($fh);
		
		$finder = new Finder();
		$files = $finder
			->files()
			->in($app['wiki.path'].$page->path)
			->depth(0)
			->name('*.md');

		if (count($files) > 1) {
			$page->siblings = array();
			
			foreach ($files as $file)
			{
				if (($page->path ? $page->path.'/' : '').$file->getRelativePathname() != $page->file) {
					$page->siblings[] = (object)[
  					  'name' => ucfirst(substr($file->getRelativePathname(), 0, -3)),
  					  'url' => ($page->path ? $page->path.'/' : '').substr($file->getRelativePathname(), 0, -3)
					];
				}
			}
		}		
	}
	
	return $page;
});