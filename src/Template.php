<?php

namespace JMasci\ComponentTemplate;

/**
 * Manages a collection of Components which are generally used to output HTML.
 *
 * A Template defines a rendering behaviour for a generic block of HTML,
 * where the rendering logic is split into multiple Components.
 *
 * The important part is that every Component can be re-defined. If
 * instance methods could also be re-defined, then this class would not
 * be necessary.
 *
 * When we invoke Components from a Template, the Template is bound
 * to the Components closure, so that the Component can more easily
 * invoke other Components from within the same Template. This makes
 * Components feel and behave very similarly to instance methods.
 *
 * Note that Components are not functions themselves, they are instead
 * objects which wrap an anonymous function.
 *
 * Example: Let's say we want to render an HTML table. We can create a Template
 * with Component's named: 'table', 'tbody', 'thead', 'tr_head', 'tr_body', 'th',
 * and 'td'. We render the table by invoking 'table'. But first, we may decide
 * to modify one or more components to change the resulting HTML.
 *
 * Class Template
 * @package JMasci\CallableComponentTemplate
 */
Class Template{

    /**
     * @var array Component
     */
    private $components = [];

    /**
     * An optional callback to render "this".
     *
     * Provides extra flexibility in some instances.
     *
     * @var callable|null
     */
    public $renderer;

    /**
     * By default, $this->render() will invoke the component
     * with this name, unless you have setup $this->renderer.
     *
     * @var string
     */
    public $top_level_component_name = "__main__";

    /**
     * The arguments you pass in will tell the Template how it can render itself.
     *
     * However, this is not necessarily relevant for all Templates. It depends
     * on what your Template does.
     *
     * Template constructor.
     * @param null $top_level_component_name - null means no change from default property value. false does not.
     * @param null $renderer
     */
    public function __construct( $top_level_component_name = null, $renderer = null ) {

        if ( $top_level_component_name !== null ) {
            $this->top_level_component_name = $top_level_component_name;
        }

        if ( $renderer ) {
            $this->renderer = $renderer;
        }
    }

    /**
     * Render the template, assuming the template was setup in such
     * a way that this produces some result.
     *
     * Not all templates should be forced to have this produce any results.
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function render( ...$args ) {

        if ( $this->renderer ) {
            return call_user_func( $this->renderer, $this, ...$args );
        }

        return $this->invoke( $this->top_level_component_name, ...$args );
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
        } else {
            // todo: what is best here? silent no-op is going to cause some headache
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

    /**
     * Clone components when $this is cloned.
     */
    public function __clone(){
        foreach ( $this->components as $index => $component ) {
            $this->components[$index] = clone $this->components[$index];
        }
    }
}