<?php

namespace Common;

/**
 * PageView generates HTML pages from title and body-texts
 * also handles charsets and stylesheets
 **/

class PageView {

  // Titles
  const TITLE_CREATE_NEW_LIST = 'Create new list';
  const NOT_LOGGED_IN = 'Please log in';
  const VIEW_LIST = 'List view';
  const SHOW_LIST = 'Show list';
  const LIST_SAVED = 'List was saved!';
  
  // Config for page
  private $m_cssFiles = array('style.css', 'jquery-ui-1.9.0.custom.css', 'jquery-ui-1.9.0.custom.min.css', 'tipsy.css');
  private $m_jsFiles= array('jquery-1.8.2.js', 'jquery-1.7.1.js', 'jquery-1.8.2.min.js', 'jquery-ui-1.9.0.custom.min.js', 'jquery-ui-1.9.0.custom.js', 'external.js', 'jquery.tipsy.js');

  // TODO: change to const?
  private $m_cssFolder = 'css';
  private $m_jsFolder = 'js';
  //const IMG_FOLDER = '/img';

  private $m_charset = 'utf-8';

  private $m_title = '';

  public function setTitle($title) {

    $this->m_title = $title;
  }
  
  /**
  * Adds a CSS stylesheet to the head of the document
  * @param urlstring $href url to css file
  **/
  public function AddStyleSheet() {

      foreach ($this->m_cssFiles as $cssFile) {
          $css .= "<link rel='stylesheet' href='$this->m_cssFolder/$cssFile' type='text/css' />\n";
      }

      return $css;
  }

  /**
  * Adds a CSS stylesheet to the head of the document
  * @param urlstring $href url to css file
  **/
  public function AddJavaScript() {

      foreach ($this->m_jsFiles as $jsFile) {
          $js .= "<script src='$this->m_jsFolder/$jsFile'></script>\n";
      }

      return $js;
  }
    
  /**
  * Returns a HTML 4.01 Transitional page
  * @param string $title  
  * @param string $body    
  * @return string     
  **/
  public function GetHTMLPage($header, $body) {
    
        $css = $this->AddStyleSheet();
        $js = $this->AddJavaScript();

        $title = $this->m_title;
        
        $html = "
              <!DOCTYPE HTML SYSTEM>
              <html>
                  <head>
                        <title>$title</title>
                        <meta http-equiv='content-type' content='text/html; charset=$this->m_charset'>
                        $css
                  </head>
                  <body>
                        <header>
                          $header
                        </header>
                        <div id='mainContainer'>
                          $body
                        </div>
                        <footer>
                        </footer>
                        $js
                  </body>
              </html>";
            
        return $html;
    }
}