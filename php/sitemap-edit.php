<?php

/*

 Reads and parses sitemap.xml for the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html


 Complies with the XML schema for sitemap files
 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd
 as defined by sitemap.org
    
*/

// Retrieve the configuration variables

    require_once "config-read.php";
    
    $directory_base = $varray["directory_base"];    
    $directory_onsite = $directory_base . "/" . $varray["directory_onsite"];    
    $directory_sitemap = $directory_onsite . "/" . $varray["directory_sitemap"]; 
    $filename_sitemap = $directory_sitemap . "/" . $varray["filename_sitemap"];
    
    $directory_data = $directory_onsite . "/" . $varray["directory_data"]; 
    $filelist_excluded = $directory_data . "/" . $varray["filelist_excluded"];
    
    if(file_exists($filename_sitemap)===false) return;
    
    $smp = new displayURL;

    $smp->standard_frequency = $varray["standard_frequency"];
    $smp->standard_priority = $varray["standard_priority"];
    
    // $smp->apriority = array("0.0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1.0");
    // $smp->achangefreq = array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");

    $smp->domain_url = $varray["domain_url"]; 
    $smp->directory_root = $varray["directory_root"];  


    if(file_exists($filelist_excluded)!==false){
        $smp->excluded = file($filelist_excluded);
    }else{
        $smp->excluded = array();
    }


// Parse the sitemap 

    $xmlLint = '';
   
    $xmlLint = file_get_contents($filename_sitemap);

    while(strpos($xmlLint,"<url>")!==false){
    
        $url_item = substr($xmlLint,
        strpos($xmlLint,"<url>")+strlen("<url>"), 
        strpos($xmlLint,"</url>")-strlen("<url>")
        -strpos($xmlLint,"<url>"));
         
        $smp->parseItemContents($url_item);

        $nxtstrpos = strpos($xmlLint,"</url>")+strlen("</url>");
        
        $xmlLint = substr($xmlLint, $nxtstrpos);

    }



class displayURL {

    var $aitems;
    var $objsel;
   
    var $domain_url;  
    var $directory_root;   
    // var $achangefreq;    
    // var $apriority;
    var $standard_priority;
    var $standard_frequency; 
    
    var $excluded;


    function parseItemContents($itemcontents){
    
        $rflag = 0;
 
        $smu = substr($itemcontents,
        strpos($itemcontents,"<loc>")+strlen("<loc>"), 
        strpos($itemcontents,"</loc>")-strlen("<loc>")
        -strpos($itemcontents,"<loc>"));

        $smu = str_replace($this->domain_url, "", $smu);
        
        if (in_array($this->directory_root . $smu, $this->excluded)) $rflag = 1;

        if(file_exists($this->directory_root . $smu)!==false) {
        
            $ftime = filemtime($this->directory_root . $smu);
            $sml = date("Y-m-d", $ftime) . "T" . date("H:i:s", $ftime) . "Z";
            
        }else{
            $sml =  "0000-00-00T00:00:00Z";
            $rflag = 1; 
        }

        if(strpos($itemcontents,"<changefreq>")!==false){
    
            $smf = substr($itemcontents,
            strpos($itemcontents,"<changefreq>")+strlen("<changefreq>"), 
            strpos($itemcontents,"</changefreq>")-strlen("<changefreq>")
            -strpos($itemcontents,"<changefreq>"));
      
        }else{
            $smf = $this->standard_frequency;
        }
        
    
        if(strpos($itemcontents,"<priority>")!==false){
    
            $smp = substr($itemcontents,
            strpos($itemcontents,"<priority>")+strlen("<priority>"), 
            strpos($itemcontents,"</priority>")-strlen("<priority>")
            -strpos($itemcontents,"<priority>"));
            
        }else{
            $smp = $this->standard_priority;
        }

        $aobj["smu"] = $smu;
        $aobj["sml"] = $sml;
        $aobj["smf"] = $smf;
        $aobj["smp"] = $smp;
        
        echo $smu . " " . $sml . " " . $smf . " " . $smp . " " . $rflag . ",";
        
        $this->aitems = $aobj;
    
    } 

    function objectDisplay($n, $sc){
        // This function is deprecated by client-side javascript.
        $fname = str_replace($this->directory_root, "", $this->aitems["smu"]);

        $line = '<div class="sc' . $sc  . '" id="smr' . $n . '"'; 
        $line .= ' onMouseOver="highlightRow(\'smr' . $n . '\', ' . $sc  . ', 2)"';  
        $line .= ' onMouseOut="highlightRow(\'smr' . $n . '\', ' . $sc  . ', 0)" >';

        $line .= '<span class="ibt">';
        $line .= '<input type="button" name="xon' . $n . '" value="&nbsp;"'; 
        $line .= ' class="xon" id="xon' . $n . '"'; 
        $line .= ' onclick="enableURL(' . $n . ');" /></span>';  
        $line .= '<span class="ibt">';
        $line .= '<input type="button" name="xoff' . $n . '" value="&nbsp;"'; 
        $line .= ' class="xoff" id="xoff' . $n . '" ';
        $line .= ' onclick="disableURL(' . $n . ');" /></span>';

        $line .= '<span class="spu" id="spu' . $n . '">';
        $line .= '<a href="' . $this->domain_url . $fname . '"'; 
        $line .= ' target="view" id="sma' . $n . '" >' . $fname . '</a>';
        $line .= '<input type="hidden" name="smu' . $n . '" value="' . $fname . '" />';
        $line .= '</span>';
    
        $line .= '<span class="hidden">';
        $line .= '<a name="' . $fname . '" id="' . $fname . '" class="">' . $n . '</a>';
        $line .= '<input type="checkbox" name="sfc'  . $n . '" value="' . $fname . '" checked="checked" />';
        $line .= '</span>';
         
        $line .= '<span class="spl" id="spl' . $n . '">';
        $line .= $this->aitems["sml"];
        $line .= '<input type="hidden" name="sml' . $n . '"  value="'. $this->aitems["sml"] . '" />';
        $line .= '</span><span class="spf" id="spf' . $n . '">';

        $this->objectSelectBox("ssf" . $n, $this->achangefreq, $this->aitems["smf"]);
        $line .= $this->objsel; 
        
        $line .=  '</span><span class="spp" id="spp' . $n . '">';
    
        $this->objectSelectBox("ssp" . $n, $this->apriority, $this->aitems["smp"]);
        $line .= $this->objsel;
        
        $line .=   '</span></div>'; 
        
        echo $line;
   
    }
    
    function objectSelectBox($objname, $aOptions, $selected){
        // This function is deprecated by client-side javascript.
        $shtml = '<select class="sms" name="' . $objname . '" >' ;
    
         foreach($aOptions as $optionvalue){
            $optionselected = '';
            if(strcmp(substr($selected, 0, 3), substr($optionvalue, 0, 3)) == 0) $optionselected = 'SELECTED';
            $shtml .=  '<option value="' . $optionvalue . '" ' . $optionselected . '>' . $optionvalue . '</option>';
        }
    
        $shtml .= '</select>';
        $this->objsel = $shtml;
 
    }
    
}

?>