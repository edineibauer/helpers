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
    private $template;

    public function __construct()
    {
        $this->base = (defined('HOME') ? HOME . DIRECTORY_SEPARATOR : "") . "vendor" . DIRECTORY_SEPARATOR;
    }

    /**
     * <b>Carregar Template View:</b> Dentro da pasta do seu template, crie a pasta _tpl e armazene as
     * template_views.tpl.html. Depois basta informar o APENAS O NOME do arquivo para carregar o mesmo!
     * @param STRING $Template = Nome_do_arquivo
     */
    public function load($Template)
    {
        $this->template = file_get_contents($this->base . DIRECTORY_SEPARATOR . (string)$Template . '.html');
        return $this->template;
    }

    /**
     * @param mixed $base
     */
    public function setBase($base)
    {
        $this->base = HOME . DIRECTORY_SEPARATOR . $base;
    }

    /**
     * <b>Exibir Template View:</b> Execute um foreach com um getResult() do seu model e informe o envelope
     * neste método para configurar a view. Não esqueça de carregar a view acima do foreach com o método Load.
     * @param array $Data = Array com dados obtidos
     * @param View $View = Template carregado pelo método Load()
     */
    public function show(array $Data, $View)
    {
        $this->setKeys($Data);
        $this->setValues();
        $this->showView($View);
    }

    public function retornaView(array $Data, $View)
    {
        $this->setKeys($Data);
        $this->setValues();

        $this->template = $View;
        return str_replace($this->keys, $this->values, $this->template);
    }

    /**
     * <b>Retorna Template View:</b> Execute um foreach com um getResult() do seu model e informe o envelope
     * neste método para configurar a view. Não esqueça de carregar a view acima do foreach com o método Load.
     * @param array $Data = Array com dados obtidos
     * @param View $View = Template carregado pelo método Load()
     */
    public function retorna(array $Data, $View)
    {
        $this->setKeys($Data);
        $this->setValues();
        $this->template = $View;
        return str_replace($this->keys, $this->values, $this->template);
    }

    /**
     * <b>Carregar PHP View:</b> Tendo um arquivo PHP com echo em variáveis extraídas, utilize esse método
     * para incluir, povoar e exibir o mesmo. Basta informar o caminho do arquivo<b>.inc.php</b> e um
     * envelope de dados dentro de um foreach!
     * @param STRING $File = Caminho / Nome_do_arquivo
     * @param ARRAY $Data = Array com dados obtidos
     */
    public function request($File, array $Data)
    {
        extract($Data);
        require("{$File}.inc.php");
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
    }

    //Obtém os valores a serem inseridos nas chaves da view.
    private function setValues()
    {
        $this->values = array_values($this->data);
    }

    //Exibe o template view com echo!
    private function showView($View)
    {
        $this->template = $View;
        echo str_replace($this->keys, $this->values, $this->template);
    }

}
