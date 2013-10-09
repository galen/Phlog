<?php

namespace Phlog\Datastore;

use \Phlog\Entity\Collection\PostCollection;
use \Phlog\Entity\Collection\CommentCollection;
use \Phlog\Entity\Collection\AttributeCollection;
use \Phlog\Entity\Comment;
use \Phlog\Entity\Post;
use \Phlog\Entity\Attribute;

Interface DatastoreInterface {

    public function getPostsWithAttributeAndValue( $attribute, $value, $offset, $length, array $where = null );

    public function getPosts( $offset, $length, array $where = null );

    public function getTotalPosts( array $where = null );

    public function getPost( $post_id, array $where = null );

    public function getPreviousPost( $post_id, array $where = null );

    public function getNextPost( $post_id, array $where = null );

    public function getPostComments( $post_id, array $where = null, $offset=0, $length=9999999999 );

    public function getPostAttributes( $post_id );

    public function getPostAttributeValues( $attribute, array $where = null );

    public function addComment( Comment $comment );

    public function addPost( Post $post );

    public function addPostAttribute( Attribute $attribute );

}