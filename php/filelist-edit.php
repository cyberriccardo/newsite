<?php

/*

 Displays the file tree view on the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html

*/

// Retrieve the configuration variables

    require_once "config-read.php";
    
    $directory_base = $varray["directory_base"];
    $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
    $directory_data = $directory_onsite . "/" . $varray["directory_data"]; 
    $filelist_complete = $directory_data . "/" . $varray["filelist_complete"];
    $filelist_excluded = $directory_data . "/" . $varray["filelist_excluded"];
    $filelist_newfiles = $directory_data . "/" . $varray["filelist_newfiles"];
   
    $domain_url = $varray["domain_url"];
    $directory_root = $varray["directory_root"];
    
    $arrDirName = array();

// Read the source file list

	$n = 0;
    $link = '';
    $spacer = '<span class="spacer">&nbsp;|&nbsp;</span>';
        
	echo '<form action="filelist-save.php" method="post" name="listform" id="listform" class="displayform" >';


    if(file_exists($filelist_excluded)!==false){
        $excluded = file($filelist_excluded);
    }else{
        $excluded = array();
    }

    if(file_exists($filelist_newfiles)!==false){
        $newfiles = file($filelist_newfiles);
    }else{
        $newfiles = array();
    }


    if(file_exists($filelist_complete)!==false){
        $included = file($filelist_complete);
    }else{
        echo "<p>File List: " . $filelist_complete . " does not exist.</p>";
        echo "<p>Please run the generate filelist script.</p>";
        return 0;
    }

    
    foreach ($included as $filename) {
    
        $echecked = '';
        $rc = 1;
        $czoff = "zoff";
        $czon = "zon";
        $acl = "lpa";

        if (in_array($filename, $newfiles)) {
            $rc = 5;
            $acl = "lpag"; 
            $czon = "zona"; 
        }
   
        if (in_array($filename, $excluded)) {
            $echecked = 'checked="checked"';
            $czoff = "zoffa";
            $czon = "zona"; 
            $acl = "lpar";
            $rc = 1;
        }

        $filename = trim($filename); 
	
		$filename = str_replace($directory_root, "", $filename);
		
		$dirlen = strrpos($filename, "/") + 1;
		
		$directory = substr($filename, 0, $dirlen);
		
		$file = substr($filename, $dirlen);
        
        if(! $file) continue;
        
        if(is_dir($directory_root . '/' . $filename )==TRUE) {
            $czon = "zona";
            $onclickon = 'onclick="enableDirectory(' . $n  . ');"';
            $onclickoff = 'onclick="disableDirectory(' . $n  . ');"';  
            $pretag = '<span class="expand" id="xdir'  . $n . '" onclick="expandDirectory(' . $n . ');">[&nbsp;+&nbsp;]</span><span class="contract" id="cdir'  . $n . '" onclick="contractDirectory(' . $n . ');">[&nbsp;-&nbsp;]</span>';
            $cln = "lpd";
            $link = '<a name="lpa'  . $n . '" id="lpa'  . $n . '">' . $file . '</a>';
            $arrPointer = "ari" . $n;
            $$arrPointer = new directoryTree;
            $$arrPointer->dirindex = $n;
            $arrDirName[$n] = $filename;   
        }else{
            $onclickon = 'onclick="enableFile(' . $n . ');"';
            $onclickoff = 'onclick="disableFile(' . $n . ');"';
            $pretag = '<span class="none"></span>';
            $cln = "lpu";
            $link = '&nbsp;&middot;&nbsp;<a href="' . $domain_url . $directory . $file . '" target="view" class="' . $acl . '" id="lpa'  . $n . '">' . $file . '</a>';
        }
        
		echo '<div class="rc' . $rc . '" id="rn' . $n . '" onMouseOver="highlightRow(\'rn' . $n . '\',' . $rc . ', 2)"  onMouseOut="highlightRow(\'rn' . $n . '\',' . $rc . ', 0)" >';
        echo '<span class="ibt"><input type="button" name="zon' . $n . '" value="&nbsp;" class="' . $czon . '" id="zon' . $n . '" ' . $onclickon . '/></span>';    
        echo '<span class="ibt"><input type="button" name="zoff' . $n . '" value="&nbsp;" class="' . $czoff . '" id="zoff' . $n . '" ' . $onclickoff . '/></span>';

        $dirpath = explode('/', $filename);
        $dircount = count($dirpath);
        for($i=1; $i<$dircount; $i++) {
            if($i > 1) echo $spacer;
        }

        echo $pretag;
        
		echo '<span class="' . $cln . '" id="lpu'  . $n . '">' . $link . '</span>
        <span class="hidden">
        <a name="xr' . $filename . '" id="xr' . $filename . '" class="">' . $n . '</a>
        <input type="hidden" name="lfd'  . $n . '" value="' . $directory . $file  . '/"/>
        <input type="checkbox" name="lfc'  . $n . '" value="' . $filename . '" ' . $echecked  . '/> 
        </span>
        </div>';
        
          
        $key = array_search(rtrim($directory, "/"), $arrDirName);
        if($key !== false){
            $dirPointer = "ari" . $key;
            $$dirPointer->addItem($n);
        }

		$n++;
        
    }
    
    echo '<div class="fme"><input type="button" name="listbutton" class="fmb" id="listbutton" value="Save File List" onclick="filelistSave();"/></div></form>';


    $xfc = fopen($directory_data . "/fileTree.js", "w");
    foreach ($arrDirName as $inx => $value) {
        $outarray = "ari" . $inx;
        $stree = $$outarray->createJSArray(); 
        fwrite($xfc, $stree . "\n"); 
    }
    fclose($xfc); 


class directoryTree {

    var $dirindex;
    var $dirarray; 

    function directoryTree() {
        $this->dirarray = array();    
    }

    function addItem($item){
        array_push($this->dirarray, $item);
    }
    
    function createJSArray(){    
        $sOutput = "ari" . $this->dirindex . " = new Array(" ;
        foreach ($this->dirarray as $elem){
          $sOutput .= "'" . $elem . "',";  
        }
        $sOutput = rtrim($sOutput, ",");
        $sOutput .= ");"; 
        return $sOutput;
    }  
  
}


?>