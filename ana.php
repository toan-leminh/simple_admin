<?php
include('menu.php');

$Path ='/etc/asterisk/cf/cal/';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //file_put_contents
        //echo grChk($_POST['id']).$_POST['mail']."<br>";       //test
        $id = $_POST['ana'];
	if($id=="1"){ $filename='1';}
	elseif($id=="2"){ $filename='2';}
	elseif($id=="3"){ $filename='3';}
	elseif($id=="4"){ $filename='4';}
	elseif($id=="5"){ $filename='5';}
	elseif($id=="8"){ $filename='8';}
	else{ $filename='9';}	//default 1

	unlink($Path.'announce');
	if(file_put_contents($Path.'announce', $filename )){
        	echo "営業時間外アナウンスを更新しました。";
        }
}
$file = file_get_contents($Path.'announce');
//echo $file[0];

echo '<table>';
echo "<form method='POST' action='ana.php'>";
echo '<th>設定</th><th>アナウンス</th><th></th></tr>';
echo '<tr><td><input type=radio name=ana value=9 ';
if($file[0]==9){echo 'checked';}
echo '></td><td>初期設定 1</td>';
//echo ' <audio src="01_taihen.ogg" preload="auto"></audio></td></tr>';
echo '<td><a href="01_taihen.wav" >音声</a></td></tr>';

for($i=1;$i<6;$i++){
        echo '<tr><td><input type=radio name=ana value="' . $i .'" ';
	if($file[0]==$i){echo 'checked';}
	echo '></td>';
        echo "<td>録音 ". $i . '</td><td>';
        echo "</tr>";
}
echo "</table>";
echo "<p><button type='submit'>変更</button></p></form>";
?>

