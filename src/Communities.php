<?php
/**
 * @section LICENSE
 * This file is part of Wikimania Scholarship Application.
 *
 * Wikimania Scholarship Application is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Wikimania Scholarship Application is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with Wikimania Scholarship Application.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @file
 * @copyright © 2013 Bryan Davis and Wikimedia Foundation.
 * @copyright © 2013 Calvin W. F. Siu, Wikimania 2013 Hong Kong organizing team
 * @copyright © 2012-2013 Katie Filbert, Wikimania 2012 Washington DC organizing team
 * @copyright © 2011 Harel Cain, Wikimania 2011 Haifa organizing team
 * @copyright © 2010 Wikimania 2010 Gdansk organizing team
 * @copyright © 2009 Wikimania 2009 Buenos Aires organizing team
 */

namespace Wikimania\Scholarship;

class Communities {

	/**
	 * Names of language communities.
	 *
	 * @var array $COMMUNITY_NAMES
	 */
	public static $COMMUNITY_NAMES = [
		// @codingStandardsIgnoreStart Line exceeds 100 characters
		'MULTILINGUAL' => 'I primarily/only contribute to Commons, Species, Data, or Incubator, and therefore cannot pick a language community',
		// @codingStandardsIgnoreEnd
		'AA' => 'Afar',
		'AB' => 'Abkhazian',
		'ACE' => 'Acehnese',
		'AF' => 'Afrikaans',
		'AK' => 'Akan',
		'ALS' => 'Alemannic',
		'AM' => 'Amharic',
		'AN' => 'Aragonese',
		'ANG' => 'Anglo-Saxon',
		'AR' => 'Arabic',
		'ARC' => 'Aramaic',
		'ARZ' => 'Egyptian Arabic',
		'AS' => 'Assamese',
		'AST' => 'Asturian',
		'AV' => 'Avar',
		'AY' => 'Aymara',
		'AZ' => 'Azerbaijani',
		'BA' => 'Bashkir',
		'BAR' => 'Bavarian',
		'BATSMG' => 'Samogitian',
		'BCL' => 'Central Bicolano',
		'BE' => 'Belarusian',
		'BEXOLD' => 'Belarusian (Taraškievica)',
		'BG' => 'Bulgarian',
		'BH' => 'Bihari',
		'BI' => 'Bislama',
		'BJN' => 'Banjar',
		'BM' => 'Bambara',
		'BN' => 'Bengali',
		'BO' => 'Tibetan',
		'BPY' => 'Bishnupriya Manipuri',
		'BR' => 'Breton',
		'BS' => 'Bosnian',
		'BUG' => 'Buginese',
		'BXR' => 'Buryat (Russia)',
		'CA' => 'Catalan',
		'CBKZAM' => 'Zamboanga Chavacano',
		'CDO' => 'Min Dong',
		'CE' => 'Chechen',
		'CEB' => 'Cebuano',
		'CH' => 'Chamorro',
		'CHO' => 'Choctaw',
		'CHR' => 'Cherokee',
		'CHY' => 'Cheyenne',
		'CKB' => 'Sorani',
		'CO' => 'Corsican',
		'CR' => 'Cree',
		'CRH' => 'Crimean Tatar',
		'CS' => 'Czech',
		'CSB' => 'Kashubian',
		'CU' => 'Old Church Slavonic',
		'CV' => 'Chuvash',
		'CY' => 'Welsh',
		'DA' => 'Danish',
		'DE' => 'German',
		'DIQ' => 'Zazaki',
		'DSB' => 'Lower Sorbian',
		'DV' => 'Divehi',
		'DZ' => 'Dzongkha',
		'EE' => 'Ewe',
		'EL' => 'Greek',
		'EML' => 'Emilian-Romagnol',
		'EN' => 'English',
		'EO' => 'Esperanto',
		'ES' => 'Spanish',
		'ET' => 'Estonian',
		'EU' => 'Basque',
		'EXT' => 'Extremaduran',
		'FA' => 'Persian',
		'FF' => 'Fula',
		'FI' => 'Finnish',
		'FIUVRO' => 'Võro',
		'FJ' => 'Fijian',
		'FO' => 'Faroese',
		'FR' => 'French',
		'FRP' => 'Franco-Provençal/Arpitan',
		'FRR' => 'North Frisian',
		'FUR' => 'Friulian',
		'FY' => 'West Frisian',
		'GA' => 'Irish',
		'GAG' => 'Gagauz',
		'GAN' => 'Gan',
		'GD' => 'Scottish Gaelic',
		'GL' => 'Galician',
		'GLK' => 'Gilaki',
		'GN' => 'Guarani',
		'GOT' => 'Gothic',
		'GU' => 'Gujarati',
		'GV' => 'Manx',
		'HA' => 'Hausa',
		'HAK' => 'Hakka',
		'HAW' => 'Hawaiian',
		'HE' => 'Hebrew',
		'HI' => 'Hindi',
		'HIF' => 'Fiji Hindi',
		'HO' => 'Hiri Motu',
		'HR' => 'Croatian',
		'HSB' => 'Upper Sorbian',
		'HT' => 'Haitian',
		'HU' => 'Hungarian',
		'HY' => 'Armenian',
		'HZ' => 'Herero',
		'IA' => 'Interlingua',
		'ID' => 'Indonesian',
		'IE' => 'Interlingue',
		'IG' => 'Igbo',
		'II' => 'Sichuan Yi',
		'IK' => 'Inupiak',
		'ILO' => 'Ilokano',
		'IO' => 'Ido',
		'IS' => 'Icelandic',
		'IT' => 'Italian',
		'IU' => 'Inuktitut',
		'JA' => 'Japanese',
		'JBO' => 'Lojban',
		'JV' => 'Javanese',
		'KA' => 'Georgian',
		'KAA' => 'Karakalpak',
		'KAB' => 'Kabyle',
		'KBD' => 'Kabardian Circassian',
		'KG' => 'Kongo',
		'KI' => 'Kikuyu',
		'KJ' => 'Kuanyama',
		'KK' => 'Kazakh',
		'KL' => 'Greenlandic',
		'KM' => 'Khmer',
		'KN' => 'Kannada',
		'KO' => 'Korean',
		'KOI' => 'Komi-Permyak',
		'KR' => 'Kanuri',
		'KRC' => 'Karachay-Balkar',
		'KS' => 'Kashmiri',
		'KSH' => 'Ripuarian',
		'KU' => 'Kurdish',
		'KV' => 'Komi',
		'KW' => 'Cornish',
		'KY' => 'Kirghiz',
		'LA' => 'Latin',
		'LAD' => 'Ladino',
		'LB' => 'Luxembourgish',
		'LBE' => 'Lak',
		'LEZ' => 'Lezgian',
		'LG' => 'Luganda',
		'LI' => 'Limburgish',
		'LIJ' => 'Ligurian',
		'LMO' => 'Lombard',
		'LN' => 'Lingala',
		'LO' => 'Lao',
		'LT' => 'Lithuanian',
		'LTG' => 'Latgalian',
		'LV' => 'Latvian',
		'MAI' => 'Maithili',
		'MAPBMS' => 'Banyumasan',
		'MDF' => 'Moksha',
		'MG' => 'Malagasy',
		'MH' => 'Marshallese',
		'MHR' => 'Meadow Mari',
		'MI' => 'Maori',
		'MIN' => 'Minangkabau',
		'MK' => 'Macedonian',
		'ML' => 'Malayalam',
		'MN' => 'Mongolian',
		'MO' => 'Moldovan',
		'MR' => 'Marathi',
		'MRJ' => 'Hill Mari',
		'MS' => 'Malay',
		'MT' => 'Maltese',
		'MUS' => 'Muscogee',
		'MWL' => 'Mirandese',
		'MY' => 'Burmese',
		'MYV' => 'Erzya',
		'MZN' => 'Mazandarani',
		'NA' => 'Nauruan',
		'NAH' => 'Nahuatl',
		'NAP' => 'Neapolitan',
		'NDS' => 'Low Saxon',
		'NDSNL' => 'Dutch Low Saxon',
		'NE' => 'Nepali',
		'NEW' => 'Newar / Nepal Bhasa',
		'NG' => 'Ndonga',
		'NL' => 'Dutch',
		'NN' => 'Norwegian (Nynorsk)',
		'NO' => 'Norwegian (Bokmål)',
		'NOV' => 'Novial',
		'NRM' => 'Norman',
		'NSO' => 'Northern Sotho',
		'NV' => 'Navajo',
		'NY' => 'Chichewa',
		'OC' => 'Occitan',
		'OM' => 'Oromo',
		'OR' => 'Oriya',
		'OS' => 'Ossetian',
		'PA' => 'Punjabi',
		'PAG' => 'Pangasinan',
		'PAM' => 'Kapampangan',
		'PAP' => 'Papiamentu',
		'PCD' => 'Picard',
		'PDC' => 'Pennsylvania German',
		'PFL' => 'Palatinate German',
		'PI' => 'Pali',
		'PIH' => 'Norfolk',
		'PL' => 'Polish',
		'PMS' => 'Piedmontese',
		'PNB' => 'Western Panjabi',
		'PNT' => 'Pontic',
		'PS' => 'Pashto',
		'PT' => 'Portuguese',
		'QU' => 'Quechua',
		'RM' => 'Romansh',
		'RMY' => 'Romani',
		'RN' => 'Kirundi',
		'RO' => 'Romanian',
		'ROARUP' => 'Aromanian',
		'ROATARA' => 'Tarantino',
		'RU' => 'Russian',
		'RUE' => 'Rusyn',
		'RW' => 'Kinyarwanda',
		'SAH' => 'Sakha',
		'SA' => 'Sanskrit',
		'SC' => 'Sardinian',
		'SCN' => 'Sicilian',
		'SCO' => 'Scots',
		'SD' => 'Sindhi',
		'SE' => 'Northern Sami',
		'SG' => 'Sango',
		'SH' => 'Serbo-Croatian',
		'SI' => 'Sinhalese',
		'SIMPLE' => 'Simple English',
		'SK' => 'Slovak',
		'SL' => 'Slovenian',
		'SM' => 'Samoan',
		'SN' => 'Shona',
		'SO' => 'Somali',
		'SQ' => 'Albanian',
		'SR' => 'Serbian',
		'SRN' => 'Sranan',
		'SS' => 'Swati',
		'ST' => 'Sesotho',
		'STQ' => 'Saterland Frisian',
		'SU' => 'Sundanese',
		'SV' => 'Swedish',
		'SW' => 'Swahili',
		'SZL' => 'Silesian',
		'TA' => 'Tamil',
		'TCY' => 'Tulu',
		'TE' => 'Telugu',
		'TET' => 'Tetum',
		'TG' => 'Tajik',
		'TH' => 'Thai',
		'TI' => 'Tigrinya',
		'TK' => 'Turkmen',
		'TL' => 'Tagalog',
		'TN' => 'Tswana',
		'TO' => 'Tongan',
		'TPI' => 'Tok Pisin',
		'TR' => 'Turkish',
		'TS' => 'Tsonga',
		'TT' => 'Tatar',
		'TUM' => 'Tumbuka',
		'TW' => 'Twi',
		'TY' => 'Tahitian',
		'TYV' => 'Tuvan',
		'UDM' => 'Udmurt',
		'UG' => 'Uyghur',
		'UK' => 'Ukrainian',
		'UR' => 'Urdu',
		'UZ' => 'Uzbek',
		'VE' => 'Venda',
		'VEC' => 'Venetian',
		'VEP' => 'Vepsian',
		'VI' => 'Vietnamese',
		'VLS' => 'West Flemish',
		'VO' => 'Volapük',
		'WA' => 'Walloon',
		'WAR' => 'Waray-Waray',
		'WO' => 'Wolof',
		'WUU' => 'Wu',
		'XAL' => 'Kalmyk',
		'XH' => 'Xhosa',
		'XMF' => 'Mingrelian',
		'YI' => 'Yiddish',
		'YO' => 'Yoruba',
		'ZA' => 'Zhuang',
		'ZEA' => 'Zeelandic',
		'ZH' => 'Chinese',
		'ZHCLASSICAL' => 'Classical Chinese',
		'ZHMINNAN' => 'Min Nan',
		'ZHYUE' => 'Cantonese',
		'ZU' => 'Zulu',
	];

	/**
	 * Construction not allowed for utility class.
	 */
	private function __construct() {
		// no-op
	}
}
