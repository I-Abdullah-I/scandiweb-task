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

-- INSERT INTO product (name, price, sku, type) VALUES("Acme DISC", 1, "JVC200123", "DVD-disc");
-- INSERT INTO attribute_values_numeric (type_id, attribute_id, entity_id, attribute_value) VALUES (1, 1, 1, 700);

-- INSERT INTO product (name, price, sku, type) VALUES ("Avme DISC", 2, "JVC200124", "DVD-disc");
-- INSERT INTO attribute_values_numeric (type_id, attribute_id, entity_id, attribute_value) VALUES (
-- 	(SELECT id FROM entity_type WHERE label = "product"),
--     (SELECT id FROM attribute WHERE label = "size"),
--     (SELECT id FROM product WHERE sku = "JVC200124"),
--     750
-- );

-- SELECT P.sku, P.name, P.price, P.type, A.label AS attribute, V.attribute_value AS attribute_value
-- FROM eav_product_catalog AS P 
-- LEFT JOIN eav_product_attribute_value_numeric AS V 
-- ON P.id = V.entity_id
-- LEFT JOIN eav_attribute AS A
-- ON A.id = V.attribute_id;

-- SELECT P.sku, P.name, P.price, P.type, JSON_OBJECTAGG(A.label, V.attribute_value)
-- FROM eav_product_catalog AS P 
-- LEFT OUTER JOIN eav_product_attribute_value_numeric AS V 
-- ON P.id = V.entity_id
-- LEFT OUTER JOIN eav_attribute AS A
-- ON A.id = V.attribute_id
-- GROUP BY P.sku;