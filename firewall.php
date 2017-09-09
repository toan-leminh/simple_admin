<?php
require_once 'Auth.php';

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

//exec("firewall_config/reallocate_ipset au bg br ca cn co de fr gb hk in it kr nl pl ro ru th tr ua us vn")
$path = 'cf/firewall';
$countryList = [
    "au" => "オーストラリア",
    "bg" => "ブルガリア",
    "br" => "ブラジル",
    "ca" => "カナダ",
    "cn" => "中国",
    "co" => "コロンビア",
    "de" => "ドイツ",
    "fr" => "フランス",
    "gb" => "イギリス",
    "hk" => "香港",
    "in" => "インド",
    "it" => "イタリア",
    "jp" => "日本",
    "kr" => "韓国",
    "nl" => "オランダ",
    "pl" => "ポーランド",
    "ro" => "ルーマニア",
    "ru" => "ロシア",
    "th" => "タイ",
    "tr" => "トルコ",
    "ua" => "ウクライナ",
    "us" => "米国",
    "vn" => "ベトナム",
];

$errorMessage = '';


// POSTメソッドを確認
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (checkAuth() == 1) {
        // Edit black list
        if (isset($_POST['edit_black_list'])) {
            $folder = 'black';
            $currentList =  getFileName($path . '/black/*');
            $newList = $_POST['black_list'];
            $zone = "drop";
            $listName = "ブラックリスト";

            // Can't remove jp
            if(isset($newList['jp'])){
                unset($newList['jp']);
            }
        // Edit white list
        }else{
            $folder = 'white';
            $currentList =  getFileName($path . '/white/*');
            $newList = $_POST['white_list'];
            $zone = "external";
            $listName = "ホワイトリスト";

            // Always allow jp
            $newList['jp'] = $countryList['jp'];
        }
        $addCountries = array_diff($newList, $currentList);
        $removeCountries = array_diff($currentList, $newList);

        $message = [];
        foreach ($addCountries as $addCountry) {
            // Check country
            if (isset($countryList[$addCountry])) {
                $addFile = $path . '/black/' . $addCountry;
                $addCountryName = $countryList[$addCountry];
                if (!file_exists($addFile)) {
                    // firewalldでipsetを遮断する
                    // firewall-cmd --permanent --zone=drop --add-source=ipset:cn
                    // firewall-cmd --reload
                    exec("sudo /usr/bin/firewall-cmd --permanent --zone=$zone --add-source=ipset:$addCountry", $output, $return);

                    if (!$return) {
                        // Create file
                        file_put_contents($addFile, "");
                    } else {
                        $message[] = "「{$addCountryName}」は{$listName}に追加失敗しました";
                    }
                }
            } else {
                $message[] = "「{$addCountry}」の国コードがサポートしておりません";
            }
        }

        foreach ($removeCountries as $removeCountry) {
            // Check country in country list
            if (isset($countryList[$removeCountry])) {
                $removeFile = $path . '/black/' . $removeCountry;
                $removeCountryName = $countryList[$removeCountry];

                if (file_exists($removeFile)) {
                    // firewalldでipsetを許可する
                    // firewall-cmd --permanent --zone=external --add-source=ipset:jp
                    // firewall-cmd --reload
                    exec("sudo /usr/bin/firewall-cmd --permanent --zone=$zone --remove-source=ipset:$removeCountry", $output, $return);
                    if (!$return) {
                        // Remove file
                        unlink($removeFile);
                    } else {
                        $message[] = "「{$removeCountryName}」は{$listName}から外すのは失敗ました";
                    }
                }
            } else {
                $message[] = "「{$removeCountry}」国コードがサポートしておりません";
            }
        }
        if (count($removeCountries) + count($addCountries)) {
            // Success
            if (count($message) == 0) {
                $errorMessage = "ブラックリストを変更しました";
            // Error
            } else {
                $errorMessage = implode("<br>", $message);
            }

            // Reload firewall
            exec("sudo /usr/bin/firewall-cmd --reload");
        }
    }else {
        $errorMessage = "<b>管理者権限がありません</b><br />";
    }
}
// Get file name list in a path
function getFileName($searchPath){
    $fileList = glob($searchPath);
    $nameList = [];
    foreach ($fileList as $f){
        $nameList[] = basename($f);
    }
    return $nameList;
}

$whiteList = getFileName($path . '/white/*');
$blackList = getFileName($path . '/black/*');

include('menu.php');
?>

<?php echo $errorMessage; ?>

<h3>ファイアーウォール</h3>
<hr>
<h4>IP ホワイトリスト</h4>
<form method="post" action="firewall.php">
    <?php $countryChunk = array_chunk($countryList, ceil(count($countryList)/3), true); ?>
    <?php foreach ($countryChunk as $column){ ?>
        <div style="float:left; width: 20%">
            <?php foreach ($column as $code=>$name){ ?>
                <?php
                if($code == 'jp'){
                    $attribute = ' checked disabled';
                }elseif(in_array($code, $whiteList)){
                    $attribute = ' checked';
                }else{
                    $attribute = '';
                }
                ?>
                <div>
                    <input type="checkbox" name="white_list[]" value="<?php echo $code ?>" <?php echo $attribute ?>> <?php echo $name ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div style="clear: both">
    <input type="submit" name="edit_white_list" value="変更" style="font-size: 16px" />
</form>

<div style="clear: both">
<br>
<hr>
<h4>IP ブラックリスト</h4>
<form method="post" action="firewall.php">
    <?php $countryChunk = array_chunk($countryList, ceil(count($countryList)/3), true); ?>
    <?php foreach ($countryChunk as $column){ ?>
        <div style="float:left; width: 20%">
            <?php foreach ($column as $code=>$name){ ?>
                <?php
                if($code == 'jp'){
                    $attribute = ' disabled';
                }elseif(in_array($code, $blackList)){
                    $attribute = ' checked';
                }else{
                    $attribute = '';
                }
                ?>
                <div>
                    <input type="checkbox" name="black_list[]" value="<?php echo $code ?>" <?php echo $attribute?> > <?php echo $name ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div style="clear: both">
    <input type="submit" name="edit_black_list" value="変更" style="font-size: 16px" />
</form>
