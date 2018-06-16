<?php

namespace Core;


class View
{
    private $patterns = [];

    public function __construct()
    {
        $this->addRule('/{ ?\$([\w\d]+) ?= ?(.*) ?}/', '<?php $$1 = $2 ?>');
        $this->addRule('/{ ?\$([\w\d]+) ?}/', '<?= $$1 ?>');
        $this->addRule('/{ ?\$([\w\d]+)(\+\+|\-\-) ?}/', '<?php $$1$2 ?>');
        $this->addRule('/@if ?\((.*)\)/', '<?php if($1): ?>');
        $this->addRule('/@elseif ?\((.*)\)/', '<?php elseif($1): ?>');
        $this->addRule('/@else/', '<?php else: ?>');
        $this->addRule('/@for ?\((.*); ?(.*); ?(.*)\)/', '<?php for($1 ; $2; $3): ?>');
        $this->addRule('/@foreach ?\((.*) as (.*)( ?=> ?.*)?\)/', '<?php foreach($1 as $2 $3): ?>');
        $this->addRule('/@while ?\((.*)\)/', '<?php while($1): ?>');
        $this->addRule('/@end(if|foreach|for|while)/', '<?php end$1 ?>');
    }

    private function addRule(string $pattern, string $replacement){
        $this->patterns[] = [$pattern => $replacement];
    }

    public function addFunction(string $function, array $params = []){
        if(function_exists($function)){
            if(empty($params)){
                $this->addRule("/@{$function}\(\)/", "<?php {$function}() ?>");
            } else {
                $this->addRule("/@{$function}\((.*)\)/", "<?php {$function}($1) ?>");
            }
        }
    }

    private function loadTemplate(string $template)
    {
        $templatePath = __DIR__ . "/../app/views/{$template}.phtml";
        if(file_exists($templatePath)){
            return file_get_contents($templatePath);
        } else {
            return false;
        }
    }

    public function render(string $template, array $data = [], string $layout = "")
    {
        $contents = $this->loadTemplate($template);

        if(!empty($data)){
            extract($data);
        }

        foreach ($this->patterns as $pattern) {
            $contents = preg_replace(array_keys($pattern), array_values($pattern), $contents);
        }

        eval(' ?>' . $contents . '<?php ');
    }
}