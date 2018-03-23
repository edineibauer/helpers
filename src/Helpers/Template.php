<?php

namespace Helpers;

class Template
{
    private $library;
    private $folder;
    private $smart;

    public function __construct($library = null)
    {
        if($library) {
            $this->setLibrary($library);
        }
    }

    /**
     * @param mixed $library
     */
    public function setLibrary($library)
    {
        $this->library = $library;
    }

    /**
     * @param mixed $folder
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    /**
     * @param string $template
     * @param array $data
     * @return string
    */
    public function getShow(string $template, array $data = null) :string
    {
        return $this->prepareShow($template, $data);
    }

    /**
     * @param string $template
     * @param array $data
     */
    public function show(string $template, array $data = null)
    {
        echo $this->prepareShow($template, $data);
    }

    private function prepareShow(string $template, array $data = null) :string {
        if($this->library) {
            $this->start();

            if ($data)
                $this->setData($data);

            $retorno = $this->smart->fetch($template . ".tpl");

            $this->smart->clearAllAssign();

            return $retorno;
        }

        return "";
    }

    /**
     * @param array $data
    */
    public function setData(array $data)
    {
        foreach ($data as $name => $value) {
            $this->smart->assign($name, $value);
        }
    }

    public function clearData()
    {
        $this->smart->clearAllAssign();
    }

    private function start()
    {
        $this->smart = new \Smarty();
        $this->preData();
        //        $this->smart->caching = true;
        //        $this->smart->cache_lifetime = 120;

        $this->smart->setTemplateDir($this->folder ?? (DEV && $this->library === DOMINIO ? "tpl" : "vendor/conn/{$this->library}/tpl"));
    }

    private function preData()
    {
        $this->smart->assign("datetime", date("d/m/Y H:i:s"));
        $this->smart->assign("date", date("d/m/Y"));
        $this->smart->assign("year", date("Y"));
        $this->smart->assign("hora", date("H:i"));
        $this->smart->assign("dev", defined("DEV") ? DEV : false);
        if(defined('HOME')) $this->smart->assign("home", HOME);
        if(defined('PATH_HOME')) $this->smart->assign("path_home", PATH_HOME);
        if(defined('LOGO')) $this->smart->assign("logo", HOME . LOGO);
        if(defined('FAVICON')) $this->smart->assign("favicon", HOME . FAVICON);
        if(defined('SITENAME')) $this->smart->assign("sitename", SITENAME);
    }
}