#!/bin/bash

#
# generate_filelists.sh
#
# Usage> ./generate_filelists.sh <config file>
# 
# The data files that are created by this script are used in
# conjunction with a cgi interface to select and specify the
# attributes of web pages to be included in a site map.
#
# File lists created by this script:
#
#  1) A complete, sorted list of web pages that match the specified file type extensions.
#  2) The directory tree of the web server.
#  3) A list of files that are in the current sitemap.
#  4) A list of files that are new since the last sitemap.
#
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


if [ -f env.sh ]
 then
   source env.sh
 else
   source $2 
fi



source ${directory_base}/${directory_onsite}/${directory_config}/$1

export directory_data=${directory_base}/${directory_onsite}/${directory_data}
export directory_sitemap=${directory_base}/${directory_onsite}/${directory_sitemap}

deltemp() {

    if [ -e $1 ]
      then
        rm -f $1
    fi

}



#
# 1) Generate the complete web server file list for the selected
#    file type extensions.
#
# The complete file list begins from the specified document root
# directory and includes the file type extensions specified in
# the confirguration file.  
#




# Define the working data file names.

    output_complete="${directory_data}/filelist-temp"  
    
    output_complete_sorted="${directory_data}/${filelist_complete}"


# Delete any existing temporary data files.


    deltemp $output_complete

    deltemp $output_timestamp

    deltemp $output_complete_sorted

    deltemp $output_timestamp_sorted 
 


# Retrieve the selected file type extensions.


    declare -a file_ext="${file_types}"
    element_count=${#file_ext[*]}
    
  

# Find all files that match the selected file extensions in the web server directories.


    for (( i = 0 ; i < $element_count ; i++ ))
	    
    do
       
        findfiletype=${file_ext[$i]}  
    	
        find ${directory_root} -name "*.${findfiletype}" -printf  '%p\n' >> ${output_complete}
       
    done



# Sort the results.

    sort ${output_complete} -o ${output_complete_sorted}
 

# Delete the temporary data files.

    deltemp $output_complete




#
# 2) Parse the sitemap for all current files.
#


parsesitemap() {

    grep \<loc\> $1 | while read line
    do 
        output_string=$3${line##<loc>$2}
        output_string=${output_string%%</loc>}
        echo $output_string >> $4
    done

}


# Define the working data file names and variables.

    output_current="${directory_data}/current-temp"  
    output_included="${directory_data}/${filelist_included}"  
    input_sitemap="${directory_sitemap}/${filename_sitemap}"  

    variable_root=${directory_root}
    variable_url=${domain_url}

    
# Delete the temporary data files.
    
    deltemp $output_current


# Parse the current sitemap.

    if [ -f $input_sitemap ]
     then
      parsesitemap $input_sitemap $variable_url $variable_root $output_current
      sort ${output_current} -o ${output_included}
    fi


# Delete the temporary data files.
   
    deltemp $output_current



#
# 3) Compare the files
#
# Compares: complete list, files excluded and files included
# from the site map, and generates a list of new files.
# 


# Define the working data file names.

    completefilelist="${directory_data}/${filelist_complete}"   
    excludedfilelist="${directory_data}/${filelist_excluded}"
    excludedsorted="${directory_data}/filelist-excluded-sorted-temp"
    incrementallist="${directory_data}/filelist-incremental-temp"
    includedfilelist="${directory_data}/${filelist_included}"
    newfilelist="${directory_data}/${filelist_newfiles}"




# Sort the excluded file list.


    if [ -f $excludedfilelist ]
     then
      sort ${excludedfilelist} -o ${excludedsorted} 
    fi




# Compare the complete file list to the excluded file list and create
# an incremental list that contains both included and new files.



    if [ -e $excludedsorted ]
     then
      comm -23 ${completefilelist} ${excludedsorted} > ${incrementallist}
     else
      cp -f ${completefilelist} ${incrementallist}
    fi




# Compare the included file list to the incremental file list created above
# and generate a list of new files.


    if [ -e $includedfilelist ]
     then
      comm -23 ${incrementallist} ${includedfilelist} > ${newfilelist}
     else
      cp -f ${incrementallist} ${includedfilelist}
      cp -f ${incrementallist} ${newfilelist}
    fi





# Delete the temporary data files.

    deltemp $excludedsorted

    deltemp $incrementallist




#
# 4) Generate and sort the directory tree of the web server and
#    then combine the directory tree with the complete file list.
#


    tree_complete="${directory_data}/tree-complete-temp"
    tree_complete_filelist="${directory_data}/tree-complete-filelist-temp"
    tree_complete_sorted="${directory_data}/${filelist_directorytree}"
    
    find ${directory_root} -type d >> ${tree_complete}
    
    cat ${tree_complete} ${completefilelist} > ${tree_complete_filelist}
    
    sort ${tree_complete_filelist} -o ${completefilelist}
    
    
    deltemp $tree_complete
    deltemp $tree_complete_filelist
 


