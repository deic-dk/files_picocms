<?php
/**
 * Pico Pagination Plugin
 *
 * @author Andrew Meyer
 * @link http://rewdy.com
 * @license http://opensource.org/licenses/MIT
 * @version 1.4
 */
class Pagination extends AbstractPicoPlugin {
	
	public $config = array();
	public $offset = 0;
	public $page_number = 0;
	public $total_pages = 1;
	public $found_pages = array();
	public $paged_pages = array();
	public $found_folders = array();
	//public $contents = array();
	public $labels = array();
	
	public function __construct(Pico $pico)
	{
		parent::__construct($pico);

		$this->config = array(
			'limit' => 5,
			'next_text' => 'Next &gt;',
			'prev_text' => '&lt; Previous',
			'page_indicator' => 'page',
			'output_format'	=> 'links',
			'flip_links' => false,
			'filter_date' => true,
			'sub_page' => false,
			'pico' => $pico,
			'exclude_labels' => []
		);
	}

	public function onConfigLoaded(&$settings)
	{
		// Pull config options for site config
		if (isset($settings['pagination_limit']))
			$this->config['limit'] = $settings['pagination_limit'];
		if (isset($settings['pagination_folder']))
				$this->config['folder'] = $settings['pagination_folder'];
		if (isset($settings['pagination_template']))
			$this->config['template'] = $settings['pagination_template'];
		if (isset($settings['pagination_next_text']))
			$this->config['next_text'] = $settings['pagination_next_text'];
		if (isset($settings['pagination_prev_text']))
			$this->config['prev_text'] = $settings['pagination_prev_text'];
		if (isset($settings['pagination_flip_links']))
			$this->config['flip_links'] = $settings['pagination_flip_links'];
		if (isset($settings['pagination_filter_date']))
			$this->config['filter_date'] = $settings['pagination_filter_date'];
		if (isset($settings['pagination_page_indicator']))
			$this->config['page_indicator'] = $settings['pagination_page_indicator'];
		if (isset($settings['pagination_output_format']))
			$this->config['output_format'] = $settings['pagination_output_format'];
		if (isset($settings['pagination_sub_page']))
			$this->config['sub_page'] = $settings['pagination_sub_page'];
		if (isset($settings['content_dir']))
				$this->config['content_dir'] = $settings['content_dir'];
	}

	public function onPagesLoaded(&$pages, &$currentPage, &$previousPage, &$nextPage)
	{
		// Honor pagination_ settings in metadata
		if (isset($currentPage['meta']['paginationfolder'])){
			$this->config['folder'] = $currentPage['meta']['paginationfolder'];
		}
		if (isset($currentPage['meta']['paginationtemplate'])){
			$this->config['template'] = $currentPage['meta']['paginationtemplate'];
		}
		if (isset($currentPage['meta']['paginationlimit'])){
			$this->config['limit'] = $currentPage['meta']['paginationlimit'];
		}
		if (isset($currentPage['meta']['paginationnexttext'])){
			$this->config['next_text'] = $currentPage['meta']['paginationnexttext'];
		}
		if (isset($currentPage['meta']['paginationprevioustext'])){
			$this->config['prev_text'] = $currentPage['meta']['paginationprevioustext'];
		}
		if (isset($currentPage['meta']['paginationfliplinks'])){
			$this->config['flip_links'] = ($currentPage['meta']['paginationfliplinks']=='true' ||
																		$currentPage['meta']['paginationfliplinks']=='yes');
		}
		if (isset($currentPage['meta']['excludelabels'])){
			$this->config['exclude_labels'] = $currentPage['meta']['excludelabels'];
		}
		// Filter the pages returned based on the pagination options
		$this->offset = ($this->page_number-1) * $this->config['limit'];
		$show_folders = array();
		// For a generated index there is no currentPage
		//$path = $this->config['content_dir']."/".$currentPage['folder'];
		/*$path = $this->config['content_dir']."/".$this->config['pico']->getFolder();
		$contents = array_diff(scandir($path),
				array(".", "..", "index.md", "403.md", "404.md", "rss.md", ".DS_Store"));
		$contents = array_map(function($name) use ($path) {return $name.(is_dir($path."/".$name)?"/":"");}, $contents);*/

		$show_pages = array();
		foreach($pages as $key=>$page) {
			// If filter_date is true, return only dated items.
			if($this->config['filter_date'] && !$page['date']){
				continue;
			}
			if(!empty($this->config['folder']) && !empty($page['folder']) &&
					strpos(trim($page['folder'], '/'), trim($this->config['folder'], '/'))!==0){
						\OCP\Util::writeLog('files_picocms', "Not in folder: ".$page['filename'].":".$page['folder'].":".$this->config['folder'], \OC_Log::INFO);
				continue;
			}
			if(!empty($this->config['template']) && !empty($page['meta']['template']) &&
					$this->config['template']!=$page['meta']['template']){
				continue;
			}
			// if the calling page has ExcludeLabels set, return only pages w/o those
			// Allow override by $_GET['labels']
			if (!empty($this->config['exclude_labels']) && !empty($page['meta']['labels']) &&
					!empty(array_intersect($this->config['exclude_labels'], $page['meta']['labels'])) &&
					(empty($_GET['label']) || !in_array($_GET['label'], $page['meta']['labels']))){
				continue;
			}
			if(!empty($_GET['label']) && ( empty($page['meta']['labels']) ||
					!in_array($_GET['label'], $page['meta']['labels']))){
				continue;
			}
			if(!$page['readable']){
				continue;
			}
			$show_pages[$key] = $page;
		}

		foreach($pages as $key=>$page) {
			if(!empty($page['folder']) && !in_array($page['folder'], $show_folders)){
				$show_folders[] = $page['folder'];
				$subfolders = explode('/', $page['folder']);
				$fullSubfolder = '';
				foreach($subfolders as $subfolder){
					$fullSubfolder = trim($fullSubfolder.$subfolder, '/').'/';
					if(!empty($fullSubfolder) && !in_array($fullSubfolder, $show_folders)){
						$show_folders[] = $fullSubfolder;
					}
				}
			}
			if(!empty($page['meta']['labels'])){
				\OCP\Util::writeLog('files_picocms', "Raw labels: ".serialize($page['meta']['labels']), \OC_Log::DEBUG);
				$this->labels = array_unique(array_merge($this->labels,
						is_array($page['meta']['labels'])?$page['meta']['labels']:array_map('trim', explode(",", $page['meta']['labels']))));
			}
		}
		\OCP\Util::writeLog('files_picocms', "Folders: ".implode(":", $show_folders), \OC_Log::WARN);
		// get total pages before show_pages is sliced
		$this->total_pages = ceil(count($show_pages) / $this->config['limit']);
		// slice show_pages to the limit
		$show_pages = array_reverse($show_pages);
		$this->found_pages = $show_pages;
		$show_pages = array_slice($show_pages, $this->offset, $this->config['limit']);
		// set filtered pages to paged_pages
		$this->paged_pages = $show_pages;
		$this->found_folders = $show_folders;
		//$this->contents = $contents;
	}

