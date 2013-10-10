<?php

namespace Phlog\Datastore;

use \Phlog\Entity\Collection\PostCollection;
use \Phlog\Entity\Collection\CommentCollection;
use \Phlog\Entity\Collection\AttributeCollection;
use \Phlog\Entity\Comment;
use \Phlog\Entity\Post;
use \Phlog\Entity\Attribute;

Class MysqlDatastore extends PdoDatastoreAbstract implements DatastoreInterface {

    /**
     * Constructor
     * 
     * @param string $host Mysql Host
     * @param string $username Mysql username
     * @param string $password Mysql password
     * @param string $database Mysql database
     * @access public
     */
    public function __construct( $host, $username, $password, $database ) {
        $this->connection = new \PDO( sprintf( 'mysql:dbname=%s;host=%s;', $database, $host ), $username, $password );
    }

   /**
     * Get Posts With Attribute
     *
     * This searches the posts table for entities that match $attribute, $value
     * 
     * @param string $entity_name Entity name e.g. Post
     * @param string $attribute Attribute e.g. Tag
     * @param string $value Value e.g. PHP (for posts tagged PHP)
     * @param int $offset Offset to use in the limit clause
     * @param int $length Number of posts to get
     * @return PostCollection
     * @access public
     */
    public function getPostsWithAttributeAndValue( $attribute, $value, $offset, $length, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            $sql = sprintf(
                'select %1$s.* from %s, %s where %2$s.%s = %1$s.%s and %2$s.%s=:attribute and %2$s.%s=:value %7$s order by %1$s.%4$s desc limit :offset, :length',
                $this->tables['posts'],
                $this->tables['post_attributes'],
                $this->table_fields['post_attributes.post_id'],
                $this->table_fields['posts.id'],
                $this->table_fields['post_attributes.attribute'],
                $this->table_fields['post_attributes.value'],
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

        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Post' );
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
     * @return PostCollection
     * @access public
     */
    public function getPosts( $offset, $length, array $where = null ) {

        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where 1=1 %s order by %s desc limit :offset, :length',
                $this->tables['posts'],
                $sql_where,
                $this->table_fields['posts.id']
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
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Post' );
        $posts = $statement->fetchAll();
        return new PostCollection( $posts );
    }

    /**
     * Get total post count
     *
     * @param array $where Array of field => value pairs to add to the query
     * @return int Number of total posts
     * @access public
     */
    public function getTotalPosts( array $where = null ) {

        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select count(%s) from %s where 1=1 %s',
                $this->table_fields['posts.id'],
                $this->tables['posts'],
                $sql_where
            )
        );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        return $statement->fetch( \PDO::FETCH_COLUMN );
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
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %s=:post_id %3$s',
                $this->tables['posts'],
                $this->table_fields['posts.id'],
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
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Post' );
        return $statement->fetch();
    }

    /**
     * Add a post
     *
     * All public fields in the post will be written to the table
     * 
     * @param $post Post to add
     * @return int Returns 0 or 1
     * @access public
     */
    public function addPost( Post $post ) {
        $vars = get_object_vars( $post );
        $sql = sprintf(
            'insert into %s (%s) values ( :%s )',
            $this->tables['posts'],
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
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %2$s = ( select max(%2$s) from %1$s where %2$s < :post_id %3$s )',
                $this->tables['posts'],
                $this->table_fields['posts.id'],
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
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Post' );
        return $statement->fetch();
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
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %2$s = ( select min(%2$s) from %1$s where %2$s > :post_id %3$s )',
                $this->tables['posts'],
                $this->table_fields['posts.id'],
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
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Post' );
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
     * @access public
     */
    public function getPostComments( $post_id, array $where = null, $offset=0, $length=9999999999 ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'select * from %s where %s=:post_id %4$s order by %s asc limit :offset, :length',
                $this->tables['comments'],
                $this->table_fields['comments.post_id'],
                $this->table_fields['posts.id'],
                $sql_where
            )
        );
        $statement->bindValue( ':post_id', $post_id );
        $statement->bindValue( ':offset', $offset, \PDO::PARAM_INT );
        $statement->bindValue( ':length', $length, \PDO::PARAM_INT );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, '\Phlog\Entity\Comment' );
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
     * @access public
     */
    public function getPostAttributes( $post_id ) {
        $statement = $this->connection->prepare(
            sprintf(
                'select %s, %s from %s where %s=:post_id',
                $this->table_fields['post_attributes.attribute'],
                $this->table_fields['post_attributes.value'],
                $this->tables['post_attributes'],
                $this->table_fields['post_attributes.post_id']
            )
        );
        $statement->bindValue( ':post_id', $post_id, \PDO::PARAM_INT );
        $statement->execute();
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Attribute' );
        $attributes = $statement->fetchAll();
        return new AttributeCollection( $attributes, $this->table_fields['post_attributes.attribute'], $this->table_fields['post_attributes.value'] );
    }

    /**
     * Add a post attribute
     *
     * All public fields in the attribute will be written to the table
     * 
     * @param $attribute Attribute to add
     * @return int Returns 0 or 1
     * @access public
     */
    public function addPostAttribute( Attribute $attribute ) {
        $vars = get_object_vars( $attribute );
        $sql = sprintf(
            'insert into %s (%s) values ( :%s )',
            $this->tables['post_attributes'],
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
     * Get all values for an attribute
     * 
     * @param string $attribute Attribute e.g. 'tag'
     * @return array
     * @access public
     */
    public function getAttributeValues( $attribute, array $where = null ) {
        $sql_where = $where ? $this->buildWhereSql( $where ) : '';
        $statement = $this->connection->prepare(
            sprintf(
                'SELECT distinct %2$s.%1$s, %2$s.%4$s FROM %2$s
                LEFT outer JOIN %3$s
                ON %3$s.%5$s = %2$s.%6$s
                where
                %2$s.%4$s=:attribute
                and %3$s.%5$s is not null %7$s',
                $this->table_fields['post_attributes.value'],
                $this->tables['post_attributes'],
                $this->tables['posts'],
                $this->table_fields['post_attributes.attribute'],
                $this->table_fields['posts.id'],
                $this->table_fields['post_attributes.post_id'],
                $sql_where
            )
        );
        $statement->bindValue( ':attribute', $attribute );
        if ( $where ) {
            foreach( $where as $k => $v ) {
                $statement->bindValue( ":$k", $v );
            }
        }
        $statement->setFetchMode( \PDO::FETCH_CLASS, 'Phlog\Entity\Attribute' );
        $statement->execute();
        $attributes = $statement->fetchAll();
        return new AttributeCollection( $attributes, $this->table_fields['post_attributes.attribute'], $this->table_fields['post_attributes.value'] );
    }

    /**
     * Add a comment
     *
     * All public fields in the comment will be written to the table
     * 
     * @param $comment Comment to add
     * @return int Returns 0 or 1
     * @access public
     */
    public function addComment( Comment $comment ) {
        $vars = get_object_vars( $comment );
        $sql = sprintf(
            'insert into %s (%s) values ( :%s )',
            $this->tables['comments'],
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

}