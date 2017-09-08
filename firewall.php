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
    //"jp" => "日本",
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
    // 管理者権限チェック
    if(checkAuth() == 1){
        $country = $_POST['country'];
        $file = $path . '/' . $country;

        // Check country
        if(isset($countryList[$country])){
            if(!file_exists($file)){
                // firewalldでipsetを遮断する
                // firewall-cmd --permanent --zone=drop --add-source=ipset:cn
                // firewall-cmd --reload
                exec("sudo /usr/bin/firewall-cmd --permanent --zone=drop --add-source=ipset:$country && sudo /usr/bin/firewall-cmd --reload", $output, $return);

                if(!$return){
                    // Create file
                    file_put_contents($file, "");

                    $errorMessage = "ブラックリストに追加は成功しました";
                }else{
                    $errorMessage = "ブラックリストに追加は失敗しました";
                }
            }else{
                $errorMessage = "すでに追加されました";
            }
        }else{
            $errorMessage = "国コードがサポートしておりません";
        }
    }else{
        $errorMessage =  "<b>管理者権限がありません</b><br />";
    }
}

// GET 確認
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(isset($_GET['remove_country'])){
        $removeCountry = $_GET['remove_country'];

        // Check country in country list
        if(isset($countryList[$removeCountry])) {
            if(checkAuth() == 1) {
                $removeFile = $path . '/' . $removeCountry;
                if(file_exists($removeFile)){
                    // firewalldでipsetを許可する
                    // firewall-cmd --permanent --zone=external --add-source=ipset:jp
                    // firewall-cmd --reload
                    exec("sudo /usr/bin/firewall-cmd --permanent --zone=drop --remove-source=ipset:$removeCountry && sudo /usr/bin/firewall-cmd --reload", $output, $return);
                    if(!$return){
                        // Remove file
                        unlink($removeFile);

                        $errorMessage = "ブラックリストから外しました";
                    }else{
                        $errorMessage = "ブラックリストから外すのは失敗ました";
                    }
                }
            }else{
                $errorMessage =  "<b>管理者権限がありません</b><br />";
            }
        }else{
            $errorMessage = "国コードがサポートしておりません";
        }
    }
}

// Backup files
$fileList = glob($path . '/*');
$blackList = [];
foreach ($fileList as $f){
    $blackList[] = basename($f);
}

include('menu.php');
?>

<?php echo $errorMessage; ?>

<h3>ファイアーウォール</h3>
<hr>
<h4>IP ブラックリスト</h4>
<form method="post" action="firewall.php">
    国:
    <select name="country">
    <?php foreach ($countryList as $code=>$name){ ?>
        <?php if(!in_array($code, $blackList)){ ?>
        <option value="<?php echo $code ?>"><?php echo $name ?></option>
        <?php } ?>
    <?php } ?>
    </select>
    <input type="submit"  value="追加" style="font-size: 16px" />
</form>

<br>
<table>
    <thead>
    <tr>
        <th width="370">No</th>
        <th width="370">国</th>
<!--        <th></th>-->
    </tr>
    </thead>
    <tbody>
    <?php foreach ($blackList as $i=>$ct){ ?>
        <tr>
            <td > <?php echo $i+1 ?></td>
            <td>
                <a class="remove-country" href="firewall.php?remove_country=<?php echo $ct ?>"><?php echo $countryList[$ct] ?></a>
            </td>
<!--            <td><button>削除</button></td>-->
        </tr>
    <?php }?>
    </tbody>
</table>

<script>
    $('.remove-country').on('click', function (e) {
        var country = $(this).text();
        // メッセージ表示
        if(!confirm('「' + country +'」はブラックリストから外します。よろしいですか？')){
            e.preventDefault();
        }
    });
</script>
