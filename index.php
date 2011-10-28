<?php

$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'docs';

if (isset($_REQUEST['doc']) && !empty($_REQUEST['doc'])) {

	$doc = $dir . '/' . $_REQUEST['doc'];

	if (($fullpath = realpath($doc)) && file_exists($fullpath) && stripos($fullpath, $dir, 0) === 0) {

		echo '<script type="text/javascript" src="libraries/jquery/jquery.js"></script>';
		echo '<link href="_assets/js/prettify/prettify.css" type="text/css" rel="stylesheet" />';
		echo '<script type="text/javascript" src="_assets/js/prettify/prettify.js"></script>';
		echo '<link rel="stylesheet" href="_assets/css/styles.css">';
		echo '<div class="box box-text documentation">';
		$output = file_get_contents($fullpath);
		$output = preg_replace('#src="\.\.\/\.\.\/\.\.\/#', 'src="', $output);
		echo $output;
		echo '</div>';
		echo '<script type="text/javascript">addEventListener("load", function (event) { prettyPrint() }, false);</script>';

	}

} else {

	$iterator = new RecursiveDirectoryIterator($dir);

	$renderer = function ($iterator) use (&$renderer, $dir) {
		$html = array();
		foreach ($iterator as $path) {
			if ($path->isDir()) {
				$html[] = "\n<li>";
				$html[] = $path->getBaseName();
				$html[] = '<ul>';
				$html[] = $renderer(new RecursiveDirectoryIterator($path));
				$html[] = '</ul>';
				$html[] = '</li>';
			} else {
				$link = ltrim(preg_replace('/^'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/').'/i', '', str_replace(DIRECTORY_SEPARATOR, '/', $path)), '/');
				$html[] = "\n<li>";
				$html[] = '<a href="index.php?doc='.urlencode($link).'">'.$path->getBaseName().'</a>';
				$html[] = '</li>';
			}
		}
		return implode("\n", $html);
	}

	?>

	<h1>Documentation</h1>

	<ul class="sections">

		<?php echo $renderer($iterator); ?>

	</ul>

<?php 

}