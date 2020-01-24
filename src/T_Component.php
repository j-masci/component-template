<?php

namespace JMasci\ComponentTemplate;

/**
 * Trait T_Component
 * @package JMasci\CallableComponentTemplate
 */
Trait T_Component{

    /**
     * Array of callables that filter the arguments passed to
     * $this->invoke() before being passed to $this->callable.
     *
     * Adding a filter is sometimes (but not always) a good way to
     * modify the behaviour of a component while re-using an existing
     * callback. You can often achieve the same thing with a few
     * more lines of code by wrapping $this->callable in another
     * callable.
     *
     * @var array
     */
    private $filters = [];

    /**
     * An anonymous function that renders the component or otherwise
     * does whatever it is that the component does.
     *
     * @var callable
     */
    private $callable;

    /**
     * Exists in case you need to create a new component while copying
     * the callable from another component. Note: you could also clone it
     * and then delete the filters.
     *
     * @return null
     */
    public function get_callable(){
        return $this->callable;
    }

    /**
     * @param callable $filter
     * @param null $priority
     */
    public function set_filter( callable $filter, $priority = null ) {
        if ( $priority === null ) {
            $this->filters[] = $filter;
        } else {
            $this->filters[$priority] = $filter;
        }
    }

    /**
     * @param $priority
     * @return mixed|null
     */
    public function get_filter( $priority ) {
        return isset( $this->filters[$priority] ) ? $this->filters[$priority] : null;
    }

    /**
     * @param $priority
     */
    public function delete_filter( $priority ){
        unset( $this->filters[$priority] );
    }

    /**
     * @param $priority
     * @return bool
     */
    public function filter_exists( $priority ) {
        return isset( $this->filters[$priority] );
    }

    /**
     *
     */
    public function clear_filters(){
        $this->filters = [];
    }

    /**
     * Invoke a component and possibly bind a $new_this to $this inside
     * of the components callable.
     *
     * @param null $new_this - $this inside of the callable.
     * @param mixed ...$args
     * @return mixed
     */
    public function invoke( $new_this = null, ...$args ){

        foreach ( $this->filters as $filter ) {
            $args = call_user_func( $filter, ...$args );
        }

        if ( $this->callable ) {

            if ( $new_this || is_object( $new_this ) ) {
                $this->callable = \Closure::bind( $this->callable, $new_this );
            }

            return call_user_func( $this->callable, ...$args );
        }
    }
}