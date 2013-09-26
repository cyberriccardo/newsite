<?php

/*

 Main page for the onSite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html

*/

// Retrieve the configuration variables

    require_once "config-read.php";   
 
    $directory_base = $varray["directory_base"];
    $directory_onsite = $directory_base . "/" . $varray["directory_onsite"]; 
    $directory_config = $directory_onsite . "/" . $varray["directory_config"];
    $directory_data = $directory_onsite . "/" . $varray["directory_data"];
    $directory_sitemap = $directory_onsite . "/" . $varray["directory_sitemap"];

    $onsite_url =  $varray["onsite_url"] . "/" . $varray["directory_onsite"];
    $directory_css = $onsite_url . "/" . $varray["directory_css"] . "/" ;
    $directory_ajax = $onsite_url . "/" . $varray["directory_ajax"] . "/" ;
    $directory_php = $onsite_url . "/" . $varray["directory_php"] . "/" ;    
    $server_data = $onsite_url . "/" . $varray["directory_data"] . "/" ;
    


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<title>onSite - Sitemap File Manager</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<meta name="description" content="Sitemap File Manager" />
<meta http-equiv="expires" content="Fri, 20 Jul 2007 00:00:18 -0700" />
<meta name="description" content="onSite - Sitemap File Manager" />
<link href="<? echo $directory_css ?>is1main.css" rel="stylesheet" type="text/css" />
<script src="<? echo $directory_ajax ?>is1main.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript">

    var siteconfig = '<? echo $siteconfig ?>';
    
    var bConfig = bList = bSite = 0;

    // Functions dependent on cgi variables
       
    var standard_priority = '0.0';
    var standard_frequency = 'yearly';    
    var domain_url = '';
    
    function configLoad() {
    
        var urlstring = "<? echo $directory_php ?>config-edit.php?siteconfig=" + siteconfig;
        displayHTML("configdisplay", urlstring);
        displayStatus("Configuration Loaded");
        
        findConfigVars();

    }
    
    function filelistLoad() {
    
        var urlstring = "<? echo $directory_php ?>filelist-edit.php?siteconfig=" + siteconfig;
        displayHTML("listdisplay", urlstring);
        displayStatus("File List Loaded");
        fileTreeLoad();
 
    }

    function sitemapLoad() {
        
        var urlstring = "<? echo $directory_php ?>sitemap-edit.php?siteconfig=" + siteconfig;
        initSitemap(urlstring);
        displayStatus("Sitemap Loaded");
        
    }
    
    function fileTreeLoad() {
        
        var urlstring = "<? echo $server_data ?>fileTree.js";
        var req = new XMLHttpRequest();
        req.open('GET', urlstring, false);
 //       req.overrideMimeType("application/javascript"); 
        req.setRequestHeader("Content-Type", "application/javascript"); 
     
        req.send(null); 

        if(req.status == 200)
          eval(req.responseText);
      
    }
   
    function sitemapSave() {
    
        var formurl = "<? echo $directory_php ?>sitemap-save.php?";
        
        var filelist_included = '<? echo $directory_data . "/" ?>' + findConfigValue("filelist_included");
        var urllist_sitemap = '<? echo $directory_sitemap . "/" ?>' + findConfigValue("urllist_sitemap");
        var filename_sitemap = '<? echo $directory_sitemap . "/" ?>' + findConfigValue("filename_sitemap");

        var domain_url = findConfigValue("domain_url"); 
        var directory_root = findConfigValue("directory_root"); 

        var surl = formurl;
        surl += 'action=start'; 
        surl += '&siteconfig=' + siteconfig;  

        sendHTML("processdisplay", surl);
 
        var curl = '';
        var suburl = '&xfs=' + filename_sitemap;
         suburl += '&xfu=' + urllist_sitemap;        
         suburl += '&xfl=' + filelist_included; 
         suburl += '&xdn=' + domain_url; 
         suburl += '&xdr=' + directory_root;
                 
        for (var i=0; i<slen; i++){
        
            if(document.getElementById("sma"+i) == null) continue;        
            if(document.forms["siteform"].elements["sfc"+i].checked === false) continue;

            curl = formurl;
            curl += 'smu=' + document.getElementById("sma"+i).innerHTML;
            curl += '&sml=' + document.getElementById("spl"+i).innerHTML;
            curl += '&ssf=' + document.forms["siteform"].elements["ssf"+i].value;
            curl += '&ssp=' + document.forms["siteform"].elements["ssp"+i].value;
            curl += suburl; 

            sendHTML("processdisplay", curl);
            
        }
        
        var eurl = formurl;
        eurl += 'action=end';
        eurl += '&xfs=' + filename_sitemap;

        sendHTML("processdisplay", eurl);
        
        displayStatus("Sitemap Files Saved");
        bSite = 0;
             
    }
    
    function filelistSave() {
       
        var formurl = "<? echo $directory_php ?>filelist-save.php?";
        var directory_data = '<? echo $directory_data ?>';
        var filelist_excluded = directory_data + "/" + findConfigValue("filelist_excluded");
        var directory_root = findConfigValue("directory_root"); 
    
        var surl = formurl;
        surl += 'action=start'; 
        surl += '&siteconfig=' + siteconfig;  

        sendHTML("processdisplay", surl);
  
        var curl = '';
                
        for (var i=0; i<flen; i++){
        
            if(document.listform.elements["lfc"+i].checked == false) continue; 

            curl = formurl; 
            curl += 'fn=' + filelist_excluded;
            curl += '&lfc=' + directory_root;
            curl += document.forms["listform"].elements["lfc"+i].value;
         
            sendHTML("processdisplay", curl);
  
        }
 
        displayStatus("Excluded Files Saved");

        bList = 0;
        
    }
    

    function configSave() {
    
        var formurl = "<? echo $directory_php ?>config-save.php?";
        var filename_config = "<? echo $directory_config  . "/" ?>" + siteconfig;

        var surl = formurl;
        surl += 'action=start'; 
        surl += '&siteconfig=' + siteconfig;  

        sendHTML("processdisplay", surl);
  
        var curl = '';
        var cid = '';
        var cval = '';
        var i=0;
        
        while(cid = document.getElementById("ctag"+i)){
        
            cval = cid.innerHTML;
            nval = cval.replace(/#/g, "~"); // Needed to pass the string to XMLHttpRequest
            
            if(nval.indexOf("export") >= 0){
                nval += document.forms["configform"].elements["cinp"+i].value;
            }
            
            curl = formurl; 
            curl += 'fn=' + filename_config;
            curl += '&cval=' + nval;

            sendHTML("processdisplay", curl); 
            i++;
        
        }

        findConfigVars(); 
        displayStatus("Configuration File Saved");
        bConfig = 0;
        
    }
    
    function generateData() {
    
        var formurl = "<? echo $directory_php ?>config-save.php?";
              
        var surl = formurl;
        surl += 'action=generate'; 
        surl += '&siteconfig=' + siteconfig;
 
        var rgen = displayHTML("processdisplay", surl);
        
        if(rgen != 0){
            alert("Check File Permissions");
            return;
        }else{
            displayStatus("Data Generated - Press Go to Refresh");
            bConfig = bList = bSite = 0;   
        }

    }
 

    function configNew() {
    
        var formurl = "<? echo $directory_php ?>config-save.php?";
        
        var newconfig = document.forms["selectform"].elements["newconfig"].value;
      
        var surl = formurl;
        surl += 'action=new'; 
        surl += '&newconfig=' + newconfig;
 
        var rconfig = displayHTML("processdisplay", surl);
        
        if(rconfig === "File already exists"){
            alert(rmsg);
            return;
        }
        
        var option = new Option(rconfig, rconfig);
        var olen = document.selectform.selectconfig.length;
        document.selectform.selectconfig.options[olen]=option; 
        document.selectform.selectconfig.options[olen].selected=true; 

        siteconfig = rconfig;

        var urlstring = "<? echo $directory_php ?>config-edit.php?siteconfig=" + siteconfig;
        displayHTML("configdisplay", urlstring);
 
        expandDisplay('config');
        
        displayStatus("Configuration File Saved");
        bConfig = bList = bSite = 0;

    }
    
    
</script>
</head>
<body id="mbody" onload="setDisplayHeight();">

<div class="md1" id="select">
<div class="md2" id="selectmenu">
<form method="post" name="selectform" id="selectform" action="#" >

<span class="ms2" id="selecttag">Select Configuration: </span>
<span class="ms2" id="selectbox">

<select name="selectconfig" id="selectconfig">
<option value="null"></option>
<option value="new">New File</option>
<?

/* PHP5     
    $files = scandir($directory_config, 1);

    foreach ($files as $fn) {
    
        if (fnmatch("*.sh", $fn)) {
            echo '<option value="' . $fn . '">' . $fn . '</option>'; 
        }
    }
*/    
    
    if ($handle = opendir($directory_config)) {
        while ($file = readdir($handle)) {
            if (fnmatch("*.sh", $file)) {
                echo '<option value="' . $file . '">' . $file . '</option>'; 
            }
        }
        closedir($handle);
    }

?>

</select>
</span>
<span class="ms2">
<input type="button" name="loadbutton" class="fmb" id="loadbutton" value=" Go "  onclick="loadpage(document.selectform.selectconfig.options[document.selectform.selectconfig.selectedIndex].value);"/>
</span>
<span class="ms2" id="cfnew">Save As: &nbsp;
<input type="text"  size="12" maxlength="254" name="newconfig" class="fmn" value="" />
&nbsp;
<input type="button" name="newbutton" class="fmb" id="newbutton" value=" Save "  onclick="configNew();"/>
</span>
</form>

<span class="ms2" id="statusmessage">
Status
</span>

</div>

<div class="md3" id="processdisplay">
</div>

</div>




<div class="md1" id="site">
<div class="md2" id="sitemenu">
<span class="ms1" id="siteshow" onclick="expandDisplay('site');">[ + ]</span>
<span class="ms1" id="sitehide" onclick="expandDisplay('site');">[ - ]</span>
<span class="ms1" id="sitetag" onclick="expandDisplay('site');">Sitemap View</span>
</div>

<div class="md3" id="sitedisplay">
Select a sitemap configuration.
</div>
</div>



<div class="md1" id="list">
<div class="md2" id="listmenu">
<span class="ms1" id="listshow" onclick="expandDisplay('list');">[ + ]</span>
<span class="ms1" id="listhide" onclick="expandDisplay('list');">[ - ]</span>
<span class="ms1" id="listtag" onclick="expandDisplay('list');">Directory Tree View</span>
</div>

<div class="md3" id="listdisplay">
Select a sitemap configuration.
</div>
</div>




<div class="md1" id="config">
<div class="md2" id="configmenu">
<span class="ms1" id="configshow" onclick="expandDisplay('config');">[ + ]</span>
<span class="ms1" id="confighide" onclick="expandDisplay('config');">[ - ]</span>
<span class="ms1" id="configtag" onclick="expandDisplay('config');">Sitemap Configuration</span>
</div>

<div class="md3" id="configdisplay">
Select a sitemap configuration.
</div>
</div>






</body>
</html>