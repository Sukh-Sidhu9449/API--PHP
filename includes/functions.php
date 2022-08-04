<?php
function encode($value1, $value2)
{
    $val1 = base64_encode($value1);
    $val2 = base64_encode($value2);

    $arr = [];
    $arr = [$val1, $val2];
    $str = implode(".", $arr);
    return $str;
}
function decode($value1)
{
    $out = [];
    $out = explode(".", $value1);
    $val1=base64_decode($out[0]);
    $val2=base64_decode($out[1]);
    return [$val1,$val2];

}
function safe_input($value){
    $value=trim($value);
    $value=stripslashes($value);
    $value=htmlspecialchars($value);
    return $value;
}

function totalTime($value1,$value2){
$time = $value1;
$parsed = date_parse($time);
$seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

$time1 = $value2;
$parsed1 = date_parse($time1);
$seconds1 = $parsed1['hour'] * 3600 + $parsed1['minute'] * 60 + $parsed1['second'];

$diff=$seconds1-$seconds;

$hours = floor($diff / 3600);
$mins = floor($diff / 60 % 60);
$secs = floor($diff % 60);
$timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
return $timeFormat;
}
?>