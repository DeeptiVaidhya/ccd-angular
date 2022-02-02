SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `assistance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NULL COMMENT 'Assistance title',
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `patient_has_assistance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `assistance_id` INT NOT NULL,
  `patient_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_patient_has_assistance_assistance1`
    FOREIGN KEY (`assistance_id`)
    REFERENCES `assistance` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_patient_has_assistance_patient_insurance_information1`
    FOREIGN KEY (`patient_id`)
    REFERENCES `patient_insurance_information` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_patient_has_assistance_assistance1_idx` ON `patient_has_assistance` (`assistance_id` ASC);

CREATE INDEX `fk_patient_has_assistance_patient_insurance_information1_idx` ON `patient_has_assistance` (`patient_id` ASC);


CREATE TABLE IF NOT EXISTS `program` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `program` VARCHAR(500) NULL,
  `phone` VARCHAR(45) NULL,
  `website` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `created_at` DATETIME NULL,
  `cancer_type` ENUM('Breast Cancer','Colorectal Cancer','Lung Cancer','Prostate Cancer','All'),
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `program_has_assistance` (
  `program_id` INT NOT NULL,
  `assistance_id` INT NOT NULL,
  PRIMARY KEY (`program_id`, `assistance_id`),
  CONSTRAINT `fk_program_has_assistance_program1`
    FOREIGN KEY (`program_id`)
    REFERENCES `program` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_program_has_assistance_assistance1`
    FOREIGN KEY (`assistance_id`)
    REFERENCES `assistance` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_program_has_assistance_assistance1_idx` ON `program_has_assistance` (`assistance_id` ASC);

CREATE INDEX `fk_program_has_assistance_program1_idx` ON `program_has_assistance` (`program_id` ASC);



CREATE TABLE IF NOT EXISTS `patient_program` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `patient_id` INT NOT NULL,
  `program_id` INT NOT NULL,
  `program_type` ENUM('applied', 'approved', 'recommended') NULL DEFAULT NULL,
  `applied_date` DATETIME NULL DEFAULT NULL,
  `recommend_date` DATETIME NULL DEFAULT NULL,
  `approved_date` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_patient_insurance_information_has_program_patient_insuranc1`
    FOREIGN KEY (`patient_id`)
    REFERENCES `patient_insurance_information` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_patient_insurance_information_has_program_program1`
    FOREIGN KEY (`program_id`)
    REFERENCES `program` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_patient_insurance_information_has_program_program1_idx` ON `patient_program` (`program_id` ASC);

CREATE INDEX `fk_patient_insurance_information_has_program_patient_insura_idx` ON `patient_program` (`patient_id` ASC);

ALTER TABLE `patient_insurance_information` ADD `age_group` ENUM('Adult','YoungAdult','Child') NOT NULL AFTER `insurance_plan`, ADD `zip_code` VARCHAR(255) NOT NULL AFTER `age_group`, ADD `cancer_type` VARCHAR(255) NOT NULL AFTER `zip_code`;


INSERT INTO `assistance` (`id`, `title`) VALUES (NULL, 'Co-Pays'), (NULL, 'Financial Assistance'), (NULL, 'Home Care'), (NULL, 'Lodging'), (NULL, 'Medical Equipment/Supplies'), (NULL, 'Medication'), (NULL, 'Nutritional Supplements'), (NULL, 'Protheses'), (NULL, 'Reconstruction'), (NULL, 'Transportation');



INSERT INTO `assistance` (`id`, `title`) VALUES
(NULL, 'Co-Pays'),
(NULL, 'Financial Assistance'),
(NULL, 'Home Care'),
(NULL, 'Lodging'),
(NULL, 'Medical Equipment/Supplies'),
(NULL, 'Medication'),
(NULL, 'Nutritional Supplements'),
(NULL, 'Protheses'),
(NULL, 'Reconstruction'),
(NULL, 'Transportation');

INSERT INTO `program` (`id`, `program`, `phone`, `website`, `description`, `cancer_type`, `created_at`) VALUES
(NULL, 'Patient Advocate Foundation Co-Pay Relief Program (PAF CPR)', '8665123861', 'https://www.copays.org', 'Provides direct co-payment assistance for pharmaceutical products to insured patients (including Medicare Part D beneficiaries) who qualify financially and medically', 'Breast Cancer', NULL),
(NULL, 'The Assistance Fund', '8558453663', 'https://tafcares.org', 'Helps patients facing high medical out-of-pocket costs by providing financial assistance for their copayments, coinsurance, deductibles and other health-related expenses. Covered diagnoses change, please check website for current funding.', 'Breast Cancer', NULL),
(NULL, 'Sisters Network® Inc. - A National African American Breast Cancer Survivorship Organization', '8667811808', 'http://www.sistersnetworkinc.org', 'African-American breast cancer survivorship organization. Promotes the importance of breast health through empowerment, support, breast education programs, resources, information and research. Provides financial assistance for mammograms, co-pay, office visits, prescriptions, medical-related lodging and prosthesis.', 'Breast Cancer', '2020-10-08 21:12:34'),
(NULL, 'PhRMA’s Medicine Assistance Tool (MAT)', '\"8884772669 \"', 'https://medicineassistancetool.org', 'Pharmaceutical Research and Manufacturers of America (PhRMA)’s Medicine Assistance Tool (MAT) is a web platform designed to help patients, caregivers and health care providers learn more about the resources available through the various biopharmaceutical industry programs offered to those who need financial support due to their lack of insurance or inadequate prescription medicine coverage. MAT is not its own patient assistance program, but rather a search engine for many of the support programs and resources that the biopharmaceutical industry has been offering for decades.', 'All', NULL),
(NULL, 'The Pink Fund', '8772347465', 'http://thepinkfund.org', 'Provides short-term financial aid to patients who are in treatment for breast cancer. The aid covers both medical and non-medical related expenses, including health insurance premiums, prescriptions, house or rent payments, car insurance payments, and utility payments. Applicants must be employed, in active treatment and experiencing a loss or reduction in income as a result. Payments are made directly to creditors.', 'Breast Cancer', '2020-10-08 21:12:34'),
(NULL, 'Joe\'s House', '8775637468', 'http://www.joeshouse.org', 'An online lodging resource for cancer patients and their families who must travel away from home for medical treatment. The site lists thousands of lodging facilities near cancer treatment centers and hospitals that cater to patients. Joe’s House’s website offers discounted lodging options for patients; it does not provide financial assistance.', 'All', NULL),
(NULL, 'Healthcare Hospitality Network', '8005429730', 'http://www.hhnetwork.org', 'A nationwide professional association of more than 200 unique, nonprofit organizations that provide lodging and support services to patients, families and their loved ones who are receiving medical treatment far from their home communities.', 'All', '2020-10-08 21:12:34'),
(NULL, 'Patient Access Network (PAN) Foundation', '8663167263', 'https://panfoundation.org', 'Provides co-payment assistance to patients who have insurance but lack the means to pay for out-of-pocket costs for their medications or infusions. Currently includes coverage for numerous disease funds, including various cancers and related illnesses.', 'Breast Cancer', '2020-10-08 21:21:23'),
(NULL, 'The DONNA Foundation', '8772366626', 'https://thedonnafoundation.org', 'Mission is to provide financial assistance and support to those living with breast cancer and fund breast cancer research.', 'Breast Cancer', NULL),
(NULL, 'Cancer Support Community', '8887939355', 'http://www.cancersupportcommunity.org', 'Non-profit dedicated to providing support, education and hope to people affected by cancer. To ensure no one has to face cancer alone, these support services are available through a network of professionally led community-based centers, hospitals, community oncology practices and other non-profits, as well as online.', 'All', '2020-10-08 21:23:18'),
(NULL, 'Triple Step Toward the Cure', '', 'http://triplesteptowardthecure.org', 'Seeks to promote awareness and education for the diagnosis, treatment, and cure of triple negative breast cancer and to provide support, both emotional and financial, to affected individuals and their families. They offer assistance with: meal delivery, emergency funds for rent, groceries, and utilities, transportation related to treatment, housekeeping services, childcare, selected co-pay assistance, prosthetics and wigs.', 'Breast Cancer', '2020-10-08 21:23:18'),
(NULL, 'Rx Outreach', '8887961234', 'http://rxoutreach.org', 'A nonprofit organization that provides affordable medicines for those who qualify financially. Provides 90-day and 180-day supplies of prescription medications, regardless of the number of pills your doctor prescribes per day for most medications. Help is available to people regardless of age or whether they use another discount medicine or patient assistance program, and even to those who currently have a pharmacy benefit, such as Medicare Part D.', 'All', '2020-10-08 21:23:18'),
(NULL, 'Patient Advocate Foundation (PAF)', '8005325274', 'http://www.patientadvocate.org', 'Provides direct services to patients with chronic, life threatening and debilitating diseases to help access care and treatment recommended by their doctor. Offers co-payment assistance - check website for up-to-date list of covered diagnoses and medications. Maintains a searchable National Financial Assistance Resource Directory. Provides a Scholarships for Survivors Program.', 'All', '2020-10-08 21:26:55'),
(NULL, 'BenefitsCheckUp', '', 'https://www.benefitscheckup.org', 'National Council on Aging (NCOA) offers BenefitsCheckUp, a comprehensive, free online tool that connects older adults with benefits they may qualify for.', 'All', '2020-10-08 21:26:55'),
(NULL, 'ViiV Connect', '8445883288', 'https://www.viivconnect.com/patient-assistance-program/', 'The ViiV Healthcare Patient Assistance Program (PAP) offers our medicines at no cost to patients who qualify. At ViiV Healthcare, we strive to provide an equal standard of care for all patients. If a patient doesn’t have insurance and is having trouble paying for the ViiV Healthcare medicine(s) prescribed, the patient may qualify for PAP.', 'All', '2020-10-08 21:28:41'),
(NULL, 'Inside Rx', '8007228979', 'https://insiderx.com/auntbertha', 'The Inside Rx card is a prescription savings card that expands affordable access to brand and generic medications for consumers and their pets at over 60,000 participating retail pharmacies in the U.S. and Puerto Rico. If you have no insurance or have experienced high out-of-pocket costs for your medications for any reason, Inside Rx may be able to help you save up to 80% on your prescriptions.', 'All', '2020-10-08 21:28:41'),
(NULL, 'ScriptSave® WellRx', '8004078156', 'https://www.wellrx.com/', 'ScriptSave® WellRx helps close the gaps in prescription coverage with an innovative savings program that’s open to all. The ScriptSave WellRx platform helps save patients money and increase medication adherence. Discounts are available exclusively through participating pharmacies and the range of the discounts will vary depending on the type of provider and services rendered.', 'All', '2020-10-08 21:30:29'),
(NULL, 'NeedyMeds', '8005036897', 'http://www.needymeds.org', 'Provides accurate, comprehensive and up-to-date information on programs that help people who are facing problems paying for medications and health care. Assists those in need in applying to programs and provides health-related education. Provides a searchable database listing pharmaceutical patient assistance programs, government programs, prescription discount cards, disease-based assistance, camps, scholarships and more.', 'All', '2020-10-09 02:10:29');

INSERT INTO `program_has_assistance` (`program_id`, `assistance_id`) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 2),
(3, 2),
(3, 4),
(3, 6),
(4, 1),
(4, 6),
(5, 2),
(5, 6),
(6, 4),
(7, 4),
(8, 1),
(8, 2),
(9, 2),
(10, 4),
(11, 2),
(12, 6),
(13, 2),
(13, 6),
(14, 6),
(15, 6),
(16, 6),
(18, 5);

SET FOREIGN_KEY_CHECKS=1;