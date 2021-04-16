<?php

/*
 * The MIT License
 *
 * Copyright 2020 Semyon Mamonov <semyon.mamonov@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AppBundle\Classes;

/**
 * Description of MiscellaneousUtils
 *
 * Generated: Jan 27, 2020 9:35:16 PM
 *  
 * @author Semyon Mamonov <semyon.mamonov@gmail.com>
 */
class MiscellaneousUtils {
    //put your code here
    
    
    
    const COUNTRY_CODES_2 = [
        "AC"=>"Ascension Island",
        "AD"=>"Andorra",
        "AE"=>"United Arab Emirates",
        "AF"=>"Afghanistan",
        "AG"=>"Antigua and Barbuda",
        "AI"=>"Anguilla",
        "AL"=>"Albania",
        "AM"=>"Armenia",
        "AO"=>"Angola",
        "AQ"=>"Antarctica",
        "AR"=>"Argentina",
        "AS"=>"American Samoa",
        "AT"=>"Austria",
        "AU"=>"Australia",
        "AW"=>"Aruba",
        "AX"=>"Åland Islands",
        "AZ"=>"Azerbaijan",
        "BA"=>"Bosnia and Herzegovina",
        "BB"=>"Barbados",
        "BD"=>"Bangladesh",
        "BE"=>"Belgium",
        "BF"=>"Burkina Faso",
        "BG"=>"Bulgaria",
        "BH"=>"Bahrain",
        "BI"=>"Burundi",
        "BJ"=>"Benin",
        "BL"=>"Saint Barthélemy",
        "BM"=>"Bermuda",
        "BN"=>"Brunei Darussalam",
        "BO"=>"Bolivia (Plurinational State of)",
        "BQ"=>"Bonaire, Sint Eustatius and Saba",
        "BR"=>"Brazil",
        "BS"=>"Bahamas",
        "BT"=>"Bhutan",
        "BV"=>"Bouvet Island",
        "BW"=>"Botswana",
        "BY"=>"Belarus",
        "BZ"=>"Belize",
        "CA"=>"Canada",
        "CC"=>"Cocos (Keeling) Islands",
        "CD"=>"Congo, Democratic Republic of the",
        "CF"=>"Central African Republic",
        "CG"=>"Congo",
        "CH"=>"Switzerland",
        "CI"=>"Côte d'Ivoire",
        "CK"=>"Cook Islands",
        "CL"=>"Chile",
        "CM"=>"Cameroon",
        "CN"=>"China",
        "CO"=>"Colombia",
        "CP"=>"Clipperton Island",
        "CR"=>"Costa Rica",
        "CU"=>"Cuba",
        "CV"=>"Cabo Verde",
        "CW"=>"Curaçao",
        "CX"=>"Christmas Island",
        "CY"=>"Cyprus",
        "CZ"=>"Czechia",
        "DE"=>"Germany",
        "DG"=>"Diego Garcia",
        "DJ"=>"Djibouti",
        "DK"=>"Denmark",
        "DM"=>"Dominica",
        "DO"=>"Dominican Republic",
        "DZ"=>"Algeria",
        "EA"=>"Ceuta, Melilla",
        "EC"=>"Ecuador",
        "EE"=>"Estonia",
        "EG"=>"Egypt",
        "EH"=>"Western Sahara",
        "ER"=>"Eritrea",
        "ES"=>"Spain",
        "ET"=>"Ethiopia",
        "EU"=>"European Union",
        "EZ"=>"Eurozone",
        "FI"=>"Finland",
        "FJ"=>"Fiji",
        "FK"=>"Falkland Islands (Malvinas)",
        "FM"=>"Micronesia (Federated States of)",
        "FO"=>"Faroe Islands",
        "FR"=>"France",
        "FX"=>"France, Metropolitan",
        "GA"=>"Gabon",
        "GB"=>"United Kingdom of Great Britain and Northern Ireland",
        "GD"=>"Grenada",
        "GE"=>"Georgia",
        "GF"=>"French Guiana",
        "GG"=>"Guernsey",
        "GH"=>"Ghana",
        "GI"=>"Gibraltar",
        "GL"=>"Greenland",
        "GM"=>"Gambia",
        "GN"=>"Guinea",
        "GP"=>"Guadeloupe",
        "GQ"=>"Equatorial Guinea",
        "GR"=>"Greece",
        "GS"=>"South Georgia and the South Sandwich Islands",
        "GT"=>"Guatemala",
        "GU"=>"Guam",
        "GW"=>"Guinea-Bissau",
        "GY"=>"Guyana",
        "HK"=>"Hong Kong",
        "HM"=>"Heard Island and McDonald Islands",
        "HN"=>"Honduras",
        "HR"=>"Croatia",
        "HT"=>"Haiti",
        "HU"=>"Hungary",
        "IC"=>"Canary Islands",
        "ID"=>"Indonesia",
        "IE"=>"Ireland",
        "IL"=>"Israel",
        "IM"=>"Isle of Man",
        "IN"=>"India",
        "IO"=>"British Indian Ocean Territory",
        "IQ"=>"Iraq",
        "IR"=>"Iran (Islamic Republic of)",
        "IS"=>"Iceland",
        "IT"=>"Italy",
        "JE"=>"Jersey",
        "JM"=>"Jamaica",
        "JO"=>"Jordan",
        "JP"=>"Japan",
        "KE"=>"Kenya",
        "KG"=>"Kyrgyzstan",
        "KH"=>"Cambodia",
        "KI"=>"Kiribati",
        "KM"=>"Comoros",
        "KN"=>"Saint Kitts and Nevis",
        "KP"=>"Korea (Democratic People's Republic of)",
        "KR"=>"Korea, Republic of",
        "KW"=>"Kuwait",
        "KY"=>"Cayman Islands",
        "KZ"=>"Kazakhstan",
        "LA"=>"Lao People's Democratic Republic",
        "LB"=>"Lebanon",
        "LC"=>"Saint Lucia",
        "LI"=>"Liechtenstein",
        "LK"=>"Sri Lanka",
        "LR"=>"Liberia",
        "LS"=>"Lesotho",
        "LT"=>"Lithuania",
        "LU"=>"Luxembourg",
        "LV"=>"Latvia",
        "LY"=>"Libya",
        "MA"=>"Morocco",
        "MC"=>"Monaco",
        "MD"=>"Moldova, Republic of",
        "ME"=>"Montenegro",
        "MF"=>"Saint Martin (French part)",
        "MG"=>"Madagascar",
        "MH"=>"Marshall Islands",
        "MK"=>"North Macedonia",
        "ML"=>"Mali",
        "MM"=>"Myanmar",
        "MN"=>"Mongolia",
        "MO"=>"Macao",
        "MP"=>"Northern Mariana Islands",
        "MQ"=>"Martinique",
        "MR"=>"Mauritania",
        "MS"=>"Montserrat",
        "MT"=>"Malta",
        "MU"=>"Mauritius",
        "MV"=>"Maldives",
        "MW"=>"Malawi",
        "MX"=>"Mexico",
        "MY"=>"Malaysia",
        "MZ"=>"Mozambique",
        "NA"=>"Namibia",
        "NC"=>"New Caledonia",
        "NE"=>"Niger",
        "NF"=>"Norfolk Island",
        "NG"=>"Nigeria",
        "NI"=>"Nicaragua",
        "NL"=>"Netherlands",
        "NO"=>"Norway",
        "NP"=>"Nepal",
        "NR"=>"Nauru",
        "NU"=>"Niue",
        "NZ"=>"New Zealand",
        "OM"=>"Oman",
        "PA"=>"Panama",
        "PE"=>"Peru",
        "PF"=>"French Polynesia",
        "PG"=>"Papua New Guinea",
        "PH"=>"Philippines",
        "PK"=>"Pakistan",
        "PL"=>"Poland",
        "PM"=>"Saint Pierre and Miquelon",
        "PN"=>"Pitcairn",
        "PR"=>"Puerto Rico",
        "PS"=>"Palestine, State of",
        "PT"=>"Portugal",
        "PW"=>"Palau",
        "PY"=>"Paraguay",
        "QA"=>"Qatar",
        "RE"=>"Réunion",
        "RO"=>"Romania",
        "RS"=>"Serbia",
        "RU"=>"Russian Federation",
        "RW"=>"Rwanda",
        "SA"=>"Saudi Arabia",
        "SB"=>"Solomon Islands",
        "SC"=>"Seychelles",
        "SD"=>"Sudan",
        "SE"=>"Sweden",
        "SG"=>"Singapore",
        "SH"=>"Saint Helena, Ascension and Tristan da Cunha",
        "SI"=>"Slovenia",
        "SJ"=>"Svalbard and Jan Mayen",
        "SK"=>"Slovakia",
        "SL"=>"Sierra Leone",
        "SM"=>"San Marino",
        "SN"=>"Senegal",
        "SO"=>"Somalia",
        "SR"=>"Suriname",
        "SS"=>"South Sudan",
        "ST"=>"Sao Tome and Principe",
        "SU"=>"USSR",
        "SV"=>"El Salvador",
        "SX"=>"Sint Maarten (Dutch part)",
        "SY"=>"Syrian Arab Republic",
        "SZ"=>"Eswatini",
        "TA"=>"Tristan da Cunha",
        "TC"=>"Turks and Caicos Islands",
        "TD"=>"Chad",
        "TF"=>"French Southern Territories",
        "TG"=>"Togo",
        "TH"=>"Thailand",
        "TJ"=>"Tajikistan",
        "TK"=>"Tokelau",
        "TL"=>"Timor-Leste",
        "TM"=>"Turkmenistan",
        "TN"=>"Tunisia",
        "TO"=>"Tonga",
        "TR"=>"Turkey",
        "TT"=>"Trinidad and Tobago",
        "TV"=>"Tuvalu",
        "TW"=>"Taiwan, Province of China",
        "TZ"=>"Tanzania, United Republic of",
        "UA"=>"Ukraine",
        "UG"=>"Uganda",
        "UK"=>"United Kingdom",
        "UM"=>"United States Minor Outlying Islands",
        "UN"=>"United Nations",
        "US"=>"United States of America",
        "UY"=>"Uruguay",
        "UZ"=>"Uzbekistan",
        "VA"=>"Holy See",
        "VC"=>"Saint Vincent and the Grenadines",
        "VE"=>"Venezuela (Bolivarian Republic of)",
        "VG"=>"Virgin Islands (British)",
        "VI"=>"Virgin Islands (U.S.)",
        "VN"=>"Viet Nam",
        "VU"=>"Vanuatu",
        "WF"=>"Wallis and Futuna",
        "WS"=>"Samoa",
        "YE"=>"Yemen",
        "YT"=>"Mayotte",
        "ZA"=>"South Africa",
        "ZM"=>"Zambia",
        "ZW"=>"Zimbabwe"
    ];


