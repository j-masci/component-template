<?php

namespace JMasci\ComponentTemplate;

/**
 * Interface I_Component
 * @package JMasci\CallableComponentTemplate
 */
Interface I_Component{
    public function invoke( Template $template, ...$args );
}