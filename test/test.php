<?php
    $arr = array('apple' , 'banana' ,'orange','lemo','blueberry' );
    foreach($arr as $value)
    {
        $value = 'i'.$value;
        echo $value.' ';
    }
    print_r($arr);  