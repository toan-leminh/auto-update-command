<?php
// Set no cache on browser
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

// Enable error
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


$commandFolder = 'command';
$commonFolder = 'command/common';

$clientIp = getClientIp();
if($clientIp){
    // 該当クライアントのコマンドフォーダー
    $clientFolder = $commandFolder . '/' . $clientIp;

    // 存在しない場合、フォルダー作成
    if(!file_exists($clientFolder)){
        mkdir($clientFolder);
    }

    $returnData = [
        'common' => [],
        'client' => []
    ];

    // Get contents in common folder
    $commonFiles = glob($commonFolder . '/*');
    if(count($commonFiles)){
        $commonFile = $commonFiles[0];
        $returnData['common'] = getFileContentWithMetadata($commonFiles[0]);
    }

    // Get contents in client folder
    $clientFiles = glob($clientFolder . '/*');
    if(count($clientFiles)){
        $returnData['client'] = getFileContentWithMetadata($clientFiles[0]);
    }

    echo json_encode($returnData);
}


/**
 * Get file content and its metadata
 * @param $file
 * @return null|array
 */
function getFileContentWithMetadata($file){
    if($file){
        // Get file content
        $content = file_get_contents($file);

        // Replace [IP] with client IP
        $ipText = str_replace('.', '-', getClientIp());
        $content = str_replace('[IP]', $ipText, $content);

        return  [
            'command' => $content,
            'fileName' => basename($file),
            'modified' => date ("Ymd_His", filemtime($file)),
        ];
    }
    return null;
}

// Get client IP Address
function getClientIp() {
    $ipAddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    ;

    return $ipAddress;
}