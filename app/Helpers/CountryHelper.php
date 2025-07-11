<?php

namespace App\Helpers;

class CountryHelper
{
    /**
     * Get country name by country code
     *
     * @param string $code
     * @return string
     */
    public static function getCountryName($code)
    {
        $countries = self::getCountries();
        return $countries[$code] ?? $code;
    }
    
    /**
     * Get all countries as an array of code => name
     *
     * @return array
     */
    public static function getCountries()
    {
        return [
            'US' => 'United States',
            'EG' => 'Egypt',
            'UK' => 'United Kingdom',
            'CA' => 'Canada',
            'AU' => 'Australia',
            'AE' => 'United Arab Emirates',
            'SA' => 'Saudi Arabia',
            'FR' => 'France',
            'DE' => 'Germany',
            'IT' => 'Italy',
            'ES' => 'Spain',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'PT' => 'Portugal',
            'IE' => 'Ireland',
            'GR' => 'Greece',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'RO' => 'Romania',
            'BG' => 'Bulgaria',
            'HR' => 'Croatia',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia',
            'LT' => 'Lithuania',
            'LV' => 'Latvia',
            'EE' => 'Estonia',
            'MT' => 'Malta',
            'CY' => 'Cyprus',
            'LU' => 'Luxembourg',
            'IS' => 'Iceland',
            'LI' => 'Liechtenstein',
            'TR' => 'Turkey',
            'RU' => 'Russia',
            'IN' => 'India',
            'CN' => 'China',
            'JP' => 'Japan',
            'KR' => 'South Korea',
            'SG' => 'Singapore',
            'MY' => 'Malaysia',
            'TH' => 'Thailand',
            'ID' => 'Indonesia',
            'PH' => 'Philippines',
            'VN' => 'Vietnam',
            'BR' => 'Brazil',
            'MX' => 'Mexico',
            'AR' => 'Argentina',
            'CL' => 'Chile',
            'CO' => 'Colombia',
            'PE' => 'Peru',
            'VE' => 'Venezuela',
            'ZA' => 'South Africa',
            'NG' => 'Nigeria',
            'KE' => 'Kenya',
            'JO' => 'Jordan',
            'LB' => 'Lebanon',
            'KW' => 'Kuwait',
            'QA' => 'Qatar',
            'BH' => 'Bahrain',
            'OM' => 'Oman',
            'MA' => 'Morocco',
            'TN' => 'Tunisia',
            'DZ' => 'Algeria',
            'LY' => 'Libya',
            'SD' => 'Sudan',
            'NZ' => 'New Zealand',
        ];
    }
} 