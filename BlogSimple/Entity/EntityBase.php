<?php

namespace BlogSimple\Entity;

abstract class EntityBase {

    /**
     * Constructor
     *
     * @param StdClass|array $data
     * @access public
     */
    public function __construct( $data = null ) {
        if ( $data ) {
            $this->setData( $data );
        }
    }

    /**
     * Set Data
     * 
     * Set the object data from an array
     * 
     * @param array|StdClass $data Data to set
     * @access public
     */
    public function setData( $data ) {
        foreach( $data as $k => $v ) {
            $this->$k = $v;
        }
    }

}