<?php
require_once 'Auth.php';

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
// initial command
// exec("firewall_config/reallocate_ipset au bg br ca cn co de fr gb hk in it kr nl pl ro ru th tr ua us vn jp")
// sudo /usr/bin/firewall-cmd --permanent --zone=external --add-source=ipset:jp
// sudo /usr/bin/firewall-cmd --zone=drop --list-all
// sudo /usr/bin/firewall-cmd --zone=external --list-all
// sudo /usr/bin/firewall-cmd --get-ipsets

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
        $blackList =  getFileName($path . '/black/*');
        $whiteList =  getFileName($path . '/white/*');

        $validate = true;
        // Edit black list
        if (isset($_POST['edit_black_list'])) {
            $folder = 'black';
            $zone = "drop";
            $currentList =  $blackList;
            $listName = "ブラックリスト";
            $newList = isset($_POST['black_list']) ? $_POST['black_list'] : [];

            // Check with white list, can't add country in whitelist
            $validateList = array_intersect($newList, $whiteList);
            if(count($validateList)){
                $validate = false;
                $validateCountryName = [];
                foreach ($validateList as $ct){
                    $validateCountryName[] = "「{$countryList[$ct]}」";
                }
                $errorMessage = implode("、", $validateCountryName) . "はホワイトリストに存在するため追加できません";
            }

            // Can't add jp to black list
            $index = array_search('jp', $blackList);
            if($index !== false){
                unset($blackList[$index]);
            }
        // Edit white list
        }else{
            $folder = 'white';
            $zone = "external";
            $currentList =  $whiteList;
            $listName = "ホワイトリスト";
            $newList = isset($_POST['white_list']) ? $_POST['white_list'] : [];

            // Check with black list, can't add the country in blacklist
            $validateList = array_intersect($newList, $blackList);
            if(count($validateList)){
                $validate = false;
                $validateCountryName = [];
                foreach ($validateList as $ct){
                    $validateCountryName[] = "「{$countryList[$ct]}」";
                }
                $errorMessage = implode("、", $validateCountryName) . "はブラックリストに存在するため追加できません";
            }

            // Always allow jp
            if(!in_array('jp', $newList)){
                $newList[] = 'jp';
            }
        }
        // If validate then add/remove country in black list (white list)
        if($validate){
            $addCountries = array_diff($newList, $currentList);
            $removeCountries = array_diff($currentList, $newList);
        // Do nothing
        }else{
            $addCountries = [];
            $removeCountries = [];
        }

//        foreach ($addCountries as $addCountry) {
//            $addFile = $path . "/{$folder}/" . $addCountry;
//            file_put_contents($addFile, "");
//        }
//
//        foreach ($removeCountries as $removeCountry) {
//            $removeFile = $path . "/{$folder}/" . $removeCountry;
//            unlink($removeFile);
//        }
        $message = [];
        foreach ($addCountries as $addCountry) {
            // Check country
            if (isset($countryList[$addCountry])) {
                $addFile = $path . "/$folder/" . $addCountry;
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
                $removeFile = $path . "/$folder/" . $removeCountry;
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
