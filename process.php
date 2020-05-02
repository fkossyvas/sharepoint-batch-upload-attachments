<?php
require 'vendor/autoload.php';
use Thybag\SharePointAPI;

$sharepoint_userid = 'myuserid';
$sharepoint_password = 'mypassword';
$sharepoint_wsdl_file = 'this_is_my_wsdl';
$sharepoint_list_name = 'nem_of_the_list';
$sharepoint_list_uuid_field_name = 'UUIDholder';
$sharepoint_list_items = array();
$files_to_process_dir = '01_files_to_upload/';
$uploaded_files_dir = '02_files_uploaded/';
$error_files_dir = '03_files_with_errors/';
$total_files = 0;
$total_files_found = 0;
$total_files_uploaded = 0;
$total_files_with_errors = 0;

$sp = new SharePointAPI($sharepoint_userid, $sharepoint_password, $sharepoint_wsdl_file, 'SPONLINE');
$sharepoint_list_items = $sp->read($sharepoint_list_name);

function process_file($filename)
{
    global $sp;
    global $sharepoint_list_name;
    global $sharepoint_list_items;
    global $files_to_process_dir;
    global $error_files_dir;
    global $uploaded_files_dir;
    global $total_files_found;
    global $total_files_uploaded;
    global $total_files_with_errors;

    $uid = substr($filename, 0, 32); //get the uid from the first 32 characters of the filename
    $theid = 0; //this is the var which will contain the id of the list item to which we will attach the file
    foreach ($sharepoint_list_items as $a)
    {
        if ($a[$sharepoint_list_uuid_filed_name] == $uid)
        {
            $theid = $a["id"];
        } // if the field UUID holder of this list element contains the same uid, we get it's id
        
    }
    if ($theid == 0)
    { //id=0 means we haven't found it, so move the file to the error_files directory
        $old_name = $files_to_process_dir . $filename;
        $new_name = $error_files_dir . $filename;
        rename($old_name, $new_name);
        $total_files_with_errors += 1;
    }
    else
    { // ok,we've found it, upload it and then move it to the processed_files directory
        $old_name = $files_to_process_dir . $filename;
        $sp->addAttachment($sharepoint_list_name, $theid, $old_name);
        $new_name = $uploaded_files_dir . $filename;
        rename($old_name, $new_name);
        $total_files_found += 1;
    }
}

$files_to_process = array_slice(scandir($files_to_process_dir) , 2); //get all the files in the directory minus '.' and '..'
$total_files = count($files_to_process);

$files_left_to_process = $total_files;

echo '**********************************************' . PHP_EOL;
echo '*     Processing : start                     *' . PHP_EOL;
echo '**********************************************' . PHP_EOL;

foreach ($files_to_process as $a)
{
    process_file($a);
    $files_left_to_process -= 1;
    echo "Files left to process : " . $files_left_to_process . "      \r";
}

echo PHP_EOL;
echo '**********************************************' . PHP_EOL;
echo '*     Processing : end                       *' . PHP_EOL;
echo '**********************************************' . PHP_EOL;
echo 'Total files to process                    :' . $total_files . PHP_EOL;
echo 'Files with id existing in Sharepoint      :' . $total_files_found . PHP_EOL;
echo 'Files uploaded in Sharepoint              :' . $total_files_uploaded . PHP_EOL;
echo 'Files whith id non existing in Sharepoint :' . $total_files_with_errors . PHP_EOL;

?>
