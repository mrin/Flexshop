<?php

function convert($path)
{
    $d = dir($path);
    while($f = $d->read())
    {
        if($f != '.' && $f != '..' && $f != '.DS_Store')
        {
            if(is_dir($path.$f)) convert ($path.$f.'/');
            if(is_file($path.$f)) system("iconv -f cp1251 -t utf-8 ".$path.$f." > ".$path.$f."1");
        }
    }
}

phpinfo();
//convert("templates/")
?>