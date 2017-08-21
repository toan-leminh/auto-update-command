<?php
/**
 * Created by PhpStorm.
 * User: leminhtoan
 * Date: 8/21/17
 * Time: 17:05
 */

if($_POST){
    $command = $_POST['command'];
    file_put_contents('command/133.130.123.156/test_command', $command);

    echo "ファイル書き込みは成功しました";
}
?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h2>ファイル書き込み</h2>
                <form method="post" class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label" for="command">ファイル内容</label>
                        <textarea class="form-control" id='command' name="command" style="height:200px"></textarea>
                    </div>

                    <input class="btn btn-primary" type="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>
</body>
</html>

