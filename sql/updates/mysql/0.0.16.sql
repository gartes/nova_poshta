ALTER TABLE `#__virtuemart_shipment_plg_nova_pochta`
    CHANGE `ref_city` `ref_city` CHAR(36)
        CHARACTER SET utf8
            COLLATE utf8_general_ci
        NULL DEFAULT NULL;