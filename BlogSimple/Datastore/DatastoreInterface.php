<?php

namespace BlogSimple\Datastore;

Interface DatastoreInterface {
/*
    public function getPost( $post_id );

    public function getPostComments( $post_id, $offset=0, $length=18446744073709551615, array $where = null );

    public function addComment( \Devblog\Entity\Comment $comment );
*/
    public function getPosts( $offset, $length, array $where = null );
/*
    public function getEntitiesWithAttributeAndValue( $entity_name, $attribute, $value, $offset, $length );

    public function getEntityNameAttributeValues( $entity_name, $attribute, $sort_dir = 'desc' );

    public function exists();

    public function create();
*/
    /*public function addPost( \Devblog\Entity\Post $post );*/



    /*public function getEntityAttributes( $entity_name, $entity_id );*/



}