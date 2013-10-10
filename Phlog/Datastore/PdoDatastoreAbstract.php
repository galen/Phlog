<?php

namespace Phlog\Datastore;

class PdoDatastoreAbstract {

    /**
     * Table fields
     *
     * Maps the table fields
     *
     * @var array
     * @access protected
     */
    protected $table_fields = array(
        'posts.id'                      => 'id',
        'comments.id'                   => 'id',
        'comments.post_id'              => 'post_id',
        'post_attributes.post_id'       => 'post_id',
        'post_attributes.attribute'     => 'attribute',
        'post_attributes.value'         => 'value'
    );

    /**
     * Tables
     *
     * Maps the table names
     *
     * @var array
     * @access protected
     */
    protected $tables = array(
        'posts'             => 'posts',
        'comments'          => 'comments',
        'post_attributes'   => 'post_attributes'
    );

    /**
     * Set the table fields
     *
     * @param array $table_fields Array of table fields
     * @return void
     * @access public
     */
    public function setTableFields( array $table_fields ) {
        $this->table_fields = $table_fields;
    }

    /**
     * Set the table names
     *
     * @param array $tables Array of table names
     * @return void
     * @access public
     */
    public function setTables( array $tables ) {
        $this->tables = $tables;
    }

    /**
     * Build where clause
     * 
     * @param array $where Array of field => value pairs to add to the query
     * @return string Returns where string
     * @access protected
     */
    protected function buildWhereSql( array $where ) {
        $sql_where = '';
        foreach( $where as $k => $v ) {
            $sql_where .= sprintf( "and $k=:$k " );
        }
        return $sql_where;
    }

    /**
     * Set PDO attribute
     *
     * @param int $attribute Attribute to set
     * @param mixed $value Value to set the attribute to
     * @return boolean Returns success status
     * @access public
     */
    public function setAttribute( $attribute, $value ){
        return $this->connection->setAttribute( $attribute, $value );
    }

    /**
     * Set PDO attributes
     *
     * @param array $attributes Array of attributes
     * @return void
     * @access public
     */
    public function setAttributes( array $attributes ) {
        $return = true;
        foreach( $attributes as $attribute => $value ) {
            $result = $this->setAttribute( $attribute, $value );
            if ( $result === false ) {
                $return = false;
            }
        }
        return $return;
    }

}