<?php

namespace Phlog\Entity\Collection;

abstract class CollectionAbstract implements \IteratorAggregate, \ArrayAccess, \Countable {

    /**
     * Collection
     *
     * Array of entity objects
     * 
     * @var array
     * @access public
     */
    public $collection = array();

    /**
     * Constructor
     * 
     * @param array $collection Array of entities
     * @access public
     */
    public function __construct( array $collection ) {
        $this->setCollection( $collection );
    }

    /**
     * Set Collection
     * 
     * @param array $collection Array of entities
     * @return void
     * @access public
     */
    public function setCollection( array $collection ) {
        $this->collection = $collection;
    }


    /**
     * IteratorAggregate
     *
     * {@link http://us2.php.net/manual/en/class.iteratoraggregate.php}
     */
    public function getIterator(){
        return new \ArrayIterator( $this->collection );
    }

    /**
     * ArrayAccess
     *
     * {@link http://us2.php.net/manual/en/class.arrayaccess.php}
     */
    public function offsetExists( $offset ) {
        return isset( $this->collection[$offset] );
    }
    public function offsetGet( $offset ) {
        return $this->collection[$offset];
    }
    public function offsetSet( $offset, $value ) {
        trigger_error( "You can't set collection data");
    }
    public function offsetUnset( $offset ) {
        trigger_error( "You can't unset collection data" );
    }

    /**
     * Countable
     * 
     * {@link http://us2.php.net/manual/en/class.countable.php}
     */
    public function count() {
        return count( $this->collection );
    }

}