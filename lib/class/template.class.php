<?php

require_once EXT_LIB.'Smarty/Smarty.class.php';


function view(){
    static $template;
    if(empty($template)){
        $template = new template();
    }
    return $template;
}

class template extends Smarty {

    public function __construct()
    {

        parent::__construct();

        //$this->force_compile = true;
        $this->debugging = false;
        $this->caching = false;
        $this->cache_lifetime = 120;

        $this->setTemplateDir(APP_ROOT.MODULE.'/view/');
        $this->setConfigDir(APP_ROOT.MODULE.'/configs/');
        $this->setCompileDir(DATA_ROOT.MODULE.'/view_c/');
        $this->setCacheDir(DATA_ROOT.MODULE.'/cache/');

    }

    public  function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
        if(!strpos($template,".".TEMPLATE_SUFFIX)){
            $template.='.'.TEMPLATE_SUFFIX;
        }
        $etime=microtime(true);
        $run_time = $etime - START_TIME;
        view()->assign('run_time',$run_time) ;
        parent::display($template, $cache_id, $compile_id, $parent);
    }

}