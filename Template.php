<?php

namespace JMasci\ComponentTemplate;

/**
 * A template is a collection of components. Normally, components
 * will invoke other components from within the same template. For
 * this reason, the Template is bound as the new $this inside
 * of the components callback when it is invoked. It is however
 * possible to use Components outside of the context of a template,
 * since at that point they are not much more than just a generic
 * callable function.
 *
 * It can be helpful to think of Component's as the methods of a template,
 * except that they can be overriden.
 *
 * Class Template
 * @package JMasci\CallableComponentTemplate
 */
Class Template{

    private $components = [];

    /**
     * Clone components when $this is cloned.
     */
    public function __clone(){
        foreach ( $this->components as $index => $component ) {
            $this->components[$index] = clone $this->components[$index];
        }
    }

    /**
     * Invokes a component by name and binds $this (the template)
     * as $this inside the components callback.
     *
     * When you do not want this behaviour, you can get the component
     * yourself and invoke it directly, which gives you control over
     * the $new_this parameter.
     *
     * Note that we bind $this so that the Component can invoke
     * other components (in a very easy way, by default), but there
     * are many other ways in which you can make it possible for your
     * components to do this.
     *
     * @param $name
     * @param mixed ...$args
     * @return mixed
     */
    public function invoke( $name, ...$args ){
        if ( $this->exists( $name ) ) {
            return $this->get( $name )->invoke( $this, ...$args );
        }
    }

    /**
     * Set a component by name. Pass in a Component instance
     * or an anon function.
     *
     * @param $name
     * @param $component
     */
    public function set( $name, $component ){
        if ( $component instanceof I_Component ) {
            $this->components[$name] = $component;
        } else if ( is_object( $component ) && is_callable( $component ) ) {
            $this->components[$name] = new Component( $component );
        }
    }

    /**
     * Returns a Component which is an object, ie. a reference to an object, so,
     * you should decide whether you want to clone the object or return a reference to it.
     *
     * @param $name
     * @param bool $clone
     * @return Component|null
     */
    public function get( $name, $clone = false ) {
        return $this->exists( $name ) ? ( $clone ? clone $this->components[$name] : $this->components[$name] ) : null;
    }

    /**
     * True if a component exists, by name.
     *
     * @param $name
     * @return bool
     */
    public function exists( $name ) {
        return isset( $this->components[$name] );
    }

    /**
     * Delete a component by name.
     *
     * @param $name
     */
    public function delete( $name ) {
        unset( $this->components[$name] );
    }
}