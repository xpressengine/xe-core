<?
$ADDR = include 'add.php';
if(!$_GET['addr1']) exit;

$addr1 = $_GET['addr1'];

$addr2 = $ADDR[$addr1];
if($addr2)
{
    foreach($addr2 as $v)
    {
?> <option value="<?=$v?>"><?=$v?></option>
<?
    }

}

?>
