<?php

namespace BlogSimple\Entity\Collection;

abstract class PaginatedCollectionAbstract extends CollectionAbstract {

    /**
     * Has Previous Page
     *
     * If the collection has a previous page this will be true
     * 
     * @var boolean
     * @access public
     */
    public $has_previous_page = false;

    /**
     * Has Next Page
     *
     * If the collection has a next page this will be true
     * 
     * @var boolean
     * @access public
     */
    public $has_next_page = false;

    /**
     * Total Entities
     *
     * Holds the total amount of entities that exist
     * 
     * @var int
     * @access public
     */
    public $total_entities;

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



}
