<?php

namespace Phlog\Entity\Collection;

class AttributeCollection extends CollectionAbstract {

    /**
     * Name of the attribute property
     *
     * @var string
     * @access public
     */
    public $attribute_property;

    /**
     * Value field
     *
     * Name of the value property
     *
     * @var string
     * @access public
     */
    public $value_property;

    /**
     * Constructor
     *
     * @param array $collection Collection
     * @param string $attribute_property Name of the attribute property
     * @param string $value_property Name of the value property
     * @return AttributeCollection
     * @access public
     */
    public function __construct( array $collection, $attribute_property, $value_property ) {
        $this->attribute_field = $attribute_property;
        $this->value_field = $value_property;
        parent::__construct( $collection );
    }

    /**
     * Organize
     *
     * Takes all the attributes and returns an array indexed by attributes
     * 
     * @return array
     * @access public
     */
    public function organize() {
        $attributes = array();
        foreach( $this->collection as $attribute ) {
            if ( isset( $attributes[$attribute->{$this->attribute_field}] ) ) {
                $attributes[$attribute->{$this->attribute_field}][] = $attribute->{$this->value_field};
            }
            else {
                $attributes[$attribute->{$this->attribute_field}] = array( $attribute->{$this->value_field} );
            }
        }

        return $attributes;
    }

}