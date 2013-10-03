<?php

namespace BlogSimple;

use BlogSimple\Entity\Collection\PostCollection;
use BlogSimple\Entity\Collection\CommentCollection;
use BlogSimple\Entity\Collection\AttributeCollection;
use BlogSimple\Entity\Post;
use BlogSimple\Entity\Comment;

class BlogSimple {

    protected $datastore;
    protected $posts_per_page = 5;

    public function __construct( \BlogSimple\Datastore\DatastoreInterface $datastore ) {
        $this->datastore = $datastore;
    }

    public function setPostsPerPage( $posts_per_page ) {
        $this->posts_per_page = (int) $posts_per_page;
    }

    public function getPosts( $page = 1, array $where = null ) {
        $collection = $this->datastore->getPosts( $this->posts_per_page * ( $page-1 ), $this->posts_per_page, $where );
        if ( !$collection instanceof PostCollection ) {
            throw new \Exception( 'Wrong return type' );
        }
        $total_posts = $this->getTotalPosts( $where );
        $collection->has_next_page = ceil( $total_posts / $this->posts_per_page ) > $page ? true : false;
        $collection->has_previous_page = $page > 1 ? true : false;
        $collection->setTotalPosts( $total_posts );
        return $collection;
    }

    public function getTotalPosts( array $where = null ) {
        return count( $this->datastore->getPosts( 0, 9999999999, $where ) );
    }

    public function organizePostAttributes( AttributeCollection $attribute_collection, $attribute_field, $value_field ) {
        $attributes = array();
        foreach( $attribute_collection as $attribute ) {
            if ( isset( $attributes[$attribute->$attribute_field] ) ) {
                if ( is_string( $attributes[$attribute->$attribute_field] ) ) {
                    $temp = $attributes[$attribute->$attribute_field];
                    $attributes[$attribute->$attribute_field] = array();
                }
                $attributes[$attribute->$attribute_field][] = $attribute->$value_field;
            }
            else {
                $attributes[$attribute->$attribute_field] = $attribute->$value_field;
            }
        }
        return $attributes;
    }

    public function getPostAttributes( $post_id ) {
        $collection = $this->datastore->getPostAttributes( $post_id );
        if ( !$collection instanceof AttributeCollection ) {
            throw new \Exception( 'Wrong return type' );
        }
        return $collection;
    }

    public function addComment( Comment $comment ) {
        return $this->datastore->addComment( $comment );
    }

    public function getPostAttributeValues( $attribute, array $where = null ) {
        return $this->datastore->getPostAttributeValues( $attribute, $where );
    }

    public function getPost( $post_id, array $where = null ) {
        $post = $this->datastore->getPost( $post_id, $where );
        if ( !$post instanceof Post ) {
            throw new \BlogSimple\Exception\InvalidPostException( 'Invalid' );
        }
        return $post;
    }

    public function getPostsWithAttributeAndValue( $attribute, $value, $page, array $where = null ) {
        $collection = $this->datastore->getPostsWithAttributeAndValue( $attribute, $value, ($page-1)*$this->posts_per_page, $this->posts_per_page, $where );
        if ( !$collection instanceof PostCollection ) {
            throw new \Exception( 'Invalid return type' );
        }
        $total_posts = $this->getTotalPostsWithAttributeAndValue( $attribute, $value, $where );
        $collection->has_next_page = ceil( $total_posts / $this->posts_per_page ) > $page ? true : false;
        $collection->has_previous_page = $page > 1 ? true : false;
        $collection->setTotalPosts( $total_posts );
        return $collection;
    }

    public function getTotalPostsWithAttributeAndValue( $attribute, $value, array $where = null ) {
        $collection = $this->datastore->getPostsWithAttributeAndValue( $attribute, $value, 0, 99999999999, $where );
        return count( $collection );
    }

    public function getPostComments( $post_id, $offset=0, $length=9999999999 ) {
        $comments = $this->datastore->getPostComments( $post_id, $offset=0, $length=9999999999 );
        if ( !$comments instanceof CommentCollection ) {
            throw new \Exception( 'Invalid return type' );
        }
        return $comments;
    }

    public function getNextPost( $post_id, array $where = null ) {
        $post = $this->datastore->getNextPost( $post_id, $where );
        return $post;
    }

    public function getPreviousPost( $post_id, array $where = null ) {
        $post = $this->datastore->getPreviousPost( $post_id, $where );
        return $post;
    }

}