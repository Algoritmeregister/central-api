<?php

class SchemaBasedEntity implements JsonSerializable {
    private $_schema;
    private $_properties;

    public function __construct($schema) {
        $this->_schema = $schema;
        $this->_properties = $schema["properties"];
    }

    public function __get($prop) {
        return $this->_properties[$prop]["value"];
    }

    public function __set($prop, $value) {
        //if (!$this->_properties->{$prop}) throw new Exception('Unknown property');
        $this->_properties[$prop]["value"] = $value;
        return $this;
    }

    public function jsonSerialize() {
        $values = array_map(function ($prop) {
            return $prop["value"] ? $prop["value"] : "niet bekend";
        }, $this->_properties);
        return $values;
    }
}