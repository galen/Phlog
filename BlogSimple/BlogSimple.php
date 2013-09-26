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

    public function getPagination( $current_page, $total_items, $items_per_page, $pagination_viewport ) {

        $total_pages = ceil( $total_items / $items_per_page );
        $start_page = max( $current_page - $pagination_viewport, 1 );
        $end_page = min( $start_page + ( $pagination_viewport * 2 ), $total_pages );
        $start_page = max( $end_page - ( $pagination_viewport * 2 ), 1 );
        $pages = range( $start_page, $end_page );
         
        $first_page_link = $last_page_link = false;
        if ( !in_array( 1, $pages ) ) {
            $first_page_link = true;
        }
        if ( !in_array( $total_pages, $pages ) ) {
            $last_page_link = true;
        }
         
        $previous_page = $current_page == 1 ? null : $current_page - 1;
        $next_page = $current_page == $total_pages ? null : $current_page + 1;
        return (object) array(
            'current_page' => $current_page,
            'pages' => $pages,
            'previous_page' => $previous_page,
            'next_page' => $next_page,
            'total_pages' => $total_pages,
            'first_page_link' => $first_page_link,
            'last_page_link' => $last_page_link
        );

    }

}