<?php

/*

 Saves configuration variables defined in the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html

*/

    
    $action = $_REQUEST['action'];
 
    switch($action) {
    
        case "new":
        
            require_once "config-read.php";
        
            $newconfig = $_REQUEST["newconfig"];
            $directory_base = $varray["directory_base"];
            $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
            $directory_config = $directory_onsite . "/" . $varray["directory_config"];

            trim($newconfig);
            $newconfig = str_replace(" ", "", $newconfig);
  
            if(strrpos($newconfig, ".sh") === false) {
                $newconfig .= ".sh";
            }

            if(is_file($directory_config . "/" . $newconfig)) { echo "File already exists"; return; }

            // PHP5
            // file_put_contents($directory_config . "/" . $newconfig, file_get_contents($directory_onsite . "/config.init" ));

            $nfi = file_get_contents($directory_onsite . "/config.init");
            $nfh = fopen($directory_config . "/" . $newconfig, "w");
            fwrite($nfh, $nfi);
            fclose($nfh); 

            echo $newconfig;
            return;
            break;
            
        case "generate":
        
            $siteconfig = $_REQUEST["siteconfig"];   

            require_once "config-read.php";
            $directory_base = $varray["directory_base"];
            $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
            $directory_config = $directory_onsite . "/" . $varray["directory_config"];
            
            $gencom = $directory_onsite . "/" . "generate_filelists.sh " . $siteconfig . " " . $directory_onsite . "/env.sh";
            system($gencom, $retval);
            echo $retval;
            return;
            break;

        case "start":
        
            $siteconfig = $_REQUEST["siteconfig"];   
            
            require_once "config-read.php";
            
            $directory_base = $varray["directory_base"];
            $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
            $directory_config = $directory_onsite . "/" . $varray["directory_config"];
            $filename_config = $directory_config . "/" . $siteconfig; 

            $xfc = fopen($filename_config, "w");
            fclose($xfc); 

            break;

            
        default :    
        
            $filename_config = $_REQUEST["fn"]; 
            $xfc = fopen($filename_config, "a");    
  
            $cval = $_REQUEST["cval"];
            $cstr = str_replace("~", "#", $cval);
            fwrite($xfc, $cstr . "\n"); 
            fclose($xfc); 
            
            break;
            
    } 
		
?>