DROP PROCEDURE IF EXISTS `gifts_update_stock`;

CREATE PROCEDURE gifts_update_stock()
	LANGUAGE SQL
    DETERMINISTIC
    SQL SECURITY DEFINER
    COMMENT 'Updates stock info of products from gifts.ru'
BEGIN
    DECLARE notFound INT DEFAULT FALSE;
    DECLARE vProductId, vAmount, vFree, vInwayamount, vInwayfree, vEnduserprice INT(11);
    DECLARE vCode VARCHAR(100);

    DECLARE productTmpCur CURSOR FOR SELECT id, code, amount, free, inwayamount, inwayfree, enduserprice FROM `dpd_product_tmp`;
    DECLARE slaveProductTmpCur CURSOR FOR SELECT id, code, amount, free, inwayamount, inwayfree, enduserprice FROM `dpd_slave_product_tmp`;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET notFound = TRUE;

    OPEN productTmpCur;

    WHILE NOT notFound DO
        FETCH productTmpCur INTO vProductId, vCode, vAmount, vFree, vInwayamount, vInwayfree, vEnduserprice;
        IF NOT notFound THEN
            UPDATE `dpd_product`
            SET
                amount = vAmount,
                free = vFree,
                inwayamount = vInwayamount,
                inwayfree = vInwayfree,
                enduserprice = vEnduserprice
            WHERE id = vProductId AND code = vCode;
        END IF;
    END WHILE;

    CLOSE productTmpCur;

    SET notFound = FALSE;

    OPEN slaveProductTmpCur;

    WHILE NOT notFound DO
        FETCH slaveProductTmpCur INTO vProductId, vCode, vAmount, vFree, vInwayamount, vInwayfree, vEnduserprice;
        IF NOT notFound THEN
            UPDATE `dpd_slave_product`
            SET
                amount = vAmount,
                free = vFree,
                inwayamount = vInwayamount,
                inwayfree = vInwayfree,
                enduserprice = vEnduserprice
            WHERE id = vProductId AND code = vCode;
        END IF;
    END WHILE;

    CLOSE slaveProductTmpCur;
END;