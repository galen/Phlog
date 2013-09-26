<?php

namespace BlogSimple\Entity;

class Post extends EntityBase {

    /**
     * Get Excerpt
     *
     * Returns a desired number of words from the beginning of a Post field
     * 
     * @param string  $field Field to get the excerpt from
     * @param integer $words Number of words to get
     * @return string Returns the excerpt
     */
    public function getExcerpt( $field = 'text', $words = 20 ) {
        return implode( ' ', array_slice( explode( ' ', $this->$field ), 0, 20 ) );
    }

    /**
     * Get Slug
     *
     * Returns a slug of a field
     * 
     * @param string $field Field to get the slug from
     * @param string $space What to replace spaces with
     * @return string Returns the slug
     */
    public function getSlug( $field = 'title', $space = '-' ) {
        return preg_replace( sprintf( '~-$~', $space ) , '', preg_replace( '~[^a-z0-9]+~i', $space, strtolower( $this->$field ) ) );
    }

}