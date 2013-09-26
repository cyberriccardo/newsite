<?php

/*

 Saves the file tree view on the onSite user interface.

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
            $filelist_excluded = $directory_data . "/" . $varray["filelist_excluded"]; 
            $xfe = fopen($filelist_excluded, "w");
            fclose($xfe); 
            break;
            
            
        default :            
 
            $filelist_excluded = $_REQUEST['fn'];
            $xfe = fopen($filelist_excluded, "a"); 
            $lfc = $_REQUEST['lfc']; 
            if(is_file($lfc)) fwrite($xfe, $lfc . "\n"); 
            fclose($xfe); 

            break;
            
    } 
		
?>