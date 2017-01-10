# sharepoint-batch-upload-attachments

CLI script in Php for uploading attachments to Sharepoint online list items

The script uses the great [PHP SharePoint Lists API](https://github.com/thybag/PHP-SharePoint-Lists-API) made by [thybag](https://github.com/thybag). 

This script can batch upload files as attachments on list items in a Sharepoint Online list. The files have to be on the *01_files_to_upload* directory. 

Their filename should be like XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX_THE-ACTUAL-FILENAME.any_ext (for example : 00A26309C6610DC2C12578AF0047BE05_THE-ACTUAL-FILENAME.pdf).

The script will search the list for an item with it's field named *UUIDholder* equal with the 32 byte id of the filename. If there is one then it will attach the file to the list item (removing from the filename the 32 byte id and the underscore character).

The files already processed will be moved to the *02_files_uploaded directory*, this way relaunching  the batch, it will never reprocess the already processed files.

The files with errors (with id not found in list) will be moved to the *03_files_with_errors directory*.

### Usage Instructions

#### Installation
Download the WSDL file for the SharePoint Lists you want to interact with. This can normally be obtained at: sharepoint.url/subsite/_vti_bin/Lists.asmx?WSDL 

 1. Run composer :
 > composer update

 2. Update these variables of the script :

$sharepoint_userid

$sharepoint_password

$sharepoint_wsdl_file

$sharepoint_list_name

$sharepoint_list_uuid_field_name


### Why did i created this ?
I migrated an old app which was holding it's data on rows in DBMS with file attachments to them as BLOBs. 
The easiest way to do the porting to Sharepoint was updating all the rows by adding a column with an unique 32 byte id in each one of them and then downloading them. 
The next step was downloading the attachments from BLOBs adding at the beginning of to their filename the 32 byte id plus the undescore character.
After that, i wrote this script ;-)
