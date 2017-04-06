<?php 

require_once __DIR__ . '/../../lib/base.php';
require_once('apps/chooser/appinfo/apache_note_user.php');


if(empty($_GET['site'])){
	exit;
}

\OCP\Util::writeLog('files_picocms', 'Firing up '.$_GET['site'].":".$_SERVER['QUERY_STRING'], \OCP\Util::WARN);

// If redirected by mod_rewrite with site_name set, serve the site	
require_once('apps/files_picocms/3rdparty/symfony/component/yaml/Parser.php');
require_once('apps/files_picocms/3rdparty/symfony/component/yaml/Inline.php');
require_once('apps/files_picocms/3rdparty/symfony/component/yaml/Yaml.php');
require_once('apps/files_picocms/3rdparty/symfony/component/yaml/Exception/ExceptionInterface.php');
require_once('apps/files_picocms/3rdparty/symfony/component/yaml/Exception/RuntimeException.php');
foreach(glob(__DIR__ . "/3rdparty/symfony/component/yaml/Exception/*.php") as $filename){
	require_once($filename);
}

require_once('apps/files_picocms/3rdparty/Parsedown.php');
require_once('apps/files_picocms/3rdparty/ParsedownExtra.php');

foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/*Interface.php") as $filename){
	require_once($filename);
}

require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Autoloader.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Loader/Filesystem.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Environment.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Cache/Null.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Extension.php');
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/Extension/*.php") as $filename){
	require_once($filename);
}
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/SimpleFilter.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Lexer.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParserBroker.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/SimpleFunction.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/SimpleTest.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/BaseNodeVisitor.php');

require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/For.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/If.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Extends.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Include.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Block.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Use.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Filter.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Macro.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Import.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/From.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Set.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Spaceless.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Flush.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/TokenParser/Do.php');
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/TokenParser/*.php") as $filename){
	require_once($filename);
}
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node/Include.php');
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/Node/*.php") as $filename){
	require_once($filename);
}
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node/Expression/Name.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node/Expression/Binary.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node/Expression/Call.php');
require_once('apps/files_picocms/3rdparty/Twig/lib/Twig/Node/Expression/Filter.php');
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/Node/*/*.php") as $filename){
	require_once($filename);
}
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/Node/*/*/*.php") as $filename){
	require_once($filename);
}

foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/NodeVisitor/*.php") as $filename){
	require_once($filename);
}
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/*.php") as $filename){
	require_once($filename);
}
foreach(glob(__DIR__ . "/3rdparty/Twig/lib/Twig/Error/*.php") as $filename){
	require_once($filename);
}

require_once('apps/files_picocms/3rdparty/Pico/lib/Pico.php');
require_once('apps/files_picocms/3rdparty/Pico/lib/PicoPluginInterface.php');
require_once('apps/files_picocms/3rdparty/Pico/lib/AbstractPicoPlugin.php');
require_once('apps/files_picocms/3rdparty/Pico/lib/PicoTwigExtension.php');

require_once('apps/files_picocms/lib/OC_Pico.php');

if(!empty($_GET['path'])){
	$_SERVER['QUERY_STRING'] = $_GET['path'];
}

$siteInfo = OCA\FilesPicoCMS\Lib::lookupSiteInfo($_GET['site']);
$sitePath = $siteInfo['uid'].
	(empty($siteInfo['gid'])?'/files':'/user_group_admin/'.$siteInfo['gid']).
		$siteInfo['path'];
$dataDir = \OC_Config::getValue("datadirectory", \OC::$SERVERROOT . "/data");

if(empty($dataDir)){
	exit;
}

$config['group'] = $siteInfo['gid'];
$config['user'] = $siteInfo['uid'];
$config['rewrite_url'] = true;

$config['site_title'] = $_GET['site'];
$config['base_url'] = "https://".$_SERVER['HTTP_HOST'].\OC::$WEBROOT."/sites/".$_GET['site'];
/*$config['master_url'] = $_SERVER['HTTP_HOST'];

if(\OCP\App::isEnabled('files_sharding') ){
	$config['master_url'] = \OCA\FilesSharding\Lib::getMasterURL();
}*/

$extension = pathinfo($_GET['path'], PATHINFO_EXTENSION);
if(!empty($extension) && ($extension=='png'||$extension=='jpg'||$extension=='svg')){
	header("Content-type: image/".$extension);
}
if(!empty($extension) && ($extension=='pdf')){
	header("Content-type: application/".$extension);
	echo file_get_contents($dataDir.'/'.$sitePath.'/content/'.$_GET['path']);
	exit;
}

if(is_dir($dataDir.'/'.$sitePath.'/themes')){
	$themesDir = $dataDir.'/'.$sitePath.'/themes/';
	//$config['themes_url'] = "https://".$_SERVER['HTTP_HOST'].\OC::$WEBROOT .
	//	"/sites/".$_GET['site']."/themes";
	if(isset($_GET['path']) && strpos($_GET['path'], 'themes/')===0){
		\OCP\Util::writeLog('files_picocms', 'Serving '.$dataDir.'/'.$sitePath.'/'.$_GET['path'], \OC_Log::WARN);
		if(!empty($extension)){
			if($extension=='js'){
				header("Content-type: application/javascript");
			}
			else{
				header("Content-type: text/".$extension);
			}
		}
		echo file_get_contents($dataDir.'/'.$sitePath.'/'.$_GET['path']);
		exit;
	}
}
elseif(is_dir($dataDir.'/'.$sitePath)){
	$themesDir = __DIR__ . '/lib/samplesite/themes/';
	$config['themes_url'] = "https://".$_SERVER['HTTP_HOST'].\OC::$WEBROOT .
		"/apps/files_picocms/lib/samplesite/themes";
}
else{
	echo("Site does not exist");
	exit;
}

\OCP\Util::writeLog('files_picocms', 'Themes dir: '.$themesDir, \OC_Log::WARN);

if(empty($sitePath) ||
		(!file_exists($dataDir.'/'.$sitePath.'/content/index.md') &&
				!file_exists($dataDir.'/'.$sitePath.'/content/index.html'))){
	$config['content_dir'] = __DIR__ . '/lib/samplesite/content';
}
else{
	$config['content_dir'] = $dataDir.'/'.$sitePath.'/content';
}

// requesttoken is needed to get avatars
$config['requesttoken'] = \OC::$session->get('requesttoken');

// This is for the clean-blog theme
$config['pages_order_by'] = 'date';
//$config['pages_order'] = 'asc';
$config['pagination'] = -1;
$config['pagination_limit'] = 100;

\OCP\Util::writeLog('files_picocms', 'Content dir: '.$config['content_dir'], \OC_Log::WARN);

// instantiate Pico
$pico = new Pico(
		__DIR__ . '/3rdparty/Pico',    // root dir
		'config/',  // config dir
		'plugins/', // plugins dir
		$themesDir   // themes dir
);

// override configuration?
$pico->setConfig($config);

// run application
echo $pico->run();


