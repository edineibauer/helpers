<?php

namespace Helpers;

class Date
{
    private $data;
    private $certeza = array("dia" => false, "mes" => false, "ano" => false);
    private $position;
    private $meses;
    private $erro;

    public function __construct()
    {
        $this->meses[0] = ["janeiro", "fevereiro", "março", "abril", "maio", "junho", "julho", "agosto", "setembro", "outubro", "novembro", "dezembro"];
        $this->meses[1] = ["january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"];
        $this->meses[2] = $this->reduzDateFormat($this->meses[0]);
        $this->meses[3] = $this->reduzDateFormat($this->meses[1]);
    }

    /**
     * Recebe um date em formato aleatório e retorna um date no padrão informado ou que esta por padrão
     * @param string $date
     * @param string $pattern
     * @return string
     */
    public function getData(string $date, string $pattern = "Y-m-d"):?string
    {
        if (!$date) {
            $this->data = date($pattern);
        } else {
            $this->prepareDate($date);
        }

        return $this->erro ? null : str_replace(array('Y', 'm', 'd'), array($this->data['ano'], $this->data['mes'], $this->data['dia']), $pattern);
    }

    /**
     * @return string
     */
    public function getErro(): string
    {
        return $this->erro;
    }

    /**
     * Reduz as strings de uma lista em strings de até 3 caracteres
     * @param array $date
     * @return array
     */
    private function reduzDateFormat(array $date): array
    {
        $data = [];
        foreach ($date as $item) {
            $data[] = substr($item, 0, 3);
        }
        return $data;
    }

    /**
     * Particiona a data recebida para analisar qual informação diz respeito ao dia, mês e ano
     * @param string $date
     */
    private function prepareDate(string $date)
    {
        foreach (preg_split("/\W/i", $date) as $i => $dado) {
            $this->getDatePart($i, $dado);
        }

        $this->checkMonthEdge();
    }

    /**
     * Verifica dados básicos da informação, e manda para verificações mais precisas
     * @param int $i
     * @param string $dado
     */
    private function getDatePart(int $i, string $dado)
    {
        if (!is_numeric($dado)) {
            $dado = (string)$dado;
            $this->checkNameMonth($dado, $i);

        } else {
            $dado = (int)$dado;

            if ($dado > 31) {
                $this->setAno($dado, $i);

            } elseif ($dado > 0) {
                $this->checkWhichPart($dado, $i);
            }
        }
    }

    /**
     * Verifica Se a string encontrada diz respeito a um mês do calendário
     * @param int $i
     * @param string $dado
     */
    private function checkNameMonth(string $dado, int $i)
    {
        foreach ($this->meses as $mes) {
            if (in_array($dado, $mes)) {
                $this->setMes(array_search($dado, $mes) + 1, $i);
                break;
            }
        }
    }

    /**
     * filtra para determinar onde aplicar este valor inteiro.
     * @param int $position
     * @param int $dado
     */
    private function checkWhichPart(int $dado, int $position)
    {
        if ($dado > 12) {
            $current = $this->setDateInfo('dia', 'ano');
            if ($current) {
                $this->data[$current] = $dado;
                $this->position[$current] = $position;
            }
        } else {
            $current = $this->setDateInfo('mes', 'dia');
            if ($current) {
                $this->data[$current] = $dado;
                $this->position[$current] = $position;
            }
        }
    }

    /**
     * seta a informação de mês e verifica se já possui um dado anterior,
     * se tiver, verifica a possibilidade de passar para outro setor.
     * @param int $position
     * @param int $mes
     */
    private function setMes(int $mes, int $position)
    {
        if (!$this->certeza['ano']) {
            if (isset($this->data['mes'])) {
                $current = $this->setDateInfo('dia', 'ano');
                if ($current) {
                    $this->data[$current] = $this->data['mes'];
                    $this->position[$current] = $this->position['mes'];
                }
            }
            $this->data['mes'] = $mes;
            $this->certeza['mes'] = true;
            $this->position['mes'] = $position;
        }
    }

    /**
     * seta a informação de ano e verifica se já possui um dado anterior,
     * se tiver, verifica a possibilidade de passar para outro setor.
     * @param int $position
     * @param int $ano
     */
    private function setAno($ano, $position)
    {
        if (!$this->certeza['ano']) {
            $this->certeza['ano'] = true;

            if (isset($this->data['ano']) && $this->data['ano'] < 32) {
                $this->checkWhichPart($this->data['ano'], $this->position['ano']);
            }

            $this->data['ano'] = $ano;
            $this->position['ano'] = $position;
        }
    }

    /**
     * Verifica qual parametro (dia, mes, ano) é mais preferível receber o valor encontrado,
     * $param1 > $param2 > $param3
     * @param string $param1
     * @param string $param2
     * @param string $param3
     * @return string
     */
    private function setDateInfo(string $param1, string $param2, ?string $param3 = null) :?string
    {
        if (!$this->certeza[$param1] && !isset($this->data[$param1])) {
            return $param1;

        } elseif (!$this->certeza[$param2] && !isset($this->data[$param2])) {
            return $param2;

        } elseif (isset($param3) && !$this->certeza[$param3] && !isset($this->data[$param3])) {
            return $param3;

        } elseif (!$this->certeza[$param1]) {
            return $param1;

        } elseif (!$this->certeza[$param2]) {
            return $param2;

        } elseif (isset($param3) && !$this->certeza[$param3]) {
            return $param3;

        }

        return null;
    }

    /**
     * Ajusta posição do mês, caso este tenha sido encontrado na ponta da data passada,
     * verifica a possibilidade de trocar a informação do mês com uma das pontas (dia ou ano)
     * se for possível, ele troca a informação, mantendo o campo mês, no meio da data passada.
    */
    private function checkMonthEdge()
    {
        asort($this->position);
        $this->position = array_keys($this->position);

        if ((isset($this->position[0]) && $this->position[0] === "mes") || (isset($this->position[2]) && $this->position[2] === "mes")) {
            $n = $this->position[1];
            if (!$this->certeza['mes'] && !$this->certeza[$n] && $this->data[$n] < 13 && $this->data[$n] > 0) {
                $temp = $this->data['mes'];
                $this->data['mes'] = $this->data[$n];
                $this->data[$n] = $temp;
            }
        }
    }
}