CREATE DATABASE IF NOT EXISTS SCANDIWEB_TEST;

USE SCANDIWEB_TEST;

CREATE TABLE eav_product_catalog(
    id BIGINT NOT NULL AUTO_INCREMENT,
    sku VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    type ENUM('DVD', 'Book', 'Furniture') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ( id ),
    UNIQUE ( sku )
);

CREATE TABLE eav_entity_type(
    id BIGINT NOT NULL AUTO_INCREMENT,
    label VARCHAR(255) NOT NULL,
    PRIMARY KEY ( id )
);

CREATE TABLE eav_attribute(
    id BIGINT NOT NULL AUTO_INCREMENT,
    label VARCHAR(255) NOT NULL,
    type_id BIGINT NOT NULL,
    PRIMARY KEY ( id ),
    FOREIGN KEY ( type_id ) REFERENCES eav_entity_type( id )
);

CREATE TABLE eav_product_attribute_value_numeric(
    id BIGINT NOT NULL AUTO_INCREMENT,
    entity_id BIGINT NOT NULL,
    attribute_id BIGINT NOT NULL,
    attribute_value FLOAT NOT NULL,
    PRIMARY KEY ( id ),
    FOREIGN KEY ( entity_id ) 
        REFERENCES eav_product_catalog( id )
        ON DELETE CASCADE,
    FOREIGN KEY ( attribute_id ) REFERENCES eav_attribute( id )
);

INSERT INTO eav_entity_type ( label ) VALUES("product");

INSERT INTO eav_attribute (label, type_id) VALUES ("size", 1), ("weight", 1), ("height", 1), ("width", 1), ("length", 1);