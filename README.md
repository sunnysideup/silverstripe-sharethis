###############################################
ShareThis
Pre 0.1 proof of concept
###############################################

Developer
-----------------------------------------------
Nicolaas Francken [at] sunnysideup.co.nz

Requirements
-----------------------------------------------
SilverStripe 2.3.0 or greater.
HIGHLY RECOMMENDED:
http://sunny.svnrepository.com/svn/sunny-side-up-general/dataobjectsorter

Documentation
-----------------------------------------------



Installation Instructions
-----------------------------------------------
1. Find out how to add modules to SS and add module as per usual.
2. copy configurations from this module's _config.php file
into mysite/_config.php file and edit settings as required.
NB. the idea is not to edit the module at all, but instead customise
it from your mysite folder, so that you can upgrade the module without redoing the settings.

Add the following to your templates:

<% include ShareThis %>
<% include ShareAllExpandedList %>
<% include SocialNetworkingLinks %>

Thank you
-----------------------------------------------
This module is heavily based on the original
SS ShareThis module.

TO DO
-----------------------------------------------
* make statics protected
* deal with legacy issues
* sharethis "all" adds twice!




