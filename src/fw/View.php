<?php

namespace MyFw;


class View extends Tokenizer
{
    private $layout;
    private $blocks = [];
    private static $instance;

    /**
     * Atribui as regras inicias da Template Engine
     *
     * View constructor.
     */
    private function __construct()
    {
        /*
         * Engine: { $varname = value }
         * PHP: <?php $varname = value ?>
         */
        $this->addRule('/{ ?\$([\w\d]+) ?= ?(.*) ?}/', '<?php $$1 = $2 ?>');
        /*
         * Engine: { $varname }
         * PHP: <?= $varname ?>
         */
        $this->addRule('/{ ?\$([\w\d]+) ?}/', '<?= $$1 ?>');
        /*
         * Engine: { $varname++ } or { $varname-- }
         * PHP: <?php $varname++ ?> or <?php $varname-- ?>
         */
        $this->addRule('/{ ?\$([\w\d]+)(\+\+|\-\-) ?}/', '<?php $$1$2 ?>');
        /*
         * Engine: { $varname.0 }
         * PHP: <?= $varname[0] ?>
         */
        $this->addRule('/{ ?\$([\w\d]+)\.(\d*) ?}/', '<?= $$1[$2] ?>');
        /*
         * Engine: { $varname.index }
         * PHP: <?= $varname['index'] ?>
         */
        $this->addRule('/{ ?\$([\w\d]+)\.(\w*) ?}/', "<?= $$1['$2'] ?>");
        /*
         * Engine: { $varname->property }
         * PHP: <?= $varname->property }
         */
        $this->addRule('/{ ?\$([\w\d]+)->(.*) ?}/', "<?= $$1->$2 ?>");
        /*
         * Engine: @if (condition)
         * PHP: <?php if(condition): ?>
         */
        $this->addRule('/@if ?\((.*)\)/', '<?php if($1): ?>');
        /*
         * Engine: @elseif (condition)
         * PHP: <?php elseif(condition): ?>
         */
        $this->addRule('/@elseif ?\((.*)\)/', '<?php elseif($1): ?>');
        /*
         * Engine: @else
         * PHP: <?php else: ?>
         */
        $this->addRule('/@else/', '<?php else: ?>');
        /*
         * Engine: @for($i = 0; $i < 10; $i++)
         * PHP: <?php for($i = 0; $i < 10; $i++): ?>
         */
        $this->addRule('/@for ?\((.*); ?(.*); ?(.*)\)/', '<?php for($1 ; $2; $3): ?>');
        /*
         * Engine: @foreach($iterable as $item) or @foreach($iterable as $key => $value)
         * PHP: <?php foreach($iterable as $item): ?> or <?php foreach($iterable as $key => $value): ?>
         */
        $this->addRule('/@foreach ?\((.*) as (.*)( ?=> ?.*)?\)/', '<?php foreach($1 as $2 $3): ?>');
        /*
         * Engine: @while(condition)
         * PHP: <?php while(condition): ?>
         */
        $this->addRule('/@while ?\((.*)\)/', '<?php while($1): ?>');
        /*
         * Engine: @end(if|foreach|for|while)
         * PHP: <?php end(if|foreach|for|while) ?>
         */
        $this->addRule('/@end(if|foreach|for|while)/', '<?php end$1 ?>');

        $this->setLayoutRule("/@extends ?\('(.*)'\)/");
        $this->setSectionRule("/@section ?\('(.*)'\)/", '/@endsection/');
        $this->setConstructRule("/@yield ?\('(.*)'\)/");
    }

    /**
     * Retorna um instância da classe View
     *
     * @return View
     */
    public static function getInstance(): View{
        if (!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class();
        }

        return self::$instance;
    }

    /**
     * Retorna o valor do atributo layout
     *
     * @return string
     */
    private function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Verifica se uma view extende de outra.
     * Caso verdade seta o nome da view para o atributo layout
     *
     * @param string $view
     */
    private function setLayout(string $view): void
    {
        $file = fopen($view, 'r');

        while (!feof($file)) {
            $line = fgets($file);

            if (preg_match($this->getLayoutRule(), $line, $matches)) {
                $this->layout = __DIR__ . "/../app/views/layout/{$matches[1]}.phtml";
                break;
            }
        }

        fclose($file);
    }

