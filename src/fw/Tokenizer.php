<?php
/**
 * Created by PhpStorm.
 * User: diego
 * Date: 16/06/18
 * Time: 22:11
 */

namespace MyFw;


class Tokenizer
{
    protected $patterns = [];
    protected $sectionRule = [];
    protected $layoutRule;
    protected $constructRule;

    protected function addRule(string $pattern, string $replacement)
    {
        $this->patterns[] = [$pattern => $replacement];
    }

    protected function setLayoutRule(string $layoutRule)
    {
        $this->layoutRule = $layoutRule;
    }

    protected function getLayoutRule()
    {
        return $this->layoutRule;
    }

    protected function setSectionRule(string $openingTag, string $closingTag)
    {
        $this->sectionRule['openingTag'] = $openingTag;
        $this->sectionRule['closingTag'] = $closingTag;
    }

    protected function getSectionRuleByKey(string $key)
    {
        return $this->sectionRule[$key];
    }

    protected function setConstructRule($constructRule)
    {
        $this->constructRule = $constructRule;
    }

    protected function getConstructRule()
    {
        return $this->constructRule;
    }
}