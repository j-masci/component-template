### Components

A Component is an object which wraps a callable and an array of argument filters which are also callables. It
has an invoke method will which filter the arguments and then invoke the callable. 

The purpose of components becomes more clear in the context of a template, where they serve a similar 
role to class methods except that we can override them after template instantiation.

Note that the behaviour gained from adding argument filters can sometimes be gained by
overriding the callable or by invoking the callable inside of another callable.    

```php
use JMasci\ComponentTemplate\Component;

$comp = new Component( function( $a_number ){
    echo "You passed in: " . (int) $a_number;    
});

// filters are callbacks that accept the same arguments as the callable and returns
// the arguments in a list.
// this is an example of a completely redundant filter.
$comp->set_filter( function( $a_number ){
    return [ $a_number ];
});

// a filter which does not like the number 42
$comp->set_filter( function( $a_number ){
    return [ $a_number === 42 ? 11 : $a_number ];
});

// a filter which doubles
$comp->set_filter( function( $a_number ){
    return[ $a_number * 2 ];
});

// prints 22
// the first parameter of invoke is called $new_this. You can use it to bind an object
// to $this inside of the callable.
echo $comp->invoke( null, 42 );
```

### Template

A template is an object that simply wraps a collection of Components. When you invoke a component
it passes itself as $new_this by default. This makes it easiest for components to invoke other
components within the same template. And makes components more like dynamic (overridable) methods 
of a template.

```php

use JMasci\ComponentTemplate\Template;

// specify the name of the top level component to invoke upon $template->render().
// we can also specify a callback here if needed (as 2nd parameter).
$template = new Template( 'main', $render_callback = null );

// set the main component. Pass in a function but it will store a Component object.
// once again, $this inside the callback will refer to the template by default, but you
// could change this behaviour by extending Template and re defining the invoke method. 
$template->set( 'main', function( $p1, $p2 ){
    ?>
    <div class="example-template">
        <p><?php $this->invoke( 'comp_2', $p1, $p2, 50 ); ?></p>            
    </div> 
    <?php    
});

$template->set( 'comp_2', function( $p1, $p2, $p3 ){    
    echo intval( $p1 + $p2 + $p3 );    
});

// you can override comp_2 at any time.
$template->set( 'comp_2', function( $p1, $p2, $p3 ){    
    echo intval( $p1 + $p2 - $p3 );    
});

$template->get( 'comp_2' )->set_filter( function( $p1, $p2, $p3 ) {
    return [ $p1, 2 * $p2, $p3 ];
});

echo $template->render( 10, 30 );
```

You can do a lot more with templates using a mix of higher order functions and template factories, but this
just shows some basic examples. 