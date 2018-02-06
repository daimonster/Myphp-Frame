<?php
$arr = array(1,1,3,4,'9' => 10, '12' => 100);
print_r($arr);
// define("CONSTANT","Hello world");
// echo CONSTANT; // 输出 "Hello world."
// echo Constant;
$data = "foo:*:1023:1000::/home/foo:/bin/sh";
$string = explode(":", $data);
print_r($string); 