/*

 Ajax and Javascript functions for the onsite user interface.

 Written by Richard McMullen <mcmullen@florin.com>
 Released under the GNU General Public License version 3
 http://www.opensource.org/licenses/gpl-3.0.html

 Project Home Page: http://www.florin.com/onsite/index.html


*/
    
    var flen = 0;
    var slen = 0;
    

function loadpage(selectedconfig) {

// Check for form changes

    checkSave();
    hideContents();
    clearContents();

// Set the new configuration file name

    siteconfig = selectedconfig;    

    if(siteconfig === "null") return 0;
    if(siteconfig === "new"){
        document.getElementById("cfnew").style.display = "inline";
        return 0;
    }


// Retrieve the data

    expandDisplay('site');
    
    configLoad();
    filelistLoad();
    sitemapLoad();
    

// Add event listeners and initialize the data

           
    filelistEvents();
   
    sitemapEvents();        
    
    displayStatus("Ready");


}


function hideContents(){

    var aSections = ["config", "list", "site"];
    
    for (var i = 0; i < aSections.length; i++) {
    
        document.getElementById(aSections[i]+"display").style.display = "none";
        document.getElementById(aSections[i]+"hide").style.display = "none";
        document.getElementById(aSections[i]+"show").style.display = "inline";
    
    }
    
    document.getElementById("cfnew").style.display = "none";
   
} 

function clearContents(){

    var aSections = ["config", "list", "site"];
    
    for (var i = 0; i < aSections.length; i++) {
        document.getElementById(aSections[i]+"display").innerHTML = "Select a sitemap configuration."; 
    }
    
    bConfig = bList = bSite = 0;


}

function checkSave(){  
    
    if(bConfig > 0 && confirm("Save Configuration Form Changes?")) configSave();
     
    if(bList > 0 && confirm("Save File List Changes?")) filelistSave();

    if(bSite > 0 && confirm("Save Sitemap Changes?")) sitemapSave();

}

function expandDisplay(sSegment) {

    var displaydiv = document.getElementById(sSegment+"display"); 
    var selectedDisplay = displaydiv.style.display;

    var hidetag = document.getElementById(sSegment+"hide"); 
    var showtag = document.getElementById(sSegment+"show"); 
    
    hideContents();
           
    displaydiv.style.display = (selectedDisplay == "block") ? "none" : "block";
    hidetag.style.display = (selectedDisplay == "block") ? "none" : "inline";
    showtag.style.display = (selectedDisplay == "block") ? "inline" : "none";

}



function displayHTML(divid, aurl) {

    var indiv = document.getElementById(divid);

    var req = new XMLHttpRequest();
    req.open('GET', aurl, false); 
    req.send(null);
    if(req.status == 200)
      indiv.innerHTML = req.responseText;
      
    return req.responseText;
    
}


function sendHTML(divid, posturl) {

    var indiv = document.getElementById(divid);

    var req = new XMLHttpRequest();
    req.open('POST', posturl, false); 
    req.send(null);
    if(req.status == 200)
      indiv.innerHTML = req.responseText;
      
    return req.responseText;

}

function displayStatus(sMessage) {

    var mdiv = document.getElementById("statusmessage");
    mdiv.innerHTML = sMessage;

}


function findConfigValue(sVarname){
    
    var cval = '';   
    
    var cid = document.getElementById(sVarname);

    if(cid === null) return 0;  
    
    var cinx = cid.innerHTML;   

    if(cinx > 0) cval = document.forms["configform"].elements["cinp"+cinx].value;
    
    return cval;
    
}

function findConfigVars(){

    standard_priority = findConfigValue("standard_priority");

    standard_frequency = findConfigValue("standard_frequency");

    domain_url = findConfigValue("domain_url");

}


function setDisplayHeight(){

    var isb = document.getElementById("mbody");  
    
    var currentValue = isb.clientHeight;

    var ieht = document.documentElement.clientHeight;

    var newheight = ieht - Number(currentValue);
     
    var ndx = document.getElementById("mbody").getElementsByTagName("div"); 
    for (var i=0; i<ndx.length; i++){
        if (ndx[i].className=="md3") {
            ndx[i].style.height = newheight + "px";
            ndx[i].style.display = "none";
        }  
    }
 
} 

