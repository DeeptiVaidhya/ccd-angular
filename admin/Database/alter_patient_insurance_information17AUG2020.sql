ALTER TABLE `patient_insurance_information` CHANGE `company_name` `insurance_name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `patient_insurance_information` ADD `identification_number` VARCHAR(255) NOT NULL AFTER `id`;

ALTER TABLE `patient_insurance_information` CHANGE `company_address` `company_address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `patient_insurance_information` DROP `company_city`, DROP `pin_code`;