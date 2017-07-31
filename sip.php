<?php
include('menu.php');

echo '<table>';

//print_r( $_POST);
//$Path ='/etc/asterisk/cf/SIP/';
$Path = 'SIP/';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['del'])) {
        if ($_POST['del'] == 1) {
            $id = substr($_POST['id'], 0, 4);
            if (checkAuth() == 1) {
                if (is_file($Path . $id)) {
                    if (unlink($Path . $id)) {
                        echo ($id) . "を除外";
                    }
                }
                if (is_file($Path . "register" . substr($_POST['id'], 3, 1))) {
                    if (unlink($Path . "register" . substr($_POST['id'], 3, 1))) {
                        //echo ("register".substr($_POST['id'],3,1)) ."を除外しました";
                        echo "しました";
                    }
                }
            }else{
                echo "<b>管理者権限がありません</b><br />";
            }
        }
    } else {
        $id = substr($_POST['id'], 0, 4);
        if (checkAuth() == 1) {
            file_put_contents($Path . $id, "[" . $id . "](base)
username=" . $_POST['user'] . "
fromuser=" . $_POST['user'] . "
secret=" . $_POST['pass'] . "
callgroup=" . $_POST['callgroup'] . "
host=" . $_POST['domain'] . "
fromdomain=" . $_POST['domain'] . "
dtmfmode=" . $_POST['dtmf'] . "
");
            file_put_contents($Path . "register" . substr($_POST['id'], 3, 1), "register=>" . $_POST['user'] . ":" . $_POST['pass'] . "@" . $id . "
");
        }else{
            echo "<b>管理者権限がありません</b><br />";
        }

    }
    include('sip-re.php');
}
echo '<th>No</th><th>user</th><th>pass</th><th>domain</th><th>DTMF</th><th>callgroup</th><th> </th><th> </th></tr>
';

for ($i = 1; $i < 10; $i++) {
    foreach (glob($Path . 'sip' . $i) as $file) {
        if (is_file($file)) {
            echo "<tr><form method='post' action='sip.php'>";
            $tmpAry = explode("/", $file);
            $tmpNum = count($tmpAry) - 1;
            echo '<tr><td>' . $i . '</td>';
            $lines = file($file);
            foreach ($lines as $line) {      //file line
                //echo $line."<br />";  //test
                if (strpos($line, 'username=') !== false) {
                    echo '<td><input type=text name=user size=18 value=' . htmlspecialchars(str_replace('username=', '', $line)) . '></td>';
                }
                if (strpos($line, 'secret=') !== false) {
                    echo '<td><input type=password name=pass size=18 value=' . htmlspecialchars(str_replace('secret=', '', $line)) . '></td>';
                }
                if (strpos($line, 'host=') !== false) {
                    echo '<td><input type=text name=domain size=18 value=' . htmlspecialchars(str_replace('host=', '', $line)) . '></td>';
                }

                if (strpos($line, 'dtmfmode=') !== false) {
                    $dtmf = str_replace('dtmfmode=', '', $line);
                    $dtmf = str_replace(PHP_EOL, '', $dtmf);
                    $s1 = '';
                    $s2 = '';
                    $s3 = '';
                    $s4 = '';
                    if ($dtmf == "inband") {
                        $s1 = "selected";
                    }
                    if ($dtmf == "rfc2833") {
                        $s2 = "selected";
                    }
                    if ($dtmf == "info") {
                        $s3 = "selected";
                    }
                    if ($dtmf == "auto") {
                        $s4 = "selected";
                    }

                    echo "<td><select name='dtmf' >";
                    echo "<option value='inband' {$s1}>inband</option>";
                    echo "<option value='rfc2833' {$s2}>rfc2833</option>";
                    echo "<option value='info' {$s3}>info</option>";
                    echo "<option value='auto' {$s4}>auto</option>";
                    echo "</select></td>
";
                }

                if (strpos($line, 'callgroup=') !== false) {
                    $callgroup = str_replace('callgroup=', '', $line);
                    $callgroup = str_replace(PHP_EOL, '', $callgroup);
                }
            }
            echo "<td><select name='callgroup' >";
            for ($j = 1; $j < 10; $j++) {
                if (strcmp($j, $callgroup) === 0) {
                    echo "<option value='{$j}' selected>{$j}</option>";
                } else {
                    echo "<option value='{$j}' >{$j}</option>";
                }
            }

            echo "</select></td>";
            echo "<td><input type='hidden' name='id' value=" . $tmpAry[$tmpNum] . ">";
            echo "<button type='submit'>edit</button></td></form>";
            //del user
            echo "<form method='post' action='sip.php'><input type='hidden' name='del' value=1>";
            echo "<input type='hidden' name='id' value=" . $tmpAry[$tmpNum] . ">";
            echo "<td><button type='submit'>del</button></td></form>";
            echo '</tr>
';
        }
    }


    if (!is_file(($Path . 'sip' . $i))) {
        echo "<tr><form method='post' action='sip.php'>";
        echo '<tr><td>' . $i . '</td>';
        echo '<td><input type=text name=user size=18></td>';
        echo '<td><input type=text name=pass size=18></td>';
        echo '<td><input type=text name=domain size=18></td>';

        echo "<td><select name='dtmf' >";
        echo "<option value='inband' >inband</option>";
        echo "<option value='rfc2833'>rfc2833</option>";
        echo "<option value='info' >info</option>";
        echo "<option value='auto' >auto</option>";
        echo "</select></td>";

        echo "<td><select name='callgroup' >";
        for ($j = 1; $j < 10; $j++) {
            echo "<option value='{$j}' >{$j}</option>";
        }
        echo "</select></td>";
        echo "<td><input type='hidden' name='id' value=sip" . $i . ">";
        echo "<button type='submit'>edit</button></td></form>";
        echo '</tr>
';
    }

}
echo "</table>";
?>