// Add Event Listeners:

function sitemapEvents() {
  
    var ndx = document.getElementById("siteform").getElementsByTagName("select"); 

    for (var i=0; i<ndx.length; i++){
    
        if (ndx[i].addEventListener){
          ndx[i].addEventListener('change', triggerSite, false); 
        } else if (ndx[i].attachEvent){
          ndx[i].attachEvent('onchange', triggerSite);
        }

    }
    
    var mdx = document.getElementById("siteform").getElementsByTagName("input"); 

    for (var i=0; i<mdx.length; i++){
    
        if (mdx[i].addEventListener){
          mdx[i].addEventListener('change', triggerSite, false); 
        } else if (mdx[i].attachEvent){
          mdx[i].attachEvent('onchange', triggerSite);
        }

    }
    
    
}


function triggerSite(e){
   
    bSite++; 
    
}

function filelistEvents() {
  
    var ndx = document.getElementById("listform").getElementsByTagName("input");
    
    // The -1 below is to exclude the save button from the event listener.

    for (var i=0; i<ndx.length-1; i++){
    
        if (ndx[i].addEventListener){
          ndx[i].addEventListener('click', triggerList, false); 
        } else if (ndx[i].attachEvent){
          ndx[i].attachEvent('onclick', triggerList);
        }

    }
       
    if(ndx.length > 3) flen = (ndx.length-1) / 4;
    
}


function triggerList(e){

    bList++;
   
}



function highlightRow(selectedRow, crc, onf){

   var clr = crc + onf;
   var color = "#FFFFFF";
    
   switch (clr){
       case 0 : 
          color = "#FFEFD5";
          break;
       case 1 : 
          color = "#FFFFFF";
          break;
       case 2 : 
          color = "#CCFFCC";
          break;
       case 3 : 
          color = "#CCFFCC";
          break;
       case 5 : 
          color = "#FFEFD5";
          break;
       case 7 : 
          color = "#CCFFCC";
          break;
       default : 
          color = "#FFFFFF";
          break;

    }
    
    var displayrow = document.getElementById(selectedRow); 
    displayrow.style.backgroundColor = color;

}


// File enabling functions:

function disableDirectory(ndir){

    var tdir = new disableDirectoryContents(ndir);

}

function disableDirectoryContents(ndir){

    var alx = new Array();   
    eval("var arl = alx.concat(ari" + ndir + ");");  
    
	for (var i = 0; i < arl.length; i++){
    
        var arx = arl[i];
		disableFile(arx);
          
		if(document.getElementById("xdir"+arx)) {
        
            var tdir = new disableDirectoryContents(arx);
            
		} 
  
    } 
    
	document.getElementById("zoff"+ndir).style.backgroundColor = "#FF0000"; 
	document.getElementById("zon"+ndir).style.backgroundColor = "#008000";
	document.getElementById("lpa"+ndir).style.color = "#CC0000"; 
    
}


function enableDirectory(ndir){

    var tdir = new enableDirectoryContents(ndir);

}

function enableDirectoryContents(ndir){

    var alx = new Array();   
    eval("var arl = alx.concat(ari" + ndir + ");");  
    
	for (var i = 0; i < arl.length; i++){
    
        var arx = arl[i];
		enableFile(arx);
          
		if(document.getElementById("xdir"+arx)) {
        
            var tdir = new enableDirectoryContents(arx);
            
		} 
  
    } 
    
	document.getElementById("zoff"+ndir).style.backgroundColor = "Maroon";  
	document.getElementById("zon"+ndir).style.backgroundColor = "Lime";
	document.getElementById("lpa"+ndir).style.color = "Black";
   
}


function disableFile(nIndex){
     
    document.forms["listform"].elements["lfc"+nIndex].checked = "checked";
	document.getElementById("zoff"+nIndex).style.backgroundColor = "#FF0000"; 
	document.getElementById("zon"+nIndex).style.backgroundColor = "#008000";
	document.getElementById("lpa"+nIndex).style.color = "#CC0000"; 

    var fname = document.forms["listform"].elements["lfc"+nIndex].value;
    
    var xr = document.getElementById(fname);
    
    if(xr != null) {

        var xi = xr.innerHTML;   

        document.forms["siteform"].elements["sfc"+xi].checked = false;
        document.getElementById("xoff"+xi).style.backgroundColor = "#FF0000";      
        document.getElementById("xon"+xi).style.backgroundColor = "#008000";
        document.getElementById("sma"+xi).style.color = "#CC0000";

    }   

}


