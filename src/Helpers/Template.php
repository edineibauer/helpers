<?php

namespace FormCrud;

class Template
{
    private $library;
    private $design;
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
     * @param mixed $design
     */
    public function setDesign($design)
    {
        $this->design = $design;
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
    public function show(string $template, array $data = null) : void
    {
        $this->prepareShow($template, $data, true);
    }

    private function prepareShow(string $template, array $data = null, bool $type = false) {
        if($this->library) {
            $this->start();

            if ($data) {
                $this->setData($data);
            }

            if($type) {

                $this->smart->display($template . ".tpl");
                $this->smart->clearAllAssign();
            } else {

                $retorno = $this->smart->fetch($template . ".tpl");
                $this->smart->clearAllAssign();
                return $retorno;
            }
        }
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
        //        $this->smart->caching = true;
        //        $this->smart->cache_lifetime = 120;

        $this->smart->setTemplateDir("vendor/conn/{$this->library}/tpl" . ($this->design ? "_{$this->design}" : ""));
    }
}