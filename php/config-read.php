<?

/*

 Reads and sets configuration variables on the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html


 Values for configuration varibles are saved in bash shell format.
 This php function reads the bash file and generates an associative
 array: $varray that stores the key/value combinations for the
 following configuration variable names:
 
     filename_config
     directory_root
     domain_url
     standard_priority
     standard_frequency
     file_types

     filelist_complete
     filelist_timestamp
     filelist_directorytree
     filelist_excluded
     filelist_included
     filelist_newfiles
     filelist_sitemap
     filename_sitemap

     directory_base
     directory_onsite
     directory_data
     directory_config
     directory_sitemap
     onsite_url
     directory_php
     directory_ajax
     directory_css
 
 

Usage: include "config-read.php";

Values can be specified as: $varray["<variable name>"]
i.e.: $directory_data = $varray["directory_data"];

The associative array ($varray) enables the configuration
settings to be modified to include additional variables.

*/


    $varray = array();
    

// Load the onSite environment

    $baseconfig = '../env.sh';
    
    if(is_file($baseconfig) == FALSE) { 
        echo "Initialization file not found"; 
        exit; 
    }
    
    readConfigFile($baseconfig);



// Load the sitemap variables

    $directory_base = $varray["directory_base"];
    
    $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 

    $directory_config = $directory_onsite . "/" . $varray["directory_config"];

    $siteconfig = $_REQUEST["siteconfig"];
   
    $filename_config = $directory_config . "/" . $siteconfig;
    
   
    if(is_file($filename_config) == FALSE) { $filename_config = $directory_onsite . "/config.init"; }
 
    readConfigFile($filename_config);




    function readConfigFile($cfilename) {
    
        global $varray;
       
        $cfilehandle = fopen($cfilename, "r");
        
        while (!feof($cfilehandle)) {
       
            $configrow = fgets($cfilehandle, 4096);
                   
            $hashexists = strncmp($configrow, '#', 1);
            
            if( $hashexists == 0)  continue;
            
            $configrow = str_replace("export", "", $configrow);
            
            $configrow = trim($configrow);
             
            $varlen = strpos($configrow, "=");
            
            if($varlen == false) continue;
    
            $varname = substr($configrow, 0, $varlen);
            
            $varvalue = substr($configrow, ++$varlen);
     
            $varray[$varname] = $varvalue;
    
        }
    
// Uncomment the following line to run this script in a standalone mode.    
//        foreach($varray as $key => $value) echo $key . ' ' . $value . '<br/>';
    
        fclose($cfilehandle);

    }
    


?>