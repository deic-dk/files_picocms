<?php 


if(!empty($_GET['site_name'])){
	// If redirected by mod_rewrite with site_name set, serve the site
	require_once('apps/files_picocms/lib/Pico.php');
	// instantiate Pico
	$pico = new Pico(
			__DIR__,    // root dir
			'config/',  // config dir
			'plugins/', // plugins dir
			'themes/'   // themes dir
	);
	
	// override configuration?
	//$pico->setConfig(array());
	
	// run application
	echo $pico->run();
}

