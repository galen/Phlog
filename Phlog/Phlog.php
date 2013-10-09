<?php

namespace Phlog;

use Phlog\Entity\Collection\PostCollection;
use Phlog\Entity\Collection\CommentCollection;
use Phlog\Entity\Collection\AttributeCollection;
use Phlog\Entity\Post;
use Phlog\Entity\Comment;
use Phlog\Entity\Attribute;

class Phlog {

    /**
     * Datastore
     * 
     * @var DatastoreInterface
     * @access protected
     */
    protected $datastore;

    /**
     * Default posts per page
     * 
     * @var integer
     * @access protected
     */
    protected $posts_per_page = 5;

    /**
     * Constructor
     * 
     * @param DatastoreInterface $datastore Datastore
     * @access public
     */
    public function __construct( \Phlog\Datastore\DatastoreInterface $datastore ) {
        $this->datastore = $datastore;
    }

    /**
     * Set posts per page
     * 
     * @param integer $posts_per_page Posts per page
     * @access public
     */
    public function setPostsPerPage( $posts_per_page ) {
        $this->posts_per_page = (int) $posts_per_page;
    }

    /**
     * Get Posts
     *
     * Get posts matching
     * 
     * @param int $page Page to get
     * @param array $where Array of field => value pairs to add to the query
     * @return PostCollection
     * @access public
     */
    public function getPosts( $page = 1, array $where = null ) {
        $collection = $this->datastore->getPosts( $this->posts_per_page * ( (int)$page-1 ), $this->posts_per_page, $where );
        if ( !$collection instanceof PostCollection ) {
            throw new \Exception( 'Wrong return type' );
        }
        $total_posts = $this->getTotalPosts( $where );
        $collection->has_next_page = ceil( $total_posts / $this->posts_per_page ) > $page ? true : false;
        $collection->has_previous_page = $page > 1 ? true : false;
        $collection->setTotalPosts( $total_posts );
        return $collection;
    }

    /**
     * Get Total Posts
     *
     * Get number of total posts
     * 
     * @param array $where Array of field => value pairs to add to the query
     * @return int
     * @access public
     */
    public function getTotalPosts( array $where = null ) {
        return (int) $this->datastore->getTotalPosts( $where );
    }

    /**
     * Add Post
     *
     * Add a post to the datastore
     * 
     * @param Post $post Post to add
     * @access public
     */
    public function addPost( Post $post ) {
        return $this->datastore->addPost( $post );
    }

    /**
     * Organize Post Attributes
     *
     * Organize post attributes in an array keyed by the attribute
     * 
     * @param AttributeCollection $attribute_collection
     * @param string $attribute_field Field name of the attribute
     * @param string $value_field Field name of the value
     * @return array Array of attributes
     * @access public
     */
    public function organizePostAttributes( AttributeCollection $attribute_collection, $attribute_field, $value_field ) {
        $attributes = array();
        foreach( $attribute_collection as $attribute ) {
            if ( isset( $attributes[$attribute->$attribute_field] ) ) {
                $attributes[$attribute->$attribute_field][] = $attribute->$value_field;
            }
            else {
                $attributes[$attribute->$attribute_field] = array( $attribute->$value_field );
            }
        }
        return $attributes;
    }

    /**
     * Get Post Attributes
     *
     * Get all attributes associated with a post
     * 
     * @param int $post_id
     * @return AttributeCollection
     * @access public
     */
    public function getPostAttributes( $post_id ) {
        $collection = $this->datastore->getPostAttributes( $post_id );
        if ( !$collection instanceof AttributeCollection ) {
            throw new \Exception( 'Wrong return type' );
        }
        return $collection;
    }

    /**
     * Add a post attribute
     * 
     * @param Attribute $attribute 
     * @return int 0 or 1, fail or success
     * @access public
     */
    public function addPostAttribute( Attribute $attribute ) {
        return $this->datastore->addPostAttribute( $attribute );        
    }

    /**
     * Add comment
     * 
     * @param Comment $comment [description]
     * @return int 0 or 1, fail or success
     * @access public
     */
    public function addComment( Comment $comment ) {
        return $this->datastore->addComment( $comment );
    }

    /**
     * Get all values for an attribute
     * 
     * @param string $attribute Attribute e.g. 'tag'
     * @return array
     * @access public
     */
    public function getPostAttributeValues( $attribute, array $where = null ) {
        return $this->datastore->getPostAttributeValues( $attribute, $where );
    }

    /**
     * Get Post
     *
     * Get a post from the posts table
     *
     * Retrieves all fields from the posts table with a primary key of $post_id
     * 
     * @param int $post_id Id of the post
     * @return Post
     * @access public
     */
    public function getPost( $post_id, array $where = null ) {
        $post = $this->datastore->getPost( $post_id, $where );
        if ( !$post instanceof Post ) {
            throw new \Phlog\Exception\InvalidPostException( 'Invalid' );
        }
        return $post;
    }

   /**
     * Get Posts With Attribute
     *
     * This searches the attributes table for posts that match $attribute, $value
     * 
     * @param string $entity_name Entity name e.g. Post
     * @param string $attribute Attribute e.g. Tag
     * @param string $value Value e.g. PHP (for posts tagged PHP)
     * @param int $page Page to get
     * @return PostCollection
     * @access public
     */
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

   /**
     * Get Total Posts With Attribute and Value
     * 
     * @param string $entity_name Entity name e.g. Post
     * @param string $attribute Attribute e.g. Tag
     * @param string $value Value e.g. PHP (for posts tagged PHP)
     * @return int Number of posts with attribute and value
     * @access public
     */
    public function getTotalPostsWithAttributeAndValue( $attribute, $value, array $where = null ) {
        $collection = $this->datastore->getPostsWithAttributeAndValue( $attribute, $value, 0, 99999999999, $where );
        return count( $collection );
    }

    /**
     * Get Post Comments
     *
     * Get all post comments
     * 
     * @param int $post_id Post Id
     * @param int $offset Offset to use in the limit clause (default 0 to get all comments)
     * @param int $length Number of posts to get (default 999.. to get all comments)
     * @return CommentCollection
     * @access public
     */
    public function getPostComments( $post_id, array $where = null, $offset=0, $length=9999999999 ) {
        $comments = $this->datastore->getPostComments( $post_id, $where, $offset=0, $length=9999999999 );
        if ( !$comments instanceof CommentCollection ) {
            throw new \Exception( 'Invalid return type' );
        }
        return $comments;
    }

    /**
     * Get Next Post
     *
     * Get the next post from the posts table
     * 
     * @param int $post_id Id of the post
     * @param array $where Array of field => value pairs to add to the query
     * @return Post
     * @access public
     */
    public function getNextPost( $post_id, array $where = null ) {
        return $this->datastore->getNextPost( $post_id, $where );
    }

    /**
     * Get Previous Post
     *
     * Get the previous post from the posts table
     * 
     * @param int $post_id Id of the post
     * @param array $where Array of field => value pairs to add to the query
     * @return Post
     * @access public
     */
    public function getPreviousPost( $post_id, array $where = null ) {
        return $this->datastore->getPreviousPost( $post_id, $where );
    }

}