<?php

namespace Phlog\Entity\Collection;

abstract class PaginatedCollectionAbstract extends CollectionAbstract {

    /**
     * Has Previous Page
     *
     * If the collection has a previous page this will be true
     * 
     * @var boolean
     * @access protected
     */
    protected $has_previous_page = false;

    /**
     * Has Next Page
     *
     * If the collection has a next page this will be true
     * 
     * @var boolean
     * @access protected
     */
    protected $has_next_page = false;

    /**
     * Total Entities
     *
     * Holds the total amount of entities that exist
     * 
     * @var int
     * @access public
     */
    protected $total_entities;

    /**
     * Set Total Entities
     *
     * Sets the total amount of entities
     * 
     * @param $total_entitites Total amount of entities
     * @return void
     * @access public
     */
    public function setTotalEntities( $total_entities ) {
        $this->total_entities = (int) $total_entities;
    }

    /**
     * Get Total Entities
     *
     * Get the total amount of entities
     *
     * $post_collection->getTotalEntities(); // Returns the total amount of posts
     * 
     * @return int Returns the total amount of entities
     * @access public
     */
    public function getTotalEntities() {
        return $this->total_entities;
    }

    /**
     * Has next page
     * 
     * @return boolean 
     * @access public
     */
    public function hasNextPage() {
        return $this->has_next_page;
    }

    /**
     * Has previous page
     * 
     * @return boolean 
     * @access public
     */
    public function hasPreviousPage() {
        return $this->has_previous_page;
    }

    /**
     * Set next page
     * 
     * Set the existence of a next page
     * 
     * @param bool $true_or_false
     * @return void
     * @access public
     */
    public function setNextPage( $true_or_false ) {
        $this->has_next_page = (bool) $true_or_false;
    }

    /**
     * Set previous page
     * 
     * Set the existence of a previous page
     * 
     * @param bool $true_or_false
     * @return void
     * @access public
     */
    public function setPreviousPage( $true_or_false ) {
        $this->has_previous_page = (bool) $true_or_false;
    }

}
