<?php


namespace Common;

/**
 * PageView generates HTML pages from title and body-texts
 * also handles charsets and stylesheets
 **/

class PageView {
  
  // Config for page
  private $m_cssFiles = array('style.css');
  private $m_jsFiles= array('jquery-1.8.2.min.js', 'external.js');

  // TODO: change to const?
  private $m_cssFolder = 'css';
  private $m_jsFolder = 'js';
  //const IMG_FOLDER = '/img';

  private $m_charset = 'utf-8';
  
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
  public function GetHTMLPage($title, $header, $body) {
    
        $css = $this->AddStyleSheet();
        $js = $this->AddJavaScript();
        
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
                        <div id='footer'>
                          footer
                        </div>
                        $js
                  </body>
              </html>";
            
        return $html;
    }
}