<?php

namespace BlogSimple\Datastore;

use \BlogSimple\Entity\Collection\PostCollection;
use \BlogSimple\Entity\Collection\CommentCollection;
use \BlogSimple\Entity\Collection\AttributeCollection;
use \BlogSimple\Entity\Comment;
use \BlogSimple\Entity\Post;
use \BlogSimple\Entity\Attribute;

Interface DatastoreInterface {

    public function getPostsWithAttributeAndValue( $attribute, $value, $offset, $length, array $where = null );

    public function getPosts( $offset, $length, array $where = null );

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