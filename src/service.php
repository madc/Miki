<?php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use dflydev\markdown\MarkdownExtraParser;

/** Page Service
 *  Retrieve all infos of a given page
 */
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

/** Parsing Service
 *  Returns the parsed content.
 */
$app['service.parse'] = $app->protect(function ($content, $advancedParser=false) use ($app) {
	$mdownParser = new MarkdownExtraParser();
	$parsedContent = $mdownParser->transformMarkdown($content);
	
	if($advancedParser) {
		// Parse non-existant links (ignore extneral urls )
		$parsedContent = preg_replace_callback('/href=[\'"](?!ftp|http[s]?:\/\/)([^\'"]*)[\'"]/i', function ($str) use ($app) {			
			$fs = new Filesystem();
			if(!$fs->exists($app['wiki.path'].$str[1].'.md')) {
			    return str_replace('href', 'class="missing-page" href', $str[0]);
			}
		
			return $str[0];
		}, $parsedContent);
	}

	return $parsedContent;
});