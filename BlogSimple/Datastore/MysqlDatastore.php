<?php

namespace BlogSimple\Datastore;

use \BlogSimple\Entity\Collection\PostCollection;
use \BlogSimple\Entity\Collection\CommentCollection;
use \BlogSimple\Entity\Collection\AttributeCollection;
use \BlogSimple\Entity\Comment;

Class MysqlDatastore implements DatastoreInterface {

    /**
     * Constructor
     * 
     * @param string $host Mysql Host
     * @param string $username Mysql username
     * @param string $password Mysql password
     * @param string $database Mysql database
     */
    public function __construct( $host, $username, $password, $database ) {
        $this->connection = new \PDO( sprintf( 'mysql:dbname=%s;host=%s;', $database, $host ), $username, $password );
        $this->connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
    }

   /**
     * Get Entities With Attribute
     *
     * This searches the EAV table for entities that match $entity_name, $attribute, $value
     * 
     * @param string $entity_name Entity name e.g. Post
     * @param string $attribute Attribute e.g. Tag
     * @param string $value Value e.g. PHP (for posts tagged PHP)
     * @param int $offset Offset to use in the limit clause
     * @param int $length Number of posts to get
     * @param boolean $get_pagination Populate the PostCollection with pagination info
     * @return PostCollection
     */
    public function getPostsWithAttributeAndValue( $attribute, $value, $offset, $length, array $where = null ) {

        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            $sql = sprintf(
                'select %1$s.* from %s, %s where %2$s.%s = %1$s.%s and %2$s.%s=:attribute and %2$s.%s=:value %7$s order by %1$s.%4$s desc limit :offset, :length',
                'posts',
                'post_attributes',
                'post_id',
                'id',
                'attribute',
                'value',
                $sql_where
            )
        );
        $statement->bindValue( ':attribute', $attribute );
        $statement->bindValue( ':value', $value );
        $statement->bindValue( ':offset', $offset, \PDO::PARAM_INT );
        $statement->bindValue( ':length', $length, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();

        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Post' );
        $posts = $statement->fetchAll();
        return new PostCollection( $posts );
    }

    /**
     * Get Posts
     *
     * Get posts matching
     * 
     * @param int $offset Offset to use in the limit clause
     * @param int $length Number of posts to get
     * @param array $where Array of field => value pairs to add to the query
     * @param boolean $get_pagination 
     * @return PostCollection
     */
    public function getPosts( $offset, $length, array $where = null ) {

        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where 1=1 %s order by %s desc limit :offset, :length',
                'posts',
                $sql_where,
                'id'
            )
        );
        $statement->bindValue( ':offset', $offset, \PDO::PARAM_INT );
        $statement->bindValue( ':length', $length, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Post' );
        $posts = $statement->fetchAll();
        return new PostCollection( $posts );
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
     */
    public function getPost( $post_id, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %s=:post_id %3$s',
                'posts',
                'id',
                $sql_where
            )
        );
        $statement->bindValue( ':post_id', $post_id, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Post' );
        return $statement->fetch();
    }

    /**
     * Get Previous Post
     *
     * Get the previous post from the posts table
     * 
     * @param int $post_id Id of the post
     * @return Post
     */
    public function getPreviousPost( $post_id, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %2$s = ( select max(%2$s) from %1$s where %2$s < :post_id %3$s )',
                'posts',
                'id',
                $sql_where
            )
        );
        $statement->bindValue( ':post_id', $post_id, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Post' );
        return $statement->fetch();
    }

    /**
     * Get Next Post
     *
     * Get the next post from the posts table
     * 
     * @param int $post_id Id of the post
     * @return Post
     */
    public function getNextPost( $post_id, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %2$s = ( select min(%2$s) from %1$s where %2$s > :post_id %3$s )',
                'posts',
                'id',
                $sql_where
            )
        );
        $statement->bindValue( ':post_id', $post_id, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Post' );
        return $statement->fetch();
    }

    /**
     * Get Post Comments
     *
     * Get all post comments
     * 
     * @param int $post_id Post Id
     * @param int $offset Offset to use in the limit clause (default 0 to get all comments)
     * @param int $length Number of posts to get (default 999.. to get all comments)
     * @param array $where Array of field => value pairs to add to the query
     * @return CommentCollection
     */
    public function getPostComments( $post_id, $offset=0, $length=9999999999 ) {
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %s=:post_id order by %s asc limit :offset, :length',
                'comments',
                'post_id',
                'id'
            )
        );
        $statement->bindValue( ':post_id', $post_id );
        $statement->bindValue( ':offset', $offset, \PDO::PARAM_INT );
        $statement->bindValue( ':length', $length, \PDO::PARAM_INT );
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, '\BlogSimple\Entity\Comment' );
        $comments = $statement->fetchAll();
        return new CommentCollection( $comments ); 
    }

    /**
     * Get Post Attributes
     *
     * Get all attributes associated with a post
     * 
     * @param int $post_id Post ID
     * @return AttributeCollection
     */
    public function getPostAttributes( $post_id ) {
        $statement = $this->connection->prepare(
            sprintf(
                'select %s, %s from %s where %s=:post_id',
                'attribute',
                'value',
                'post_attributes',
                'post_id'
            )
        );
        $statement->bindValue( ':post_id', $post_id, \PDO::PARAM_INT );
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'BlogSimple\Entity\Attribute' );
        $attributes = $statement->fetchAll();
        return new AttributeCollection( $attributes );
    }

    /**
     * Get all values for an attribute
     * 
     * @param string $attribute Attribute e.g. 'tag'
     * @return array
     */
    public function getPostAttributeValues( $attribute, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'SELECT distinct %2$s.%1$s FROM %2$s
                LEFT outer JOIN %3$s
                ON %3$s.%5$s = %2$s.%6$s
                where
                %2$s.%4$s=:attribute
                and %3$s.%5$s is not null %7$s',
                'value',
                'post_attributes',
                'posts',
                'attribute',
                'id',
                'post_id',
                $sql_where
            )
        );
        $statement->bindValue( ':attribute', $attribute );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        return $statement->fetchAll( \PDO::FETCH_OBJ );
    }

    /**
     * Add a comment
     *
     * All public fields in the comment will be written to the table
     * 
     * @param $comment Comment to add
     * @return int Returns 0 or 1
     */
    public function addComment( Comment $comment ) {
        $vars = get_object_vars( $comment );
        $sql = sprintf(
            'insert into %s (%s) values ( :%s )',
            'comments',
            implode( ',', array_keys( $vars ) ),
            implode( ',:', array_keys( $vars ) )
        );
        $statement = $this->connection->prepare( $sql );
        foreach( $vars as $k => $v ) {
            $statement->bindValue( ":$k", $v );
        }
        $statement->execute();
        return $statement->rowCount();
    }

    /**
     * Build where clause
     * @param array $where Array of field => value pairs to add to the query
     * @return string Returns where string
     */
    protected function buildWhereSql( array $where ) {
        $sql_where = '';
        foreach( $where as $k => $v ) {
            $sql_where .= sprintf( "and $k=:$k " );
        }
        return $sql_where;
    }

    public function exists() {
        return true;
    }

}