function enableFile(nIndex){

    document.forms["listform"].elements["lfc"+nIndex].checked = false;   
	document.getElementById("zoff"+nIndex).style.backgroundColor = "Maroon";  
	document.getElementById("zon"+nIndex).style.backgroundColor = "Lime";
	document.getElementById("lpa"+nIndex).style.color = "Black";
    
    var fname = document.forms["listform"].elements["lfc"+nIndex].value;
    
    var xr = document.getElementById(fname);
    
    if(xr != null) {

        var xi = xr.innerHTML;   

        document.forms["siteform"].elements["sfc"+xi].checked = "checked";
        document.getElementById("xoff"+xi).style.backgroundColor = "Maroon";      
        document.getElementById("xon"+xi).style.backgroundColor = "Lime";
        document.getElementById("sma"+xi).style.color = "Black";

    } else {
    
        if(document.getElementById("xdir"+nIndex)===null) addToSitemap(fname);  // Checks for directory
        
    }
 
}


function disableURL(nIndex){
     
     
    var fname = document.forms["siteform"].elements["smu"+nIndex].value;
    
    var xr = document.getElementById("xr"+fname);

    if(xr != null) {
    
        disableFile(xr.innerHTML)
    
    }else{
    
        document.forms["siteform"].elements["sfc"+nIndex].checked = false;
        document.getElementById("xoff"+nIndex).style.backgroundColor = "#FF0000";      
        document.getElementById("xon"+nIndex).style.backgroundColor = "#008000";
        document.getElementById("sma"+nIndex).style.color = "#CC0000";

    
    }

}


function enableURL(nIndex){

    var fname = document.forms["siteform"].elements["smu"+nIndex].value;
    
    var xr = document.getElementById("xr"+fname);

    if(xr != null) {
    
        enableFile(xr.innerHTML)
    
    }else{
    
        document.forms["siteform"].elements["sfc"+nIndex].checked = "checked";
        document.getElementById("xoff"+nIndex).style.backgroundColor = "Maroon";      
        document.getElementById("xon"+nIndex).style.backgroundColor = "Lime";
        document.getElementById("sma"+nIndex).style.color = "Black";

    }

 
}


// File tree functions:


function expandDirectory(tnx) {

    var alx = new Array();   
    eval("var arl = alx.concat(ari" + tnx + ");");  
    
	for (var i = 0; i < arl.length; i++){
    
        var arx = arl[i];
        
        document.getElementById("rn"+arx).style.display = "block";
 
         
		if(document.getElementById("xdir"+arx)) {
	
            document.getElementById("xdir"+arx).style.display = "inline";
            document.getElementById("cdir"+arx).style.display = "none";
            
		} 
  
    } 

    document.getElementById("xdir"+tnx).style.display = "none";
    document.getElementById("cdir"+tnx).style.display = "inline"; 
  
}


function contractDirectory(ndir) {

    var tdir = new hideDirectoryContents(ndir);

}


function hideDirectoryContents(tnx){

   
    var alx = new Array();   
    eval("var arl = alx.concat(ari" + tnx + ");"); 
    
    
	for (var i = 0; i < arl.length; i++){
    
        var arx = arl[i];

		if(document.getElementById("xdir"+arx)!==null) {
	
            var tdir = new hideDirectoryContents(arx);
            
		} 
        
        document.getElementById("rn"+arx).style.display = "none";

    } 

    document.getElementById("xdir"+tnx).style.display = "inline";
    document.getElementById("cdir"+tnx).style.display = "none"; 

}





// Sitemap display functions

    var apriority = new Array("0.0", "0.1", "0.2", "0.3", "0.4", "0.5", "0.6", "0.7", "0.8", "0.9", "1.0");
    var achangefreq = new Array("always", "hourly", "daily", "weekly", "monthly", "yearly", "never");