	public function onPageRendering(&$twig, &$twigVariables, &$templateName)
	{
		// Set a bunch of view vars

		if($this->config['pico']->forbidden){
			$twigVariables['paged_pages'] = array();
			$twigVariables['found_folders'] = array();
			$twigVariables['labels'] = array();
		}
		
		// send the paged pages in separate var
		elseif ($this->paged_pages){
			$twigVariables['found_pages'] = $this->found_pages;
			$twigVariables['paged_pages'] = $this->paged_pages;
			$twigVariables['found_folders'] = $this->found_folders;
			$twigVariables['contents'] = $this->contents;
			$twigVariables['labels'] = empty($this->labels)?array():$this->labels;
		}
		
		\OCP\Util::writeLog('files_picocms', "Labels: ".implode(":", $twigVariables['labels']), \OC_Log::WARN);
		
		// set var for page_number
		if ($this->page_number)
			$twigVariables['page_number'] = $this->page_number;

		// set var for total pages
		if ($this->total_pages)
			$twigVariables['total_pages'] = $this->total_pages;

		// set var for page_indicator
		$twigVariables['page_indicator'] = $this->config['page_indicator'];
		
		// build pagination links
		// set next and back link vars to empty. links will be added below if they are available.
		$twigVariables['next_page_link'] = '';
		$pagination_parts = array('prev_link'=>'', 'next_link'=>'');
		list($path, $qs) = explode("?", $_SERVER["REQUEST_URI"], 2);
		if(empty($qs)){
			$qs = '';
		}
		else{
			$qs = '?'.$qs;
		}
		$page_base = preg_replace('|'.$this->config['page_indicator'].'/[0-9]+/*$|', '', $path);
		if ($this->page_number > 1) {
			$prev_path = $page_base . $this->config['page_indicator'] . '/' . ($this->page_number - 1);
			$pagination_parts['prev_link'] = '<a href="' . $prev_path . $qs. '" id="prev_page_link">' . $this->config['prev_text'] . '</a>';
		}
		if ($this->page_number < $this->total_pages) {
			$next_path = $page_base . $this->config['page_indicator'] . '/' . ($this->page_number + 1);
			$pagination_parts['next_link'] = '<a href="' . $next_path . $qs .'" id="next_page_link">' . $this->config['next_text'] . '</a>';
		}

		// reverse order if flip_links is on
		if ($this->config['flip_links']) {
			$pagination_parts = array_reverse($pagination_parts);
		}

		// create pagination links output
		if ($this->config['output_format'] == "list") {
            $twigVariables['paginationlinks'] = '<ul id="pagination"><li>' . implode('</li><li>', array_values($pagination_parts)) . '</li></ul>';
		} else {
			$twigVariables['paginationlinks'] = array_values($pagination_parts);
		}

		// set page of page var
    $twigVariables['page_of_page'] = "Page " . $this->page_number . " of " . $this->total_pages . ".";
	}

	public function onRequestUrl(&$url)
	{
		// checks for page # in URL
		$pattern = '/' . $this->config['page_indicator'] . '\/[0-9]*/';
		if (preg_match($pattern, $url)) {
			$page_numbers = explode('/', $url);
			$page_number = $page_numbers[count($page_numbers)-1];
			$this->page_number = $page_number;
			if ($this->config['sub_page']) {
				$url = $this->config['page_indicator'];
			} else {
				$url = preg_replace($pattern, '', $url);
			}
		} else {
			$this->page_number = 1;
		}
		\OCP\Util::writeLog('files_picocms', 'URL: '.$url.':'.$this->config['sub_page'], \OC_Log::WARN);
	}
}