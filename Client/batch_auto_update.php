<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 8/20/17
 * Time: 23:41
 */
require_once 'Log.php';
require_once 'libs/PHPMailer/PHPMailerAutoload.php';

// Set timezone
date_default_timezone_set('Asia/Tokyo');


// Enable error
$DEBUG = true;
if($DEBUG){
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);
}

///////============= SETTING ===============/////
// Server URL
//$apiURL = 'http://localhost/workspace/auto-update-cmd/Server/api_get_command.php';
$apiURL = "http://54.199.131.39/auto-update-command/Server/api_get_command.php";

// Log
$logFile = 'logs/all.log';

// Output
$outputCommonFile = 'output/common.log';
$outputClientFile = 'output/client.log';

// Last execute content
$lastCommonFile = 'last/common';
$lastClientFile = 'last/client';

// Email setting
$adminEmail = 'minhtoan.bk@gmail.com'; // 結果を送信するメール


// Zip
$zipTargetFolder = 'output';
$zipFolder = 'zip';
$zipPassword = '1234';
///////============= END SETTING ===============/////

// Check update contents
$apiResult = file_get_contents($apiURL);
if($apiResult){
    $apiResult = json_decode($apiResult, true);
    $commonContent = $apiResult['common'];
    $clientContent = $apiResult['client'];

    // Execute common command
    $commonExe = executeContent($commonContent, $lastCommonFile, $outputCommonFile, $logFile);

    // Execute common command
    $clientExe = executeContent($clientContent, $lastClientFile, $outputClientFile, $logFile);

    // Zip result and send email
    if($commonExe || $clientExe){
        $now = date('Ymd_His');

        // Zip output file
        $zipFileName = 'output_' . $now . '.zip';
        $zipFilePath = realpath($zipFolder) . '/' . $zipFileName;
        $parentFolder = dirname(realpath($zipTargetFolder));
        $folderName = basename($zipTargetFolder);

        // Zip コマンドを呼び出す
        exec("cd $parentFolder; zip -P $zipPassword -r $zipFilePath $folderName 2>&1", $output, $return);
        if(!$return){
            Log::info("Email to: " . $adminEmail, Log::INFO, $logFile);

            // Email
            $email = new PHPMailer();
            //$email->SMTPDebug = 2;
//            $email->isSMTP();
//            $email->Host = $smtpHost;
//            $email->Port = 587;
//            $email->SMTPAuth = true;
//            $email->Username = $smtpUser;
//            $email->Password = $smtpPassword;
//            $email->SMTPSecure = 'tls';
            // SMTPからSendmailに変更
            $email->isSendmail();

            $email->FromName  = gethostname();
            $email->addReplyTo($adminEmail, gethostname());
            $email->CharSet = 'UTF-8';
            $email->Subject   = 'コマンド実装結果-' . $now;
            $email->Body      = '実行結果はZipファイルに添付しました';
            $email->addAddress($adminEmail);
            $email->addAttachment($zipFilePath , $zipFileName );
            $email->send();

        }else{
            Log::info("Zip エラー: " . "\n" . print_r($output, true), Log::ERROR, $logFile);
        }
    }
}

/**
 * Execute a content
 *
 * @param array $contentArray
 * @param $lastContentFile
 * @param $outputFile
 * @param $logFile
 * @return string
 */
function executeContent($contentArray, $lastContentFile, $outputFile, $logFile){
    // Clear output file
    file_put_contents($outputFile, '');

    // Check history file. If not exited then create new blank file
    if(!file_exists($lastContentFile)){
        file_put_contents($lastContentFile, '');
    }
    // Get history file
    $lastContent = file_get_contents($lastContentFile);
    $content = json_encode($contentArray);

    if(count($contentArray) == 0){
        file_put_contents($lastContentFile, '');
        return false;
    }

    // Content is the same, then exist
    if($content && $lastContent == json_encode($content)){
        return false;
    }else{
        file_put_contents($lastContentFile, json_encode($content));
    }

    $commandString = $contentArray['command'];
    $commandList = explode(';', $commandString);

    // Execute each command and write to log
    foreach ($commandList as $command){
        if(trim($command)){
            $command = trim($command);
            $outputs = null;

            exec("$command 2>&1", $outputs, $return);

            // Get the output content
            $outputString = '';
            if($outputs){
                $outputString = implode("\n", $outputs);
            }

            // Write to log file and output file
            if(!$return){
                Log::info($command. "\n" . $outputString, Log::INFO, $outputFile);
                Log::info($command. "\n" . $outputString, Log::INFO, $logFile);
            }else{
                Log::info($command. "\n" . $outputString, Log::ERROR, $outputFile);
                Log::info($command. "\n" . $outputString, Log::ERROR, $logFile);
            }
        }
    }
    return true;
}


