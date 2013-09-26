<?php

/*

 Displays configuration variables on the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html

*/
    require_once "config-read.php";
   
    switch($siteconfig) {

        case "new":
            $filename_config = $directory_onsite . "/config.init"; 
            break;
        case "null":
            echo "Please select a valid configuration file";
            return 0;
            break;
        default :
            $filename_config = $directory_config . "/" . $siteconfig;
            break;
    }
    
      
    $cfilehandle = fopen($filename_config, "r");
     
    $n = 0;

    echo '<form action="config-edit.php" method="post" name="configform" id="configform" class="displayform" >';

 	while (!feof($cfilehandle)) {
   
        $configrow = fgets($cfilehandle, 4096);
              
        $configrow = trim($configrow);

        $exportexists = strncmp($configrow, "export", 1);
        
        if( $exportexists == 0) { 
                    
            $eqlen = strpos($configrow, "="); 

        	$varname = substr($configrow, 7, $eqlen-7);
		
            $varvalue = substr($configrow, ++$eqlen);
                       
            echo '<div class="cid"><span class="ctag" id="ctag' . $n . '">export ' . $varname . '=</span><span class="ctag"><input type="text" size="55" maxlength="254" name="cinp' . $n . '" class="cinp" value="' . $varvalue . '" /></span><span class="hidden" id="' . $varname . '">' . $n . '</span></div>';
        
        } else {

            echo '<div class="ctd"><span class="ctag" id="ctag' . $n . '">' . $configrow  . '&nbsp;</span></div>';
        }
        
        $n++;

    }
    
	fclose($cfilehandle);
    
    echo '<div class="fme">
    <input type="button" name="genbutton" class="fmb" id="genbutton" value="Generate Data"  onclick="generateData();"/>&nbsp;   
    <input type="button" name="configbutton" class="fmb" id="configbutton" value="Save Settings"  onclick="configSave();"/>
    </div>';
    
 

?>