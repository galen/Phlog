<?php

namespace Phlog\Entity;

class Post extends EntityBase {

    /**
     * Get Excerpt
     *
     * Returns a desired number of words from the beginning of a Post field
     * 
     * @param string  $field Field to get the excerpt from
     * @param integer $words Number of words to get
     * @return string Returns the excerpt
     * @access public
     */
    public function getExcerpt( $field = 'text', $words = 20 ) {
        return implode( ' ', array_slice( explode( ' ', $this->$field ), 0, 20 ) );
    }

}