<?php


class Yt{

    function Index(){
        $test = $_GET['test'];

        echo 'web - yt - index - '.$test;
        view()->display('yt');
    }

}