function objectSitemap(vsmu, vsml, vssf, vssp, n, rc){


    var line = '<div class="sc' + rc + '" id="smr' + n + '"'; 
     line += ' onMouseOver="highlightRow(\'smr' + n + '\', ' + rc + ', 2)"';  
     line += ' onMouseOut="highlightRow(\'smr' + n + '\', ' + rc + ', 0)" >';

    line += '<span class="ibt">';
     line += '<input type="button" name="xon' + n + '" value="&nbsp;"'; 
     line += ' class="xon" id="xon' + n + '"'; 
     line += ' onclick="enableURL(' + n + ');" /></span>';  
     line += '<span class="ibt">';
     line += '<input type="button" name="xoff' + n + '" value="&nbsp;"'; 
     line += ' class="xoff" id="xoff' + n + '" ';
     line += ' onclick="disableURL(' + n + ');" /></span>';
     
     line += '<span class="spu" id="spu' + n + '">';
     line += '<a href="' + domain_url + vsmu + '"'; 
     line += ' target="view" id="sma' + n + '" >' + vsmu + '</a>';
     line += '</span>';


     line += '<span class="hidden">';
     line += '<input type="hidden" name="smu' + n + '" value="' + vsmu + '" />';
     line += '<a name="' + vsmu + '" id="' + vsmu + '">' + n + '</a>';
     line += '<a name="' + vsml + '" id="' + vsml + '">' + n + '</a>';     
     line += '<input type="checkbox" name="sfc'  + n + '" value="' + vsmu + '" checked="checked" />';
     line += '</span>';
     
     line += '<span class="spl" id="spl' + n + '">';
     line += vsml;
     line += '</span><span class="spf" id="spf' + n + '">';

     objFreq = new createSelectBox("ssf" + n, achangefreq, vssf);
     line += objFreq.selectbox;
     
     line += '</span><span class="spp" id="spp' + n + '">';

     objPrio = new createSelectBox("ssp" + n, apriority, vssp);
     line += objPrio.selectbox;

     line += '</span></div>';
     
     this.vline = line;
     

}

function createSelectBox(sname, sarray, sselected){

    var isselected = '';

    var sbox = '<select class="sms" name="'  + sname + '" >'; 
   
    for(i=0; i<sarray.length; i++){
    
        isselected = '';
        if(sselected.indexOf(sarray[i]) === 0) isselected = 'SELECTED';
//        if(sarray[i] == sselected) isselected = 'SELECTED';

        sbox += '<option value="' + sarray[i] + '" ' + isselected + '>' + sarray[i] + '</option>'; 
    
    }

    sbox += '</select>';
    
    this.selectbox = sbox;

}


function addToSitemap(sFname){
   
    objRow = new objectSitemap(sFname, "0000-00-00T00:00:00Z", standard_frequency, standard_priority, slen, 0);

    var sp1 = document.createElement("div");
          
    sp1.setAttribute("id", "node" + slen);

    sp1.innerHTML = objRow.vline;
    
    var sp2 = document.getElementById("aj1");
    var parentDiv = sp2.parentNode;
    
    parentDiv.insertBefore(sp1, sp2);
    
    slen++;
    
    bSite++;

}


