<?php

namespace MyFw;


class Tokenizer
{
    protected $patterns = [];
    protected $sectionRule = [];
    protected $layoutRule;
    protected $constructRule;

    /**
     * Adiciona uma regra de substituição
     * para a Template Engine
     *
     * @param string $pattern
     * @param string $replacement
     */
    protected function addRule(string $pattern, string $replacement)
    {
        $this->patterns[] = [$pattern => $replacement];
    }

    /**
     * Seta a regra para extensão de layout
     *
     * @param string $layoutRule
     */
    protected function setLayoutRule(string $layoutRule)
    {
        $this->layoutRule = $layoutRule;
    }

    /**
     * Retorna a regra de extensão de layout
     *
     * @return mixed
     */
    protected function getLayoutRule()
    {
        return $this->layoutRule;
    }

    /**
     * Seta as regras de abertura e fechamento
     * para blocos nas views
     *
     * @param string $openingTag
     * @param string $closingTag
     */
    protected function setSectionRule(string $openingTag, string $closingTag)
    {
        $this->sectionRule['openingTag'] = $openingTag;
        $this->sectionRule['closingTag'] = $closingTag;
    }

    /**
     * Retorna a regra de bloco conforme o parâmetro $key
     *
     * @param string $key
     * @return mixed
     */
    protected function getSectionRuleByKey(string $key)
    {
        return $this->sectionRule[$key];
    }

    /**
     * Seta a regra de construção para substituição de blocos
     * entre view mãe e view filha
     *
     * @param $constructRule
     */
    protected function setConstructRule(string $constructRule)
    {
        $this->constructRule = $constructRule;
    }

    /**
     * Retorna a regra de construção
     *
     * @return mixed
     */
    protected function getConstructRule()
    {
        return $this->constructRule;
    }
}