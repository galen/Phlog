<?php

namespace Phlog\Datastore;

use \Phlog\Entity\Collection\PostCollection;
use \Phlog\Entity\Collection\CommentCollection;
use \Phlog\Entity\Collection\AttributeCollection;
use \Phlog\Entity\Comment;

Class SqliteDatastore extends MysqlDatastore {

    /**
     * Constructor
     * 
     * @param string $database Location of database
     * @access public
     */
    public function __construct( $database ) {
        $this->connection = new \PDO( sprintf( 'sqlite:%s', $database ) );
    }

}