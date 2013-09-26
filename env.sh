#!/bin/bash

#
# Configuration variables for onSite directories.
#
# These variable definitions remain constant for all onSite processes 
# and should be defined during installation. See the INSTALL guide for
# the variable descriptions and the default directory structure before
# editing this script.
#
# For updates visit: http://www.florin.com/onsite/index.html
#



#
# Directory Definitions
#


#
# Base directory of the web server where the onSite scripts are installed.
# This is the web server directory immediately before the onsite installation.
# (No following slashes) 
#
# For example - if the installation directory is:
#   /var/www/html/foo/administrator/onsite
# then the base directory (directory_base) is:
#   /var/www/html/foo/administrator
#


export directory_base=/var/www/html/florin


#
# Base URL of the web server where the onSite scripts are installed.
# This is the URL for the directory immediately before the onsite
# installation. (No following slashes)
#
# For example - if the URL of the installation directory is:
#   http://localhost/administrator/onsite
# then the base URL (onsite_url) is:
#   http://localhost/administrator
#

export onsite_url=http://casa



#
# 1. Command-line script subdirectory (below base directory)
#

export directory_onsite=onsite


#
# 2. Data file subdirectory (below script directory)
#

export directory_data=data


#
# 3. Configuration file subdirectory (below script directory)
#

export directory_config=config

#
# 4. Sitemap xml file destination subdirectory (below script directory)
#

export directory_sitemap=sitemap




#
# Directories used by the web user interface - defined
# relative to the base URL of the onsite installation
#


#
# 1. PHP script directory (below base URL)
#

export directory_php=php


#
# 2. Javascript file directory (below base URL)
#

export directory_ajax=ajax


#
# 3. Style sheet directory (below base URL)
#

export directory_css=css



