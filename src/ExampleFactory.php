<?php

namespace JMasci\ComponentTemplate;

/**
 * Class ExampleFactory
 */
Class ExampleFactory{

    /**
     * HTML Table without the thead.
     *
     * @return Template
     */
    public static function table_headless(){

        $template = clone self::table_1();

        $template->set( 'table', self::get_table_renderer( 'table-headless' ) );

        // do nothing
        $template->set( 'thead', function( $table ){});

        return $template;
    }

    public static function get_table_renderer( $class ) {
        return function( $table ) use( $class ) {
            echo '<table class="' . htmlspecialchars( $class ) . '">';
            $this->invoke( 'thead', $table );
            $this->invoke( 'tbody', $table );
            echo '</table>';
        };
    }

    /**
     * @return Template
     */
    public static function table_1(){

        $template = new Template();

        $template->set( 'table', self::get_table_renderer( 'table-1' ) );

        $template->set( 'thead', function( $table ) {
            echo '<thead>';
            echo '<tr>';
            foreach ( $table->cols as $col_index => $col_label ) {
                $this->invoke( 'th', $table, $col_index );
            }
            echo '</tr>';
            echo '</thead>';
        });

        $template->set( 'th', function( $table, $index ) {
            $value = $table->cols[$index];
            $class = 'col-' . $index;
            echo '<td class="' . esc_attr( $class ) . '">' . htmlspecialchars( $value ) . '</td>';
        });

        $template->set( 'tbody', function( $table ) {
            echo '<tbody>';
            foreach ( $table->rows as $row_index => $row ) {
                $this->invoke( 'tbody_row', $table, $row_index );
            }
            echo '</tbody>';
        });

        $template->set( 'tbody_row', function( $table, $row_index ) {
            echo '<tr>';
            foreach ( $table->cols as $col_index => $col_label ) {
                // pass indexes only
                $this->invoke( 'td', $table, $row_index, $col_index );
            }
            echo '</tr>';
        });

        // accepts indexes, not the value it needs to render.
        $template->set( 'td', function( $table, $row_index, $col_index ) {
            // note: $col_index is not guaranteed to bet set, unlike $row_index.
            $value = is_scalar( @$table[$row_index][$col_index] ) ? @$table[$row_index][$col_index] : "";
            $class = 'col-' . $col_index;

            echo '<td class="' . htmlspecialchars( $class ) . '">' . htmlspecialchars( $value ) . '</td>';
        });

        return $template;
    }
}