function initSitemap(urlstring){

   var indiv = document.getElementById("sitedisplay");

    var ht = '<form action="sitemap-save.php" method="post" name="siteform" id="siteform" class="displayform" >';
    ht += '<div class="str" id="str3">';
    ht += '<span class="stu" id="stu3"><span class="stuf" id="stu4" onclick="sortSitemapName(\'forward\');">[&nbsp;+&nbsp;]</span>';
    ht += '&nbsp;File Name&nbsp;&nbsp;<span class="stur" id="stu5" onclick="sortSitemapName(\'reverse\');">[&nbsp;-&nbsp;]</span>';
    ht += '</span>';
    ht += '<span class="stp">Priority</span>';
    ht += '<span class="stf">Change Frequency</span>';
    ht += '<span class="stl" id="stl3"><span class="stlf" id="stl4" onclick="sortSitemapDate(\'forward\');">[&nbsp;+&nbsp;]</span>';
    ht += '&nbsp;Last Modified&nbsp;<span class="stlr" id="stl5" onclick="sortSitemapDate(\'reverse\');">[&nbsp;-&nbsp;]</span></span>';
    ht += '</div>';

    ht += '<div class="fme" id="aj1">';
    ht += '<input type="button" name="sitebutton" class="fmb" id="sitebutton" value="Save Sitemap" onclick="sitemapSave();"/>';
    ht += '</div></form>';

    indiv.innerHTML = ht;
       
    var req = new XMLHttpRequest();
    req.open('GET', urlstring, false); 
    req.send(null);
    if(req.status == 200)
      var sfull = req.responseText.split(",");

    var srow = new Array(4);
    var rc = 1;


    for(var i=0; i<sfull.length; i++){

        srow = sfull[i].split(" ");
        
        if(!srow[0]) continue;
  
        objRow = new objectSitemap(srow[0], srow[1], srow[2], srow[3], i, rc);
       
        sp1 = document.createElement("div");
              
        sp1.setAttribute("id", "node" + i);

        sp1.innerHTML = objRow.vline;
       
        sp2 = document.getElementById("aj1");
        parentDiv = sp2.parentNode;
        
        parentDiv.insertBefore(sp1, sp2);

        if(srow[4] > 0){
            document.forms["siteform"].elements["sfc"+i].checked = false;
            document.getElementById("xoff"+i).style.backgroundColor = "#FF0000";      
            document.getElementById("xon"+i).style.backgroundColor = "#008000";
            document.getElementById("sma"+i).style.color = "#CC0000";
        }

        // if(++rc > 1) rc = 0;
        
    }
    
    slen = sfull.length - 1;
    
}

function sortSitemapName(sDirection){


    var aFiles = new Array();
    
    for(var i=0; i<slen; i++){
    
        aFiles[i] = document.getElementById("sma"+i).innerHTML;
  
    }
    
    aFiles.sort();
    
    var sp2 = document.getElementById("aj1");
    var parentDiv = sp2.parentNode;
    
    var sc = 0;
    
    switch (sDirection){
    
       case "forward" : 

        for(var j=0; j<slen; j++){
        
       
            var nNode = document.getElementById(aFiles[j]).innerHTML;
            var sortNode = document.getElementById("node"+nNode);
            var innerNode = document.getElementById("smr"+nNode);
     
            parentDiv.insertBefore(sortNode, sp2); 
    
        }
        
        break;
        
       case "reverse" : 

        for(var k=slen-1; k>=0; k--){
        
            var nNode = document.getElementById(aFiles[k]).innerHTML;
            var sortNode = document.getElementById("node"+nNode);
            var innerNode = document.getElementById("smr"+nNode);
     
            parentDiv.insertBefore(sortNode, sp2); 
    
        }
        
        break;  
 
    }
    
}

function sortSitemapDate(sDirection){


    var aFiles = new Array();
    
    for(var i=0; i<slen; i++){
    
        aFiles[i] = document.getElementById("spl"+i).innerHTML;
        aFiles[i] += ":" + i;

    }
    
    aFiles.sort();
    
    var sp2 = document.getElementById("aj1");
    var parentDiv = sp2.parentNode;
    
    var sc = 0;
    
    switch (sDirection){
    
       case "forward" : 

        for(var j=0; j<slen; j++){
           
            var si = aFiles[j].lastIndexOf(":");
            var sTimestamp = aFiles[j].substr(0, si);
            var nNode = aFiles[j].substr(si+1);

            var sortNode = document.getElementById("node"+nNode);
            var innerNode = document.getElementById("smr"+nNode);
     
            parentDiv.insertBefore(sortNode, sp2); 
    
        }
        
        break;
        
       case "reverse" : 

        for(var k=slen-1; k>=0; k--){
        
            var si = aFiles[k].lastIndexOf(":");
            var sTimestamp = aFiles[k].substr(0, si);
            var nNode = aFiles[k].substr(si+1);

            var sortNode = document.getElementById("node"+nNode);
            var innerNode = document.getElementById("smr"+nNode);
     
            parentDiv.insertBefore(sortNode, sp2); 
                
        }
        
        break;  
 
    }
    
}

