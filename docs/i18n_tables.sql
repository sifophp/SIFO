-- --------------------------------------------------------

-- 
-- Table structure for table `i18n_language_codes`
-- 

CREATE TABLE `i18n_language_codes` (
  `3letter` char(3) NOT NULL COMMENT 'ISO 639-2 Code',
  `2letter` varchar(2) default NULL COMMENT 'ISO 639-1 Code',
  `english_name` varchar(255) default NULL,
  `french_name` varchar(255) default NULL,
  `local_name` varbinary(255) default NULL,
  `enabled` enum('No','Yes') default 'No',
  `l10n` varchar(5) default NULL,
  `direction` enum('RTL','LTR') default 'LTR',
  PRIMARY KEY  (`3letter`),
  KEY `l10n` (`l10n`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `i18n_language_codes`
-- 

INSERT INTO `i18n_language_codes` VALUES ('aar', 'aa', 'Afar', 'afar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('abk', 'ab', 'Abkhazian', 'abkhaze', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ace', NULL, 'Achinese', 'aceh', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ach', NULL, 'Acoli', 'acoli', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ada', NULL, 'Adangme', 'adangme', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ady', NULL, 'Adyghe; Adygei', 'adyghé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('afa', NULL, 'Afro-Asiatic languages', 'afro-asiatiques, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('afh', NULL, 'Afrihili', 'afrihili', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('afr', 'af', 'Afrikaans', 'afrikaans', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ain', NULL, 'Ainu', 'aïnou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('aka', 'ak', 'Akan', 'akan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('akk', NULL, 'Akkadian', 'akkadien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('alb', 'sq', 'Albanian', 'albanais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ale', NULL, 'Aleut', 'aléoute', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('alg', NULL, 'Algonquian languages', 'algonquines, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('alt', NULL, 'Southern Altai', 'altai du Sud', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('amh', 'am', 'Amharic', 'amharique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ang', NULL, 'English, Old (ca.450-1100)', 'anglo-saxon (ca.450-1100)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('anp', NULL, 'Angika', 'angika', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('apa', NULL, 'Apache languages', 'apaches, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ara', 'ar', 'Arabic', 'arabe', 0xd8b9d8b1d8a8d98a, 'Yes', 'ar_AE', 'RTL');
INSERT INTO `i18n_language_codes` VALUES ('arc', NULL, 'Official Aramaic (700-300 BCE); Imperial Aramaic (700-300 BCE)', 'araméen d''empire (700-300 BCE)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('arg', 'an', 'Aragonese', 'aragonais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('arm', 'hy', 'Armenian', 'arménien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('arn', NULL, 'Mapudungun; Mapuche', 'mapudungun; mapuche; mapuce', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('arp', NULL, 'Arapaho', 'arapaho', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('art', NULL, 'Artificial languages', 'artificielles, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('arw', NULL, 'Arawak', 'arawak', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('asm', 'as', 'Assamese', 'assamais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ast', NULL, 'Asturian; Bable; Leonese; Asturleonese', 'asturien; bable; léonais; asturoléonais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ath', NULL, 'Athapascan languages', 'athapascanes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('aus', NULL, 'Australian languages', 'australiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ava', 'av', 'Avaric', 'avar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ave', 'ae', 'Avestan', 'avestique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('awa', NULL, 'Awadhi', 'awadhi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('aym', 'ay', 'Aymara', 'aymara', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('aze', 'az', 'Azerbaijani', 'azéri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bad', NULL, 'Banda languages', 'banda, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bai', NULL, 'Bamileke languages', 'bamiléké, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bak', 'ba', 'Bashkir', 'bachkir', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bal', NULL, 'Baluchi', 'baloutchi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bam', 'bm', 'Bambara', 'bambara', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ban', NULL, 'Balinese', 'balinais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('baq', 'eu', 'Basque', 'basque', 0x4575736b617261, 'Yes', 'eu_ES', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bas', NULL, 'Basa', 'basa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bat', NULL, 'Baltic languages', 'baltes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bej', NULL, 'Beja; Bedawiyet', 'bedja', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bel', 'be', 'Belarusian', 'biélorusse', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bem', NULL, 'Bemba', 'bemba', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ben', 'bn', 'Bengali', 'bengali', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ber', NULL, 'Berber languages', 'berbères, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bho', NULL, 'Bhojpuri', 'bhojpuri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bih', 'bh', 'Bihari', 'bihari', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bik', NULL, 'Bikol', 'bikol', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bin', NULL, 'Bini; Edo', 'bini; edo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bis', 'bi', 'Bislama', 'bichlamar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bla', NULL, 'Siksika', 'blackfoot', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bnt', NULL, 'Bantu languages', 'bantou, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bos', 'bs', 'Bosnian', 'bosniaque', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bra', NULL, 'Braj', 'braj', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bre', 'br', 'Breton', 'breton', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('btk', NULL, 'Batak languages', 'batak, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bua', NULL, 'Buriat', 'bouriate', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bug', NULL, 'Buginese', 'bugi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bul', 'bg', 'Bulgarian', 'bulgare', 0xd0b1d18ad0bbd0b3d0b0d180d181d0bad0b8, 'Yes', 'bg_BG', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('bur', 'my', 'Burmese', 'birman', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('byn', NULL, 'Blin; Bilin', 'blin; bilen', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cad', NULL, 'Caddo', 'caddo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cai', NULL, 'Central American Indian languages', 'amérindiennes de l''Amérique centrale,  langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('car', NULL, 'Galibi Carib', 'karib; galibi; carib', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cat', 'ca', 'Catalan', 'catalan; valencien', 0x436174616cc3a0, 'Yes', 'ca_ES', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cau', NULL, 'Caucasian languages', 'caucasiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ceb', NULL, 'Cebuano', 'cebuano', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cel', NULL, 'Celtic languages', 'celtiques, langues; celtes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cha', 'ch', 'Chamorro', 'chamorro', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chb', NULL, 'Chibcha', 'chibcha', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('che', 'ce', 'Chechen', 'tchétchène', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chg', NULL, 'Chagatai', 'djaghataï', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chi', 'zh', 'Chinese', 'chinois', 0xe4b8ade69687, 'Yes', 'zh_CN', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chk', NULL, 'Chuukese', 'chuuk', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chm', NULL, 'Mari', 'mari', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chn', NULL, 'Chinook jargon', 'chinook, jargon', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cho', NULL, 'Choctaw', 'choctaw', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chp', NULL, 'Chipewyan; Dene Suline', 'chipewyan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chr', NULL, 'Cherokee', 'cherokee', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chu', 'cu', 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic', 'slavon d''église; vieux slave; slavon liturgique; vieux bulgare', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chv', 'cv', 'Chuvash', 'tchouvache', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('chy', NULL, 'Cheyenne', 'cheyenne', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cmc', NULL, 'Chamic languages', 'chames, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cop', NULL, 'Coptic', 'copte', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cor', 'kw', 'Cornish', 'cornique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cos', 'co', 'Corsican', 'corse', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cpe', NULL, 'Creoles and pidgins, English based', 'créoles et pidgins basés sur l''anglais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cpf', NULL, 'Creoles and pidgins, French-based', 'créoles et pidgins basés sur le français', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cpp', NULL, 'Creoles and pidgins, Portuguese-based', 'créoles et pidgins basés sur le portugais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cre', 'cr', 'Cree', 'cree', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('crh', NULL, 'Crimean Tatar; Crimean Turkish', 'tatar de Crimé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('crp', NULL, 'Creoles and pidgins', 'créoles et pidgins', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('csb', NULL, 'Kashubian', 'kachoube', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cus', NULL, 'Cushitic languages', 'couchitiques,  langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('cze', 'cs', 'Czech', 'tchèque', 0xc48c65c5a174696e61, 'Yes', 'cs_CZ', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dak', NULL, 'Dakota', 'dakota', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dan', 'da', 'Danish', 'danois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dar', NULL, 'Dargwa', 'dargwa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('day', NULL, 'Land Dayak languages', 'dayak, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('del', NULL, 'Delaware', 'delaware', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('den', NULL, 'Slave (Athapascan)', 'esclave (athapascan)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dgr', NULL, 'Dogrib', 'dogrib', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('din', NULL, 'Dinka', 'dinka', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('div', 'dv', 'Divehi; Dhivehi; Maldivian', 'maldivien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('doi', NULL, 'Dogri', 'dogri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dra', NULL, 'Dravidian languages', 'dravidiennes,  langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dsb', NULL, 'Lower Sorbian', 'bas-sorabe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dua', NULL, 'Duala', 'douala', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dum', NULL, 'Dutch, Middle (ca.1050-1350)', 'néerlandais moyen (ca. 1050-1350)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dut', 'nl', 'Dutch', 'néerlandais; flamand', 0x4e656465726c616e6473, 'Yes', 'nl_NL', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dyu', NULL, 'Dyula', 'dioula', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('dzo', 'dz', 'Dzongkha', 'dzongkha', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('efi', NULL, 'Efik', 'efik', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('egy', NULL, 'Egyptian (Ancient)', 'égyptien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('eka', NULL, 'Ekajuk', 'ekajuk', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('elx', NULL, 'Elamite', 'élamite', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('eng', 'en', 'English', 'anglais', 0x456e676c697368, 'Yes', 'en_US', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('enm', NULL, 'English, Middle (1100-1500)', 'anglais moyen (1100-1500)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('epo', 'eo', 'Esperanto', 'espéranto', 0x4573706572616e746f, 'Yes', 'eo_XX', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('est', 'et', 'Estonian', 'estonien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ewe', 'ee', 'Ewe', 'éwé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ewo', NULL, 'Ewondo', 'éwondo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fan', NULL, 'Fang', 'fang', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fao', 'fo', 'Faroese', 'féroïen', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fat', NULL, 'Fanti', 'fanti', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fij', 'fj', 'Fijian', 'fidjien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fil', NULL, 'Filipino; Pilipino', 'filipino; pilipino', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fin', 'fi', 'Finnish', 'finnois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fiu', NULL, 'Finno-Ugrian languages', 'finno-ougriennes,  langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fon', NULL, 'Fon', 'fon', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fre', 'fr', 'French', 'français', 0x4672616ec3a7616973, 'Yes', 'fr_FR', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('frm', NULL, 'French, Middle (ca.1400-1600)', 'français moyen (1400-1600)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fro', NULL, 'French, Old (842-ca.1400)', 'français ancien (842-ca.1400)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('frr', NULL, 'Northern Frisian', 'frison septentrional', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('frs', NULL, 'Eastern Frisian', 'frison oriental', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fry', 'fy', 'Western Frisian', 'frison occidental', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ful', 'ff', 'Fulah', 'peul', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('fur', NULL, 'Friulian', 'frioulan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gaa', NULL, 'Ga', 'ga', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gay', NULL, 'Gayo', 'gayo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gba', NULL, 'Gbaya', 'gbaya', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gem', NULL, 'Germanic languages', 'germaniques, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('geo', 'ka', 'Georgian', 'géorgien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ger', 'de', 'German', 'allemand', 0x44657574736368, 'Yes', 'de_DE', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gez', NULL, 'Geez', 'guèze', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gil', NULL, 'Gilbertese', 'kiribati', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gla', 'gd', 'Gaelic; Scottish Gaelic', 'gaélique; gaélique écossais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gle', 'ga', 'Irish', 'irlandais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('glg', 'gl', 'Galician', 'galicien', 0x47616c65676f, 'Yes', 'gl_ES', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('glv', 'gv', 'Manx', 'manx; mannois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gmh', NULL, 'German, Middle High (ca.1050-1500)', 'allemand, moyen haut (ca. 1050-1500)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('goh', NULL, 'German, Old High (ca.750-1050)', 'allemand, vieux haut (ca. 750-1050)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gon', NULL, 'Gondi', 'gond', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gor', NULL, 'Gorontalo', 'gorontalo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('got', NULL, 'Gothic', 'gothique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('grb', NULL, 'Grebo', 'grebo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('grc', NULL, 'Greek, Ancient (to 1453)', 'grec ancien (jusqu''à 1453)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gre', 'el', 'Greek, Modern (1453-)', 'grec moderne (après 1453)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('grn', 'gn', 'Guarani', 'guarani', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gsw', NULL, 'Swiss German; Alemannic; Alsatian', 'suisse alémanique; alémanique; alsacien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('guj', 'gu', 'Gujarati', 'goudjrati', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('gwi', NULL, 'Gwich''in', 'gwich''in', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hai', NULL, 'Haida', 'haida', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hat', 'ht', 'Haitian; Haitian Creole', 'haïtien; créole haïtien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hau', 'ha', 'Hausa', 'haoussa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('haw', NULL, 'Hawaiian', 'hawaïen', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('heb', 'he', 'Hebrew', 'hébreu', 0xd7a2d6b4d791d6b0d7a8d6b4d799d7aa, 'Yes', 'he_IL', 'RTL');
INSERT INTO `i18n_language_codes` VALUES ('her', 'hz', 'Herero', 'herero', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hil', NULL, 'Hiligaynon', 'hiligaynon', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('him', NULL, 'Himachali', 'himachali', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hin', 'hi', 'Hindi', 'hindi', 0xe0a4b9e0a4bfe0a4a8e0a58de0a4a6e0a580, 'Yes', 'hi_IN', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hit', NULL, 'Hittite', 'hittite', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hmn', NULL, 'Hmong', 'hmong', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hmo', 'ho', 'Hiri Motu', 'hiri motu', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hrv', 'hr', 'Croatian', 'croate', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hsb', NULL, 'Upper Sorbian', 'haut-sorabe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hun', 'hu', 'Hungarian', 'hongrois', 0x4d6167796172, 'Yes', 'hu_HU', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('hup', NULL, 'Hupa', 'hupa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('iba', NULL, 'Iban', 'iban', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ibo', 'ig', 'Igbo', 'igbo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ice', 'is', 'Icelandic', 'islandais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ido', 'io', 'Ido', 'ido', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('iii', 'ii', 'Sichuan Yi; Nuosu', 'yi de Sichuan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ijo', NULL, 'Ijo languages', 'ijo, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('iku', 'iu', 'Inuktitut', 'inuktitut', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ile', 'ie', 'Interlingue; Occidental', 'interlingue', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ilo', NULL, 'Iloko', 'ilocano', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ina', 'ia', 'Interlingua (International Auxiliary Language Association)', 'interlingua (langue auxiliaire internationale)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('inc', NULL, 'Indic languages', 'indo-aryennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ind', 'id', 'Indonesian', 'indonésien', 0x42616861736120496e646f6e65736961, 'No', 'id_ID', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ine', NULL, 'Indo-European languages', 'indo-européennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('inh', NULL, 'Ingush', 'ingouche', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ipk', 'ik', 'Inupiaq', 'inupiaq', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ira', NULL, 'Iranian languages', 'iraniennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('iro', NULL, 'Iroquoian languages', 'iroquoises, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ita', 'it', 'Italian', 'italien', 0x4974616c69616e6f, 'Yes', 'it_IT', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('jav', 'jv', 'Javanese', 'javanais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('jbo', NULL, 'Lojban', 'lojban', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('jpn', 'ja', 'Japanese', 'japonais', 0xe697a5e69cace8aa9e, 'Yes', 'ja_JP', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('jpr', NULL, 'Judeo-Persian', 'judéo-persan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('jrb', NULL, 'Judeo-Arabic', 'judéo-arabe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kaa', NULL, 'Kara-Kalpak', 'karakalpak', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kab', NULL, 'Kabyle', 'kabyle', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kac', NULL, 'Kachin; Jingpho', 'kachin; jingpho', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kal', 'kl', 'Kalaallisut; Greenlandic', 'groenlandais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kam', NULL, 'Kamba', 'kamba', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kan', 'kn', 'Kannada', 'kannada', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kar', NULL, 'Karen languages', 'karen, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kas', 'ks', 'Kashmiri', 'kashmiri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kau', 'kr', 'Kanuri', 'kanouri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kaw', NULL, 'Kawi', 'kawi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kaz', 'kk', 'Kazakh', 'kazakh', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kbd', NULL, 'Kabardian', 'kabardien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kha', NULL, 'Khasi', 'khasi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('khi', NULL, 'Khoisan languages', 'khoïsan, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('khm', 'km', 'Central Khmer', 'khmer central', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kho', NULL, 'Khotanese; Sakan', 'khotanais; sakan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kik', 'ki', 'Kikuyu; Gikuyu', 'kikuyu', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kin', 'rw', 'Kinyarwanda', 'rwanda', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kir', 'ky', 'Kirghiz; Kyrgyz', 'kirghiz', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kmb', NULL, 'Kimbundu', 'kimbundu', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kok', NULL, 'Konkani', 'konkani', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kom', 'kv', 'Komi', 'kom', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kon', 'kg', 'Kongo', 'kongo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kor', 'ko', 'Korean', 'coréen', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kos', NULL, 'Kosraean', 'kosrae', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kpe', NULL, 'Kpelle', 'kpellé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('krc', NULL, 'Karachay-Balkar', 'karatchai balkar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('krl', NULL, 'Karelian', 'carélien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kro', NULL, 'Kru languages', 'krou, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kru', NULL, 'Kurukh', 'kurukh', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kua', 'kj', 'Kuanyama; Kwanyama', 'kuanyama; kwanyama', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kum', NULL, 'Kumyk', 'koumyk', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kur', 'ku', 'Kurdish', 'kurde', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('kut', NULL, 'Kutenai', 'kutenai', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lad', NULL, 'Ladino', 'judéo-espagnol', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lah', NULL, 'Lahnda', 'lahnda', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lam', NULL, 'Lamba', 'lamba', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lao', 'lo', 'Lao', 'lao', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lat', 'la', 'Latin', 'latin', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lav', 'lv', 'Latvian', 'letton', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lez', NULL, 'Lezghian', 'lezghien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lim', 'li', 'Limburgan; Limburger; Limburgish', 'limbourgeois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lin', 'ln', 'Lingala', 'lingala', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lit', 'lt', 'Lithuanian', 'lituanien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lol', NULL, 'Mongo', 'mongo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('loz', NULL, 'Lozi', 'lozi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ltz', 'lb', 'Luxembourgish; Letzeburgesch', 'luxembourgeois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lua', NULL, 'Luba-Lulua', 'luba-lulua', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lub', 'lu', 'Luba-Katanga', 'luba-katanga', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lug', 'lg', 'Ganda', 'ganda', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lui', NULL, 'Luiseno', 'luiseno', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lun', NULL, 'Lunda', 'lunda', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('luo', NULL, 'Luo (Kenya and Tanzania)', 'luo (Kenya et Tanzanie)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('lus', NULL, 'Lushai', 'lushai', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mac', 'mk', 'Macedonian', 'macédonien', 0xd0bcd0b0d0bad0b5d0b4d0bed0bdd181d0bad0b820d198d0b0d0b7d0b8d0ba, 'No', 'mk_MK', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mad', NULL, 'Madurese', 'madourais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mag', NULL, 'Magahi', 'magahi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mah', 'mh', 'Marshallese', 'marshall', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mai', NULL, 'Maithili', 'maithili', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mak', NULL, 'Makasar', 'makassar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mal', 'ml', 'Malayalam', 'malayalam', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('man', NULL, 'Mandingo', 'mandingue', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mao', 'mi', 'Maori', 'maori', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('map', NULL, 'Austronesian languages', 'austronésiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mar', 'mr', 'Marathi', 'marathe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mas', NULL, 'Masai', 'massaï', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('may', 'ms', 'Malay', 'malais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mdf', NULL, 'Moksha', 'moksa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mdr', NULL, 'Mandar', 'mandar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('men', NULL, 'Mende', 'mendé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mga', NULL, 'Irish, Middle (900-1200)', 'irlandais moyen (900-1200)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mic', NULL, 'Mi''kmaq; Micmac', 'mi''kmaq; micmac', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('min', NULL, 'Minangkabau', 'minangkabau', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mis', NULL, 'Uncoded languages', 'langues non codées', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mkh', NULL, 'Mon-Khmer languages', 'môn-khmer, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mlg', 'mg', 'Malagasy', 'malgache', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mlt', 'mt', 'Maltese', 'maltais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mnc', NULL, 'Manchu', 'mandchou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mni', NULL, 'Manipuri', 'manipuri', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mno', NULL, 'Manobo languages', 'manobo, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('moh', NULL, 'Mohawk', 'mohawk', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mon', 'mn', 'Mongolian', 'mongol', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mos', NULL, 'Mossi', 'moré', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mul', NULL, 'Multiple languages', 'multilingue', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mun', NULL, 'Munda languages', 'mounda, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mus', NULL, 'Creek', 'muskogee', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mwl', NULL, 'Mirandese', 'mirandais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('mwr', NULL, 'Marwari', 'marvari', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('myn', NULL, 'Mayan languages', 'maya, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('myv', NULL, 'Erzya', 'erza', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nah', NULL, 'Nahuatl languages', 'nahuatl, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nai', NULL, 'North American Indian languages', 'nord-amérindiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nap', NULL, 'Neapolitan', 'napolitain', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nau', 'na', 'Nauru', 'nauruan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nav', 'nv', 'Navajo; Navaho', 'navaho', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nbl', 'nr', 'Ndebele, South; South Ndebele', 'ndébélé du Sud', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nde', 'nd', 'Ndebele, North; North Ndebele', 'ndébélé du Nord', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ndo', 'ng', 'Ndonga', 'ndonga', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nds', NULL, 'Low German; Low Saxon; German, Low; Saxon, Low', 'bas allemand; bas saxon; allemand, bas; saxon, bas', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nep', 'ne', 'Nepali', 'népalais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('new', NULL, 'Nepal Bhasa; Newari', 'nepal bhasa; newari', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nia', NULL, 'Nias', 'nias', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nic', NULL, 'Niger-Kordofanian languages', 'nigéro-kordofaniennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('niu', NULL, 'Niuean', 'niué', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nno', 'nn', 'Norwegian Nynorsk; Nynorsk, Norwegian', 'norvégien nynorsk; nynorsk, norvégien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nob', 'nb', 'Bokmål, Norwegian; Norwegian Bokmål', 'norvégien bokmål', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nog', NULL, 'Nogai', 'nogaï; nogay', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('non', NULL, 'Norse, Old', 'norrois, vieux', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nor', 'no', 'Norwegian', 'norvégien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nqo', NULL, 'N''Ko', 'n''ko', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nso', NULL, 'Pedi; Sepedi; Northern Sotho', 'pedi; sepedi; sotho du Nord', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nub', NULL, 'Nubian languages', 'nubiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nwc', NULL, 'Classical Newari; Old Newari; Classical Nepal Bhasa', 'newari classique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nya', 'ny', 'Chichewa; Chewa; Nyanja', 'chichewa; chewa; nyanja', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nym', NULL, 'Nyamwezi', 'nyamwezi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nyn', NULL, 'Nyankole', 'nyankolé', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nyo', NULL, 'Nyoro', 'nyoro', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('nzi', NULL, 'Nzima', 'nzema', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('oci', 'oc', 'Occitan (post 1500)', 'occitan (après 1500)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('oji', 'oj', 'Ojibwa', 'ojibwa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ori', 'or', 'Oriya', 'oriya', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('orm', 'om', 'Oromo', 'galla', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('osa', NULL, 'Osage', 'osage', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('oss', 'os', 'Ossetian; Ossetic', 'ossète', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ota', NULL, 'Turkish, Ottoman (1500-1928)', 'turc ottoman (1500-1928)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('oto', NULL, 'Otomian languages', 'otomi, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('paa', NULL, 'Papuan languages', 'papoues, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pag', NULL, 'Pangasinan', 'pangasinan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pal', NULL, 'Pahlavi', 'pahlavi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pam', NULL, 'Pampanga; Kapampangan', 'pampangan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pan', 'pa', 'Panjabi; Punjabi', 'pendjabi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pap', NULL, 'Papiamento', 'papiamento', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pau', NULL, 'Palauan', 'palau', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('peo', NULL, 'Persian, Old (ca.600-400 B.C.)', 'perse, vieux (ca. 600-400 av. J.-C.)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('per', 'fa', 'Persian', 'persan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('phi', NULL, 'Philippine languages', 'philippines, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('phn', NULL, 'Phoenician', 'phénicien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pli', 'pi', 'Pali', 'pali', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pol', 'pl', 'Polish', 'polonais', 0x506f6c736b69, 'Yes', 'pl_PL', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pon', NULL, 'Pohnpeian', 'pohnpei', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('por', 'pt', 'Portuguese', 'portugais', 0x506f7274756775c3aa73, 'Yes', 'pt_BR', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pra', NULL, 'Prakrit languages', 'prâkrit, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pro', NULL, 'Provençal, Old (to 1500);Occitan, Old (to 1500)', 'provençal ancien (jusqu''à 1500); occitan ancien (jusqu''à 1500)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('pus', 'ps', 'Pushto; Pashto', 'pachto', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('qaa', NULL, 'Reserved for local use', 'réservée à l''usage local', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('que', 'qu', 'Quechua', 'quechua', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('raj', NULL, 'Rajasthani', 'rajasthani', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rap', NULL, 'Rapanui', 'rapanui', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rar', NULL, 'Rarotongan; Cook Islands Maori', 'rarotonga; maori des îles Cook', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('roa', NULL, 'Romance languages', 'romanes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('roh', 'rm', 'Romansh', 'romanche', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rom', NULL, 'Romany', 'tsigane', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rum', 'ro', 'Romanian', 'roumain; moldave', 0x526f6dc3a26ec483, 'Yes', 'ro_RO', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('run', 'rn', 'Rundi', 'rundi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rup', NULL, 'Aromanian; Arumanian; Macedo-Romanian', 'aroumain; macédo-roumain', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('rus', 'ru', 'Russian', 'russe', 0xd0a0d183d181d181d0bad0b8d0b9, 'Yes', 'ru_RU', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sad', NULL, 'Sandawe', 'sandawe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sag', 'sg', 'Sango', 'sango', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sah', NULL, 'Yakut', 'iakoute', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sai', NULL, 'South American Indian languages', 'sud-amérindiennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sal', NULL, 'Salishan languages', 'salishennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sam', NULL, 'Samaritan Aramaic', 'samaritain', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('san', 'sa', 'Sanskrit', 'sanskrit', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sas', NULL, 'Sasak', 'sasak', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sat', NULL, 'Santali', 'santal', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('scn', NULL, 'Sicilian', 'sicilien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sco', NULL, 'Scots', 'écossais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sel', NULL, 'Selkup', 'selkoupe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sem', NULL, 'Semitic languages', 'sémitiques, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sga', NULL, 'Irish, Old (to 900)', 'irlandais ancien (jusqu''à 900)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sgn', NULL, 'Sign Languages', 'langues des signes', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('shn', NULL, 'Shan', 'chan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sid', NULL, 'Sidamo', 'sidamo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sin', 'si', 'Sinhala; Sinhalese', 'singhalais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sio', NULL, 'Siouan languages', 'sioux, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sit', NULL, 'Sino-Tibetan languages', 'sino-tibétaines, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sla', NULL, 'Slavic languages', 'slaves, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('slo', 'sk', 'Slovak', 'slovaque', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('slv', 'sl', 'Slovenian', 'slovène', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sma', NULL, 'Southern Sami', 'sami du Sud', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sme', 'se', 'Northern Sami', 'sami du Nord', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('smi', NULL, 'Sami languages', 'sames, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('smj', NULL, 'Lule Sami', 'sami de Lule', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('smn', NULL, 'Inari Sami', 'sami d''Inari', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('smo', 'sm', 'Samoan', 'samoan', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sms', NULL, 'Skolt Sami', 'sami skolt', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sna', 'sn', 'Shona', 'shona', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('snd', 'sd', 'Sindhi', 'sindhi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('snk', NULL, 'Soninke', 'soninké', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sog', NULL, 'Sogdian', 'sogdien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('som', 'so', 'Somali', 'somali', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('son', NULL, 'Songhai languages', 'songhai, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sot', 'st', 'Sotho, Southern', 'sotho du Sud', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('spa', 'es', 'Spanish', 'espagnol; castillan', 0x45737061c3b16f6c, 'Yes', 'es_ES', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('srd', 'sc', 'Sardinian', 'sarde', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('srn', NULL, 'Sranan Tongo', 'sranan tongo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('srp', 'sr', 'Serbian', 'serbe', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('srr', NULL, 'Serer', 'sérère', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ssa', NULL, 'Nilo-Saharan languages', 'nilo-sahariennes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ssw', 'ss', 'Swati', 'swati', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('suk', NULL, 'Sukuma', 'sukuma', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sun', 'su', 'Sundanese', 'soundanais', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sus', NULL, 'Susu', 'soussou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('sux', NULL, 'Sumerian', 'sumérien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('swa', 'sw', 'Swahili', 'swahili', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('swe', 'sv', 'Swedish', 'suédois', 0x5376656e736b61, 'No', 'sv_SE', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('syc', NULL, 'Classical Syriac', 'syriaque classique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('syr', NULL, 'Syriac', 'syriaque', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tah', 'ty', 'Tahitian', 'tahitien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tai', NULL, 'Tai languages', 'tai, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tam', 'ta', 'Tamil', 'tamoul', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tat', 'tt', 'Tatar', 'tatar', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tel', 'te', 'Telugu', 'télougou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tem', NULL, 'Timne', 'temne', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ter', NULL, 'Tereno', 'tereno', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tet', NULL, 'Tetum', 'tetum', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tgk', 'tg', 'Tajik', 'tadjik', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tgl', 'tl', 'Tagalog', 'tagalog', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tha', 'th', 'Thai', 'thaï', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tib', 'bo', 'Tibetan', 'tibétain', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tig', NULL, 'Tigre', 'tigré', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tir', 'ti', 'Tigrinya', 'tigrigna', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tiv', NULL, 'Tiv', 'tiv', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tkl', NULL, 'Tokelau', 'tokelau', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tlh', NULL, 'Klingon; tlhIngan-Hol', 'klingon', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tli', NULL, 'Tlingit', 'tlingit', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tmh', NULL, 'Tamashek', 'tamacheq', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tog', NULL, 'Tonga (Nyasa)', 'tonga (Nyasa)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ton', 'to', 'Tonga (Tonga Islands)', 'tongan (Îles Tonga)', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tpi', NULL, 'Tok Pisin', 'tok pisin', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tsi', NULL, 'Tsimshian', 'tsimshian', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tsn', 'tn', 'Tswana', 'tswana', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tso', 'ts', 'Tsonga', 'tsonga', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tuk', 'tk', 'Turkmen', 'turkmène', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tum', NULL, 'Tumbuka', 'tumbuka', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tup', NULL, 'Tupi languages', 'tupi, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tur', 'tr', 'Turkish', 'turc', 0x54c3bc726bc3a765, 'No', 'tr_TR', 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tut', NULL, 'Altaic languages', 'altaïques, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tvl', NULL, 'Tuvalu', 'tuvalu', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('twi', 'tw', 'Twi', 'twi', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('tyv', NULL, 'Tuvinian', 'touva', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('udm', NULL, 'Udmurt', 'oudmourte', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('uga', NULL, 'Ugaritic', 'ougaritique', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('uig', 'ug', 'Uighur; Uyghur', 'ouïgour', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ukr', 'uk', 'Ukrainian', 'ukrainien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('umb', NULL, 'Umbundu', 'umbundu', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('und', NULL, 'Undetermined', 'indéterminée', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('urd', 'ur', 'Urdu', 'ourdou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('uzb', 'uz', 'Uzbek', 'ouszbek', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('vai', NULL, 'Vai', 'vaï', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ven', 've', 'Venda', 'venda', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('vie', 'vi', 'Vietnamese', 'vietnamien', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('vol', 'vo', 'Volapük', 'volapük', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('vot', NULL, 'Votic', 'vote', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wak', NULL, 'Wakashan languages', 'wakashanes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wal', NULL, 'Wolaitta; Wolaytta', 'wolaitta; wolaytta', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('war', NULL, 'Waray', 'waray', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('was', NULL, 'Washo', 'washo', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wel', 'cy', 'Welsh', 'gallois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wen', NULL, 'Sorbian languages', 'sorabes, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wln', 'wa', 'Walloon', 'wallon', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('wol', 'wo', 'Wolof', 'wolof', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('xal', NULL, 'Kalmyk; Oirat', 'kalmouk; oïrat', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('xho', 'xh', 'Xhosa', 'xhosa', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('yao', NULL, 'Yao', 'yao', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('yap', NULL, 'Yapese', 'yapois', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('yid', 'yi', 'Yiddish', 'yiddish', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('yor', 'yo', 'Yoruba', 'yoruba', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('ypk', NULL, 'Yupik languages', 'yupik, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zap', NULL, 'Zapotec', 'zapotèque', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zbl', NULL, 'Blissymbols; Blissymbolics; Bliss', 'symboles Bliss; Bliss', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zen', NULL, 'Zenaga', 'zenaga', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zha', 'za', 'Zhuang; Chuang', 'zhuang; chuang', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('znd', NULL, 'Zande languages', 'zandé, langues', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zul', 'zu', 'Zulu', 'zoulou', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zun', NULL, 'Zuni', 'zuni', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zxx', NULL, 'No linguistic content; Not applicable', 'pas de contenu linguistique; non applicable', NULL, 'No', NULL, 'LTR');
INSERT INTO `i18n_language_codes` VALUES ('zza', NULL, 'Zaza; Dimili; Dimli; Kirdki; Kirmanjki; Zazaki', 'zaza; dimili; dimli; kirdki; kirmanjki; zazaki', NULL, 'No', NULL, 'LTR');

-- --------------------------------------------------------

-- 
-- Table structure for table `i18n_messages`
-- 

CREATE TABLE `i18n_messages` (
  `id` int(11) NOT NULL auto_increment,
  `message` varchar(255) character set utf8 collate utf8_bin default NULL,
  `comment` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `message` (`message`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `i18n_messages` VALUES (null,'Welcome %1', '%1 = Name of the user');

-- --------------------------------------------------------

-- 
-- Table structure for table `i18n_translations`
-- 

CREATE TABLE `i18n_translations` (
  `id_message` int(11) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `translation` varbinary(5000) NOT NULL,
  `author` varchar(100) default NULL,
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_message`,`lang`),
  KEY `lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `i18n_translations` VALUES (1,'es_ES','Hola %1','sifo@harecoded.com', NOW());
-- --------------------------------------------------------

-- 
-- Table structure for table `i18n_translators`
-- 

CREATE TABLE `i18n_translators` (
  `id_account` int(11) NOT NULL,
  `lang` varchar(100) NOT NULL COMMENT 'List of translatable languages, or an * for all languages',
  PRIMARY KEY  (`id_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 
-- Constraints for table `i18n_translations`
-- 
ALTER TABLE `i18n_translations`
  ADD CONSTRAINT `i18n_translations_ibfk_3` FOREIGN KEY (`id_message`) REFERENCES `i18n_messages` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `i18n_translations_ibfk_5` FOREIGN KEY (`lang`) REFERENCES `i18n_language_codes` (`l10n`) ON UPDATE CASCADE;

-- 
-- Constraints for table `i18n_translators`
-- 
-- ALTER TABLE `i18n_translators`
--  ADD CONSTRAINT `i18n_translators_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `accounts` (`id_account`) ON DELETE CASCADE ON UPDATE CASCADE;