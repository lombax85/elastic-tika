<?php
/**
 * Created by PhpStorm.
 * User: Lombardo
 * Date: 29/07/18
 * Time: 11:13
 */



function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}


/**
 * Gets local file metadata (like the date) to compare with the remote file
 * @param $file
 * @return array
 */
function getLocalFileMetadata($file) {
    return Array('date' => '01/01/01', 'size' => 123);
}

/**
 * Gets remote file metadata
 * @param $file
 * @return array
 */
function getRemoteFileMetadata($file) {
    return Array('date' => '01/01/02', 'size' => 123);
}

function getRemoteFileMetadataAlt($file) {
    $res = curlPutFile('http://tika:9998/meta', $file, Array('Accept: application/json'));
    return $res;
}

/**
 * Compares metadata of the remote file with the local file and check if they differs
 * @param $localFile
 * @param $remoteFile
 * @return bool true if files are the same, false if they differs
 */
function compareFilesMetadata($localFile, $remoteFile) {
    $localMetadata = getLocalFileMetadata($localFile);
    $remoteMetadata = getRemoteFileMetadata($remoteFile);

    $diff = array_diff($localMetadata, $remoteMetadata);

    if (count($diff) == 0) {
        return true;
    } else {
        return false;
    }
}



function getFileContentAsText($file) {
    $url_path_str = 'http://tika:9998/tika';
    $file_path_str = $file;

    $res = curlPutFile($url_path_str, $file_path_str);

    return $res;
}

function curlPutFile($url_path_str, $file_path_str, $headers = array()) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, ''.$url_path_str.'');
    curl_setopt($ch, CURLOPT_PUT, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $fh_res = fopen($file_path_str, 'r');

    curl_setopt($ch, CURLOPT_INFILE, $fh_res);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($file_path_str));

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curl_response_res = curl_exec ($ch);
    fclose($fh_res);

    return $curl_response_res;
}

function curlGetRequest($url_path_str) {
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url_path_str);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $output;
}

function curlPostRequest($url_path_str, $post_fields) {
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url_path_str);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $output;
}



// ----------------------------------------------

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$files = getDirContents('./files/');
header('Content-Type: application/json');

try {
    foreach ($files as $file) {
        if (compareFilesMetadata($file, $file) === false) {
            // file differs, start updating index
            $newMetaData = getRemoteFileMetadataAlt($file);

            if ($newMetaData) {
                $data = json_decode($newMetaData);
                $data->content = getFileContentAsText($file);
                echo json_encode($data, JSON_PRETTY_PRINT);

            } else {
                // pass to next file?
                //throw new Exception("File Not Recognized");
            }
        }
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
