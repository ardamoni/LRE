select sum(d2.`rate`) as TotalRevenueExpected from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d1.`year`=d2.`year` AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`year`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d1.`year`=d2.`year` AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`year` d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d1.`year`=d2.`year` AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d1.`year`=d2.`year` AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`class`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE (d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`class`);
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE (d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`class`) AND d2.`districtid`=d3.`districtid`;
select d1.`upn`, d1.`subupn`, d1.`property_use`, d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`,d2.`districtid`,d3.`district_name` from `property` d1, `fee_fixing_property` d2, `area_district` d3 WHERE d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`class` AND d2.`districtid`=d3.`districtid`;
select DISTINCT d1.`districtid`, d1.year, d2.`district_name` from `fee_fixing_property`d1, `area_district` d2 where d1.districtid = d2.`districtid`;
select DISTINCT d1.`districtid`, d2.`district_name` from `fee_fixing_property`d1, `area_district` d2 where d1.districtid = d2.`districtid`;
select DISTINCT d1.`districtid`, d2.`district_name` from `fee_fixing_property`d1 `area_district` d2 where d1.districtid = d2.`districtid`;
select DISTINCT d1.`districtid`, d2.`district_name` from `fee_fixing_property`d1 `area_district` d2 where d1.districtid = d2 districtid;
select DISTINCT d1.`districtid`, d2.`district_name` from `fee_fixing_property`d1, `area_district` d2 where d1.districtid = d2 districtid;
select DISTINCT d1.`districtid`, d2.`district_name` from `fee_fixing_property`, `area_district` d2 where d1.districtid = d2 districtid;
select DISTINCT `districtid` from `fee_fixing_property`;
select unique `districtid`, `districtid` from `fee_fixing_property`;
select unique districtid from `fee_fixing_property`;
UPDATE `fee_fixing_business` a
SET a.year = '2013';
UPDATE `fee_fixing_property` a
SET a.year = '2013';
UPDATE property a, own_owner c
SET a.ownerid = c.id
WHERE a.owner = c.name AND a.owner<>'';
UPDATE `property` a
SET a.ownerid = '';
UPDATE property a, own_owner c
SET a.ownerid = c.id
WHERE a.owner = c.name AND c.name<>'';
UPDATE property a, own_owner c
SET a.ownerid = c.id
WHERE a.owner = c.name;
UPDATE property a, own_owner c
SET a.ownerid = c.id
WHERE a.ownerid = c.name;
SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` FROM `property`

UPDATE `property`
SET property.ownerid = 
( 
SELECT own_owner.id FROM own_owner
WHERE property.`owner` = own_owner.`name`
);
INSERT INTO `own_owner`
   (name, `address1`, tel1, `email1`)
      SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` FROM `property`;
INSERT INTO `own_owner`
   (name, `address1`, tel1, `email1`)
      VALUES SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` FROM `property`;
SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` FROM `property`;
INSERT INTO `own_owner`
   (name, `address1`, tel1, `email1`)
      VALUES (SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` FROM `property`);
INSERT INTO `own_owner`
   (name, `address1`, tel1, `email1`)
      VALUES (SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email` from `property`);
INSERT INTO `own_owner`
   (name, `address1`, tel1, `email1`)
      VALUES (SELECT DISTINCT `owner`, `owneraddress`, `owner_tel`, `owner_email`);
UPDATE property 
          SET
                 ownerid = id;
UPDATE A 
          SET
                 A.ownerid = U.id
          FROM property A
          JOIN   `property` U
          ON A.id = U.id;
SELECT districtid, district_name from `area_district` ORDER BY `districtid`;
SELECT * from `area_district` ORDER BY `districtid`;
SELECT * from `area_district` ORDER BY `districtid ;;
SELECT districtid, district_name from `area_district` ORDER BY `districtid ;;
SELECT * from from `area_district` ORDER BY `districtid ;;
UPDATE payments_property SET `districtid`='130';
INSERT INTO `usr_user_district` (`username`, `regionid`, `districtid`) VALUES
('arben', 10, 130);
INSERT INTO `tickets` (`id`, `starting`, `ending`, `username`) VALUES
(1, '100001', '100100', 'collector1'),
(2, '200001', '200100', 'arben'),
(3, '300001', '300100', 'ekke');
INSERT INTO `tickets` (`id`, `starting`, `ending`, `username`) VALUES
(NULL, '100001', '100100', 'collector1'),
(NULL, '200001', '200100', 'arben'),
(NULL, '300001', '300100', 'ekke');
SELECT * 	FROM 	`business` 	WHERE 	`upn` = '610-0616-0155' AND `year` = '2013' AND `districtid` = '130';
SELECT * 	FROM 	`business` 	WHERE 	`upn` = '608-0616-0155' AND `year` = '2013' AND `districtid` = '130';
SELECT * 	FROM 	`business` 	WHERE 	`upn` = '608-0615-0308' AND `year` = '2013' AND `districtid` = '130';
SELECT * 	FROM 	`business` 	WHERE 	`upn` = '608-0615-0315' AND `year` = '2013' AND `districtid` = '130';
SELECT * 	FROM 	`business` 	WHERE 	`upn` = '608-0615-0230' AND `year` = '2013' AND `districtid` = '130';
SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d1.LUPMIS_color, d1.Address, d1.Landuse, d1.ParcelOf, d2.unit_planning from `KML_from_LUPMIS` d1, `property` d2 WHERE d1.`UPN` = d2.`upn` AND d1.`districtid`='130'