<?php
require_once 'Auth.php';
require_once 'Zip.php';
ini_set('display_errors', 1);

//$path='/etc/asterisk/cf/cal/';
$cfFolder = 'cf';
$zipFolder = 'download';
$backupFolder = 'backup';
$zipPassword = '1234';

$errorMessage = '';
// POSTメソッドを確認
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //エクスポート
    if (isset($_POST['export'])) {
        // 管理者権限チェック
        if(checkAuth() == 1){
            if(file_exists($cfFolder)){
                $zipFileName = 'cf_' . date('Ymd_His') . '.zip';
                $zipFilePath = realpath($zipFolder) . '/' . $zipFileName;
                $parentFolder = dirname(realpath($cfFolder));
                $folderName = basename($cfFolder);

                // Zip コマンドを呼び出す
                exec("cd $parentFolder; zip -P $zipPassword -r $zipFilePath $folderName 2>&1", $output, $return);

                if(!$return){
                    // ファイルダウンロード
                    ob_get_clean();
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false);
                    header('Content-Type: application/zip');
                    header("Content-Disposition: attachment; filename= $zipFileName;");
                    header('Content-Length: ' . filesize($zipFilePath));
                    readfile($zipFilePath);
                    exit();
                }else{
                    $errorMessage = "圧縮失敗しました";
                }
            }
        }else{
            $errorMessage =  "<b>管理者権限がありません</b><br />";
        }
    }

    if (isset($_POST['import'])) {
        if(isset($_FILES) && isset($_FILES['file'])) {
            // 管理者権限チェック
            if (checkAuth() == 1) {
                if (file_exists($cfFolder)) {
                    // Backup
                    $zipFileName = 'cf_' . date('Ymd_His') . '.zip';
                    $zipFilePath = realpath($backupFolder) . '/' . $zipFileName;
                    $parentFolder = dirname(realpath($cfFolder));
                    $folderName = basename($cfFolder);

                    // Backup
                    exec("cd $parentFolder; zip -P $zipPassword -r $zipFilePath $folderName", $output, $backupReturn);
                    if (!$backupReturn) {
                        // インポート
                        $file = $_FILES['file'];
                        if ($file['type'] == 'application/zip') {
                            $uploadFileName = $file['tmp_name'];

                            removeDirectory($cfFolder);
                            mkdir($cfFolder);
                            // Unzip folder
                            exec("unzip  -P $zipPassword  $uploadFileName -d $parentFolder 2>&1", $output, $return);

                            if (!$return) {
                                $errorMessage = "インポート成功しました";
                            } else {
                                $errorMessage = "インポート失敗しました";
                            }
                        };

                    } else {
                        $errorMessage = "バックアップ失敗しました";
                    }
                }
            } else {
                $errorMessage = "<b>管理者権限がありません</b><br />";
            }
        }
    }
}

// GET 確認
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['download_file'])){
        $fileName  = $_GET['download_file'];
        // 管理者権限チェック
        if( backupFileChk($fileName)){
            if(checkAuth() == 1){
                $filePath = $backupFolder . '/' .$fileName;
                if(file_exists($filePath)){
                    // Download file
                    // ファイルダウンロード
                    ob_get_clean();
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false);
                    header('Content-Type: application/zip');
                    header("Content-Disposition: attachment; filename= $fileName;");
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                    exit();
                }
            }else{
                $errorMessage =  "<b>管理者権限がありません</b><br />";
            }
        }
    }
}

// バックアップファイル名のバリデーション
function backupFileChk($fileName)
{
    return preg_match('/^cf_[0-9]{8}_[0-9]{6}.zip$/', $fileName);
}

// Remove directory
function removeDirectory($dir) {
    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach($it as $file) {
        if ($file->isDir()) {
            rmdir($file->getPathname());
        }else{
            unlink($file->getPathname());
        }
    }
    rmdir($dir);
}

// Backup files
$backupList = glob($backupFolder . '/*');

include('menu.php');
?>

<?php echo $errorMessage; ?>

<h3>エクスポート</h3>
<form method="post" action="ie.php">
    <input type="submit" class="btn btn-primary" id="export"  name="export" value="現在の設定をダウンロード"  style="font-size: 16px"/>
</form>
<!--<a href="download.php">-->
<!--    Download-->
<!--</a>-->

<hr>
<h3>インポート</h3>
<form method="post" action="ie.php" id="import_form" enctype="multipart/form-data">
    ファイル選択: <input type="file" name="file"> <br><br>
    <input type="submit" class="btn btn-danger"  id="import"  name="import" value="設定ファイルをインポート" style="font-size: 16px" />
</form>

<br>
<h3> バックアップ履歴</h3>
<table>
    <thead>
    <tr>
        <th>No</th>
        <th width="300px">ファイル</th>
    </tr>
    </thead>
    <tbody>
        <?php foreach ($backupList as $i=>$f){ ?>
            <tr>
                <td> <?php echo $i+1 ?></td>
                <td><a href="ie.php?download_file=<?php echo basename($f) ?>"><?php echo basename($f) ?></a></td>
            </tr>
        <?php }?>
    </tbody>
</table>

<script>
    $('#import_form').on('submit', function (e) {
        // メッセージ表示
        if(!confirm('設定ファイルを更新します。よろしいですか？')){
            e.preventDefault();
        }
    });
</script>
