<?php

namespace JMasci\ComponentTemplate;

/**
 * Class Component
 * @package JMasci\CallableComponentTemplate
 */
Class Component implements I_Component{

    use T_Component;

    /**
     * Component constructor.
     * @param callable $callable
     * @param array $filters
     */
    public function __construct( Callable $callable, array $filters = [] ) {
        $this->callable = $callable;
        $this->filters = $filters;
    }
}