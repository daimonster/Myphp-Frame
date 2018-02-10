<?php
    $arr = array('apple' , 'banana' ,'orange','lemo','blueberry' );
    // $string = implode(' ',$arr);
    // print_r($arr);  
    // echo $string;|
    // foreach($arr as $value)
    // {
    //     echo $value;
    // }
    $fields = array();
    $names = array();
    foreach ($arr as $key => $value)
    {
        $fileds[] = sprintf("`%s`",$key);
        $names[] = sprintf(":%s",$value);
    }
    print_r($fileds);
    print_r($names);