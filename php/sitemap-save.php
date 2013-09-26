<?php

/*

 Saves the sitemap.xml from the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html

*/
    
    $action = $_REQUEST['action'];
 
    switch($action) {

        case "start":
        
            $siteconfig = $_REQUEST["siteconfig"];   
            
            require_once "config-read.php";
            
            $directory_base = $varray["directory_base"];
            $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
            $directory_data = $directory_onsite . "/" . $varray["directory_data"];
            $directory_sitemap = $directory_onsite . "/" . $varray["directory_sitemap"];
            
            $filename_sitemap = $directory_sitemap . "/" . $varray["filename_sitemap"];
            $urllist_sitemap = $directory_sitemap . "/" . $varray["urllist_sitemap"];
            $filelist_included = $directory_data . "/" . $varray["filelist_included"]; 
              
            $xmlhead = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

            $hxfs = fopen($filename_sitemap, "w");
            $hxfu = fopen($urllist_sitemap, "w");    
            $hxfl = fopen($filelist_included, "w");    

            fwrite($hxfs, $xmlhead . "\n");
            
            fclose($hxfs);
            fclose($hxfu);
            fclose($hxfl); 

            break;
            
            
       case "end":
        
            $xmlfoot = '</urlset>';
            $xfs = $_REQUEST['xfs']; 
            $hxfs = fopen($xfs, "a");
            fwrite($hxfs, $xmlfoot);
            fclose($hxfs);
  
            break;
            
            
        default :            
 
            $xfs = $_REQUEST['xfs']; 
            $xfu = $_REQUEST['xfu'];  
            $xfl = $_REQUEST['xfl'];
 
            $xdn = $_REQUEST['xdn']; 
            $xdr = $_REQUEST['xdr']; 
  
            $smu = $_REQUEST['smu']; 
            $sml = $_REQUEST['sml'];  
            $ssf = $_REQUEST['ssf']; 
            $ssp = $_REQUEST['ssp']; 
 
            $inclist = $xdr . $smu;

            $hxfs = fopen($xfs, "a");
            $hxfu = fopen($xfu, "a");
            $hxfl = fopen($xfl, "a");
 
            if(is_file($inclist)){ 
 
                $ftime = filemtime($inclist);
                $sml = date("Y-m-d", $ftime) . "T" . date("H:i:s", $ftime) . "Z";
                
                $urllistall = $xdn . $smu . 
                ' priority=' . $ssp  . 
                ' changefreq=' . $ssf . 
                ' lastmod=' . $sml;
                
                $sitemap = " <url>\n  <loc>" . $xdn . $smu . 
                "</loc>\n  <priority>" . $ssp  . 
                "</priority>\n  <changefreq>" . $ssf . 
                "</changefreq>\n  <lastmod>" . $sml . 
                "</lastmod>\n </url>";
                
 
                fwrite($hxfs, $sitemap . "\n");
                fwrite($hxfu, $urllistall . "\n");    
                fwrite($hxfl, $inclist . "\n");
               
            }

            fclose($hxfs);
            fclose($hxfu);
            fclose($hxfl); 
            
            break;
            
    } 
 
?>