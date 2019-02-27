<?php

/**
 *
 * @author Pontus Horn
 * @link https://pontushorn.me
 * @repository https://github.com/PontusHorn/Pico-Search
 * @license http://opensource.org/licenses/MIT
 */

class PicoSearch extends AbstractPicoPlugin
{

    private $search_area;
    private $search_terms;
    private $search_fields;
    public $has_search_page;

    /**
     * Parses the requested URL to determine if a search has been requested. The search may be
     * scoped to a folder. An example URL: yourdomain.com/blog/search/foobar/page/2,
     * which searches the /blog folder for "foobar" and shows the second page of results using
     * e.g. https://github.com/rewdy/Pico-Pagination.
     *
     * @see    Pico::getBaseUrl()
     * @see    Pico::getRequestUrl()
     * @param  string &$url request URL
     * @return void
     */
    public function onRequestUrl(&$url)
    {
    		$pico = $this->getPico();
    		$this->has_search_page = file_exists($pico->getConfig('content_dir').'/search.md');

    		// If form was submitted without being intercepted by JavaScript, redirect to the canonical search URL.
        if (preg_match('~^(.+/)?search$~', $url) && !empty($_GET['q'])) {
            header('Location: ' . $this->getPico()->getBaseUrl() . $url . '/' . urlencode($_GET['q']));
            exit;
        }

        if (preg_match('~^(.+/)?search/([^/]+)(/.+)?$~', $url, $matches)) {
            $this->search_terms = urldecode($matches[2]);

            if (!empty($matches[1])) {
                $this->search_area = $matches[1];
            }
        }
    }
    
    public function onPageRendering(&$twig, &$twigVariables, &$templateName)
    {
    	if ($this->has_search_page){
    		$twigVariables['has_search_page'] = $this->has_search_page;
    	}
    }

    /**
     * If accessing search results, {@link Pico::discoverRequestFile()} will have failed since
     * the search terms are included in the URL but do not map to a file. This method takes care
     * of finding the appropriate file.
     *
     * @see    Pico::discoverRequestFile()
     * @param  string &$file request file
     * @return void
     */
    public function onRequestFile(&$file)
    {
        if ($this->search_terms) {
            $pico = $this->getPico();

            // Aggressively strip out any ./ or ../ parts from the search area before using it
            // as the folder to look in. Should already be taken care of previously, but just
            // as a safeguard to make sure nothing slips through the cracks.
            if ($this->search_area) {
                $folder = str_replace('\\', '/', $this->search_area);
                $folder = preg_replace('~\b../~', '', $folder);
                $folder = preg_replace('~\b./~', '', $folder);
            }

            $temp_file = $pico->getConfig('content_dir') . (!empty($folder) ?: '') . 'search' . $pico->getConfig('content_ext');
            if (file_exists($temp_file)) {
                $file = $temp_file;
            }
        }
    }

    /**
     * If accessing search results, filter the $pages array to pages matching the search terms.
     *
     * @see    Pico::getPages()
     * @see    Pico::getCurrentPage()
     * @see    Pico::getPreviousPage()
     * @see    Pico::getNextPage()
     * @param  array &$pages        data of all known pages
     * @param  array &$currentPage  data of the page being served
     * @param  array &$previousPage data of the previous page
     * @param  array &$nextPage     data of the next page
     * @return void
     */
    public function onPagesLoaded(&$pages, &$currentPage, &$previousPage, &$nextPage)
    {
        if($currentPage['id']=='search' && empty($this->search_terms)){
        	$pages = array();
        	return;
        }
	    	$pico = $this->getPico();
	    	$excludes = array('403', '404', 'search');
	    	$confExcludes = $pico->getConfig('search_excludes');
	    	if (!empty($confExcludes)) {
	    		$excludes = array_merge($excludes, $confExcludes);
	    	}
	    	if (!empty($excludes)) {
	    		foreach ($excludes as $exclude_path) {
	    			unset($pages[$exclude_path]);
	    		}
	    	}

        if ($currentPage && isset($this->search_area) || isset($this->search_terms)) {
            if (isset($this->search_area)) {
                $pages = array_filter($pages, function ($page) {
                    return substr($page['id'], 0, strlen($this->search_area)) === $this->search_area;
                });
            }


            if (isset($this->search_terms)) {
            		// Search only page field specified by field:search_string, e.g. author:test@user.org
            		if(preg_match('~([^:]+):([^:]+)~', $this->search_terms, $this->search_fields)){
            			$pages = array_filter($pages, function ($page) {
            				return (stripos($page[$this->search_fields[1]], $this->search_fields[2]) !== false);
            			});
            		}
            		else{
            			$pages = array_filter($pages, function ($page) {
            				return (stripos($page['title'], $this->search_terms) !== false)
            				|| (stripos($page['raw_content'], $this->search_terms) !== false);
            			});
            		}
            }
        }
    }
}
