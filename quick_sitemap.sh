#!/bin/bash

#
# quick_sitemap.sh
#
# Usage> ./quick_sitemap.sh <config file>
# 
# Appends all new files to the end of the current sitemap.
#
# This script can be run from the command line, and/or placed in
# a crontab scheduled process.
#
#
# Written by Richard McMullen <mcmullen@florin.com>
# Released under the GNU General Public License version 3
# http://www.opensource.org/licenses/gpl-3.0.html
#
# Project Home Page: http://www.florin.com/onsite/index.html
#
#

#
# Import the directory and file variable definitions
#


source env.sh
source ${directory_base}/${directory_onsite}/${directory_config}/$1

directory_data=${directory_base}/${directory_onsite}/${directory_data}
directory_sitemap=${directory_base}/${directory_onsite}/${directory_sitemap}

deltemp() {

    if [ -e $1 ]
      then
        rm -f $1
    fi

}



# Define the working data file names.

   
    filename_sitemap="${directory_sitemap}/${filename_sitemap}"
    output_temp="${directory_sitemap}/sitemap-temp"  
    filelist_newfiles="${directory_data}/${filelist_newfiles}"

    standard_frequency=${standard_frequency}
    standard_priority=${standard_priority}
    directory_root=${directory_root}
    domain_url=${domain_url}

# Delete any existing temporary data files.


    deltemp $output_temp

 

#
# Parse the sitemap for all current files.
#



parsesitemap() {

    cat $1 | while read line
    do
        case $line in
            /urlset) break;;
        esac

        output_string="$line" 
        printf "$output_string\n" >> $2
    done

}


    parsesitemap $filename_sitemap $output_temp 


#
# Append new files to the sitemap
#


appendsitemap() {

    cat $1 | while read line
    do

        dtm=$(date --iso-8601=seconds -u -r $line)

        printf " <url>\n" >> $2
        
        output="<loc>"$4${line##$3}"</loc>\n" 
        printf "  "$output >> $2
        printf "  <lastmod>"$dtm"</lastmod>\n" >> $2
        printf "  <changefreq>"$4"</changefreq>\n" >> $2
        printf "  <priority>"$5"</priority>\n" >> $2             
        
        printf " </url>\n" >> $2
        
    done

}

    appendsitemap $filelist_newfiles $output_temp $directory_root $domain_url $standard_frequency $standard_priority

    printf "</urlset>" >> $output_temp
    
    
#
# Replace sitemap.xml with the new file
#

    mv -f $output_temp $filename_sitemap
    
    deltemp $output_temp


