<?php
defined('IN_FRAME') or exit('Access Denied');

class Index{

    public  function doIndex()
    {

        //var_dump( template()->getTemplateDir());
        view()->assign("title", "My Frame");
        view()->display('index.html');

    }
}