<?php

/**
 * View.class [ HELPER MVC ]
 * Reponsável por carregar o template, povoar e exibir a view, povoar e incluir arquivos PHP no sistem.
 * Arquitetura MVC!
 *
 * @copyright (c) 2017, Edinei J. Bauer
 */

namespace Helpers;

class View
{
    private $data;
    private $base;
    private $keys;
    private $values;
    private $lib;

    public function __construct()
    {
        $this->setLib("link-control");
    }

    /**
     * <b>Carregar Template View:</b> Dentro da pasta do seu template, crie a pasta _tpl e armazene as
     * template_views.tpl.html. Depois basta informar o APENAS O NOME do arquivo para carregar o mesmo!
     * @param STRING $template = Nome_do_arquivo
     */
    public function load($template)
    {
        return file_get_contents($this->base . (!preg_match('/\/$/i', $this->base) ? DIRECTORY_SEPARATOR : "") . (string)$template . '.html');
    }

    /**
     * @param string $lib
     */
    public function setLib($lib)
    {
        $this->lib = $lib;
        $this->base = (defined('PATH_HOME') ? PATH_HOME : (defined("HOME") ? HOME : "")) . "vendor/conn/" . $this->lib . "/view/template/";
    }

    /**
     * @param mixed $base
     */
    public function setBase($base)
    {
        $this->base = (defined('PATH_HOME') ? PATH_HOME : (defined("HOME") ? HOME : "")) . $base;
    }

    /**
     * <b>Exibir Template View:</b> Execute um foreach com um getResult() do seu model e informe o envelope
     * neste método para configurar a view. Não esqueça de carregar a view acima do foreach com o método Load.
     * @param array $data = Array com dados obtidos
     * @param View $view = Template carregado pelo método Load()
     */
    public function show($view, array $data = array())
    {
        $this->setKeys($data);
        $this->setValues();
        echo str_replace($this->keys, $this->values, $this->load($view));
    }

    /**
     * <b>Retorna Template View:</b> Execute um foreach com um getResult() do seu model e informe o envelope
     * neste método para configurar a view. Não esqueça de carregar a view acima do foreach com o método Load.
     * @param array $data = Array com dados obtidos
     * @param View $view = Template carregado pelo método Load()
     */
    public function getShow($view, array $data = array())
    {
        $this->setKeys($data);
        $this->setValues();
        return str_replace($this->keys, $this->values, $this->load($view));
    }

    /**
     * <b>Carregar PHP View:</b> Tendo um arquivo PHP com echo em variáveis extraídas, utilize esse método
     * para incluir, povoar e exibir o mesmo. Basta informar o caminho do arquivo<b>.inc.php</b> e um
     * envelope de dados dentro de um foreach!
     * @param STRING $file = Caminho / Nome_do_arquivo
     * @param ARRAY $data = Array com dados obtidos
     */
    public function includeFile($file, array $data = array())
    {
        extract($data);
        require("{$file}.inc.php");
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */

    //Executa o tratamento dos campos para substituição de chaves na view.
    private function setKeys($Data)
    {
        $this->data = $Data;
        $this->setPreData();

        $this->keys = explode('&', '##' . implode("##&##", array_keys($this->data)) . '##');
        $this->keys[] = '##HOME##';
    }

    private function setPreData()
    {
        $this->data['HOME'] = defined('HOME') ? HOME : "";
        $this->data['SITENAME'] = defined('SITENAME') ? SITENAME : "";
        $this->data['LOGO'] = defined('LOGO') ? LOGO : "";
        $this->data['FAVICON'] = defined('FAVICON') ? FAVICON : "";
    }

    //Obtém os valores a serem inseridos nas chaves da view.
    private function setValues()
    {
        $this->values = array_values($this->data);
    }
}
