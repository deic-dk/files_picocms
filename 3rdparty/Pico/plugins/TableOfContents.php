<?php

/**
 * @see README.mb for further details
 *
 * @package Pico
 * @subpackage TableOfContents
 * @history Originally mcb_TableOfContent - renamed and updated to API v1.
 * @version 0.2 alpha
 * @author mcbSolutions.at <dev@mcbsolutions.at>
 * @author frederik.orellana@deic.dk
 */
class TableOfContents extends AbstractPicoPlugin {

   // default settings
   private $depth = 3;
   private $min_headers = 3;
   private $top_txt = '&uarr;';
   private $caption = '';
   private $anchor = false;
   private $top_link;
   // internal
   private $toc = '';
   private $xpQuery;

   private function makeToc(&$content)
   {
      //get the headings
      if(preg_match_all('/<(h[1-9]|toc) *.*?>.*?<\/(h[1-'.$this->depth.']|toc)>/s',$content,$headers) === false)
         return "";

      //$this->depth
      //create the toc
       if(preg_match('/<(toc) *.*?>.*?<\/(toc)>/s', $content)){
      	foreach($headers[0] as $i=>$head){
      		unset($headers[0][$i]);
      		if(strpos($head, '<toc')!==false){
      			break;
      		}
      	}
      }

      $heads = implode("\n",$headers[0]);
      $heads = preg_replace('/<a.+?\/a>/','',$heads);
      $heads = preg_replace('/<h([1-6]) id="?/','<li class="toc$1"><a href="#',$heads);
      $heads = preg_replace('/<\/h[1-6]>/','</a></li>',$heads);

      $cap = $this->caption =='' ? "" :  '<p id="toc-header">'.$this->caption.'</p>';

      return '<div id="toc">'.$cap.'<ul>'.$heads.'</ul></div>';
   }

   public function onConfigLoaded(&$settings)
   {
      if(isset($settings['toc_depth'      ])) $this->depth       = &$settings['toc_depth'];
      if(isset($settings['toc_min_headers'])) $this->min_headers = &$settings['toc_min_headers'];
      if(isset($settings['toc_top_txt'    ])) $this->top_txt     = &$settings['toc_top_txt'];
      if(isset($settings['toc_caption'    ])) $this->caption     = &$settings['toc_caption'];
      if(isset($settings['toc_anchor'     ])) $this->anchor      = &$settings['toc_anchor'];
      if(isset($settings['top_link'       ])) $this->top_link    = &$settings['top_link'];

      for ($i=1; $i <= $this->depth; $i++) {
         $this->xpQuery[] = "//h$i";
      }
      $this->xpQuery = join("|", $this->xpQuery);

      $this->top_link = '<a href="#top" id="toc-nav">'.$this->top_txt.'</a>';
   }

   public function onContentParsed(&$content)
   {
      if(trim($content)=="")
        return;
      // Workaround from cbuckley:
      // "... an alternative is to prepend the HTML with an XML encoding declaration, provided that the
      // document doesn't already contain one:
      //
      // http://stackoverflow.com/questions/8218230/php-domdocument-loadhtml-not-encoding-utf-8-correctly
      $dom = new \DOMDocument();
      $html = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
      \OCP\Util::writeLog('files_picocms', 'CONTENT: '.$content, \OC_Log::INFO);
      $xp = new \DOMXPath($dom);

      $nodes =$xp->query($this->xpQuery);

      if($nodes->length < $this->min_headers)
         return;

      // add missing id's to the h tags
      $id = 0;
      foreach($nodes as $i => $sort)
      {
          if (isset($sort->tagName) && $sort->tagName !== '')
          {
             if($sort->getAttribute('id') === "")
             {
                ++$id;
                $sort->setAttribute('id', "toc_head$id");
             }
             if($i>0){
	             	$a = $dom->createElement('a', $this->top_txt);
	             	$a->setAttribute('href', '#top');
	             	$a->setAttribute('id', 'toc-nav');
             		$sort->appendChild($a);
             }
          }
      }
      // add top anchor
      if($this->anchor)
      {
         $body = $xp->query("//body/node()")->item(0);
         $a = $html->createElement('a');
         $a->setAttribute('name', 'top');
         $body->parentNode->insertBefore($a, $body);
      }

      $content = preg_replace(
                     array("/<(!DOCTYPE|\?xml).+?>/", "/<\/?(html|body)>/"),
                     array(                         "",                   ""),
                     $dom->saveHTML()
                              );

      $this->toc = $this->makeToc($content);
   }

   public function onPageRendering(&$twig, &$twig_vars, &$templateName)
   {
      $twig_vars['toc'] = $this->toc;
      $twig_vars['toc_top'] = $this->anchor ? "" : '<a id="top"></a>';
      $twig_vars['top_link'] = $this->top_link;
   }

   public function onContentPrepared(&$output)
   {
   	// Debugging
    //$output = $output . "<pre style=\"background-color:white;\">".htmlentities(print_r($this,1))."</pre>";
   }
}