    /**
     * Retorna o valor do atributo blocks
     *
     * @return array
     */
    private function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * Faz toda a compição(substituição de regras) do arquivo de view,
     * e retorna toda a view compilada
     *
     * @param string $view
     * @return string
     */
    private function compile(string $view): string
    {
        $tempView = $view;

        foreach ($this->patterns as $pattern) {
            $tempView = preg_replace(array_keys($pattern), array_values($pattern), $tempView);
        }

        return $tempView;
    }

    /**
     * Busca na view blocos para serem substituidos na view pai
     * e atribui para o atributo blocks
     *
     * @param string $view
     */
    private function setBlocks(string $view): void
    {
        $file = fopen($view, 'r');

        $sectionName = '';
        $contentBlocks = [];
        while (!feof($file)) {
            $line = fgets($file);

            if (preg_match($this->getSectionRuleByKey('openingTag'), $line, $matches)) {
                $sectionName = $matches[1];
            }

            $contentBlocks[$sectionName] .= $line;

            if (preg_match($this->getSectionRuleByKey('closingTag'), $line, $matches)) {
                $sectionName = '';
            }
        }

        $contentBlocks = array_filter($contentBlocks, function ($key) {
            return !empty($key);
        }, ARRAY_FILTER_USE_KEY);

        $newBlocks = [];
        foreach ($contentBlocks as $blockName => $block) {
            preg_match("/@section\('(.*)'\)((.|\n)*)@endsection/", $block, $matches);
            $newBlocks[$blockName] = trim($matches[2]);
        }

        fclose($file);
        $this->blocks = $newBlocks;
    }

    /**
     * Faz o merge da view pai com a view mãe
     *
     * @param string $layoutName
     * @return string
     */
    private function compileLayout(string $layoutName): string
    {
        $file = fopen($layoutName, 'r');
        $result = '';
        while (!feof($file)) {
            $line = fgets($file);

            preg_match($this->getConstructRule(), $line, $matches);
            if (array_key_exists($matches[1], $this->getBlocks())) {
                $line = preg_replace($this->getConstructRule(), $this->getBlocks()[$matches[1]], $line);
            } else {
                $line = preg_replace($this->getConstructRule(), "", $line);
            }

            $result .= $line;
        }
        fclose($file);
        return $result;
    }

    /**
     * Registra uma função personalizado
     * atribuida pelo usuário
     *
     * @param callable $functionName
     */
    public function registerFunction(Callable $functionName): void{
        $pattern = '/@'.$functionName.'\((.*)\)/';
        $replace = '<?php '.$functionName.'($1) ?>';
        $this->addRule($pattern, $replace);
    }

    /**
     * Renderiza uma view passada pelo parâmetro $view
     * e caso $data não seja vazia, passa os valores para a view
     *
     * @param string $view
     * @param array $data
     * @throws \ErrorException
     */
    public function render(string $view, array $data = []): void
    {

        if (!empty($data)) {
            extract($data);
        }

        $view = __DIR__ . "/../../app/views/{$view}.phtml";
        $this->setLayout($view);

        if ($this->layout != null) {
            $this->setBlocks($view);
            $content = $this->compile($this->compileLayout($this->getLayout()));
        } else {
            $viewContent = file_get_contents($view);
            $content = $this->compile($viewContent);
        }

        try{
            eval('?>'.$content.'<?php');
        } catch (\ErrorException | \Error $e) {
            throw new \ErrorException($e->getMessage(), $e->getCode(), E_ERROR, $view, $e->getLine());
        }

    }

    /**
     * Previne a clonagem dessa instância da classe
     */
    private function __clone()
    {
    }

    /**
     * Previne a desserialização da instância dessa classe
     */
    private function __wakeup()
    {
    }

}