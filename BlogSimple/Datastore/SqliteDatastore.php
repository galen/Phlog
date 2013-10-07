<?php

namespace BlogSimple\Datastore;

use \BlogSimple\Entity\Collection\PostCollection;
use \BlogSimple\Entity\Collection\CommentCollection;
use \BlogSimple\Entity\Collection\AttributeCollection;
use \BlogSimple\Entity\Comment;

Class SqliteDatastore extends MysqlDatastore {

    /**
     * Constructor
     * 
     * @param string $database Location of database
     * @access public
     */
    public function __construct( $database ) {
        $this->connection = new \PDO( sprintf( 'sqlite:%s', $database ) );
        $this->connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
    }

}