    /**
     * 
     * @param string $country
     * @param string $bankCode
     * @param string $accountNumber
     * @param int $bankLength
     * @param int $accountLength
     * @return false|string It returns IBAN or false if error occurred.
     */
    public static function getIBAN($country, $bankCode, $accountNumber, $bankLength = 6, $accountLength = 19){
        $country = strtoupper(trim($country));
        if (!array_key_exists($country, self::COUNTRY_CODES_2) ) {
            return false;
        }
        
        $padString = function ($str, $strLen){
            $result = false;
            $str = strtoupper(str_replace(' ', '', $str));
            if ( strlen($str) <=  $strLen ){
                $result = str_pad($str, $strLen, '0', STR_PAD_LEFT );
            }
            return $result;
        };
        
        $bankCode = $padString($bankCode,$bankLength);
        if (!$bankCode) {
            return false;
        }
        $accountNumber = $padString($accountNumber,$accountLength);
        if ( !$accountNumber ){
            return false;
        } 
        
        $chars = str_split($bankCode.$accountNumber.$country.'00');
        $bigNum = '';
        foreach ( $chars as $char ){
            $ordChar = ord($char);
            if ( $ordChar < 48 ||  ( $ordChar > 57 && $ordChar < 65 ) || $ordChar > 90  ){
                return false;
            }
            
            if ( $ordChar >= 65 && $ordChar <= 90 ){
                $bigNum .= $ordChar - 55;
            } else {
                $bigNum .= $char;
            }
        }
        
        $parts = str_split($bigNum, strlen((string)PHP_INT_MAX)-2 );  
        $rest = '';
        foreach ($parts as $part) {
            $rest = ($rest.$part) % 97;
        }

        return $country.str_pad(98-$rest,2,'0',STR_PAD_LEFT).$bankCode.$accountNumber;
    } 
    
    
    
    
}
