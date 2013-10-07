<?php

namespace BlogSimple\Entity\Collection;

class PostCollection extends PaginatedCollectionAbstract {

    /**
     * Get Total Posts
     * 
     * @return integer Totals posts
     * @access public
     */
    public function getTotalPosts() {
        return $this->getTotalEntities();
    }

    /**
     * Set Total Posts
     * 
     * @param int $total_posts Total posts
     * @access public
     */
    public function setTotalPosts( $total_posts ) {
        $this->setTotalEntities( $total_posts );
    }

}