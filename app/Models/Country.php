<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Lwwcas\LaravelCountries\Models\Country as BaseCountry;
use Lwwcas\LaravelCountries\Models\CountryCoordinates;
use Lwwcas\LaravelCountries\Models\CountryExtras;
use Lwwcas\LaravelCountries\Models\CountryGeographical;
use Lwwcas\LaravelCountries\Models\CountryRegion;
use Lwwcas\LaravelCountries\Models\CountryTranslation;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id Primary key: auto-incremented country ID.
 * @property int $lc_region_id Foreign key referencing the region this country belongs to.
 * @property string $uid Unique ULID for the country, sortable and lexicographically unique.
 * @property string $official_name The official name of the country (e.g., "United States of America").
 * @property string|null $capital Capital city of the country.
 * @property string $iso_alpha_2 ISO 3166-1 alpha-2 code (e.g., "US" for the United States).
 * @property string $iso_alpha_3 ISO 3166-1 alpha-3 code (e.g., "USA" for the United States).
 * @property int|null $iso_numeric ISO 3166-1 numeric code for the country.
 * @property string|null $international_phone International dialing code (e.g., +1 for the United States).
 * @property string|null $geoname_id Geonames ID for geographical reference.
 * @property string|null $wmo World Meteorological Organization (WMO) abbreviation.
 * @property Carbon|null $independence_day Year the country gained independence.
 * @property string|null $population The country’s population.
 * @property string|null $area Area of the country in square kilometers (km²).
 * @property string|null $gdp Gross Domestic Product (GDP) in billions of US dollars.
 * @property array<array-key, mixed>|null $languages List of official languages spoken in the country.
 * @property array<array-key, mixed>|null $tld Top-level domain(s) used by the country (e.g., ".us" for the United States).
 * @property array<array-key, mixed>|null $alternative_tld Alternative top-level domains the country may use.
 * @property array<array-key, mixed>|null $borders List of neighboring countries sharing borders.
 * @property array<array-key, mixed>|null $timezones Main and other timezones used in the country.
 * @property array<array-key, mixed>|null $currency Currency details including name, code, symbol, banknotes, coins, and unit conversions.
 * @property array<array-key, mixed>|null $flag_emoji Emoji representation of the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors Base colors of the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors_web Web-safe color codes for the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors_contrast Contrasting colors for use with flag colors for readability.
 * @property array<array-key, mixed>|null $flag_colors_hex Hexadecimal color codes for the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors_rgb RGB color values for the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors_cmyk CMYK color values for the country’s flag.
 * @property array<array-key, mixed>|null $flag_colors_hsl HSL (Hue, Saturation, Lightness) color values for the flag.
 * @property array<array-key, mixed>|null $flag_colors_hsv HSV (Hue, Saturation, Value) color values for the flag.
 * @property array<array-key, mixed>|null $flag_colors_pantone Pantone color codes for the country’s flag.
 * @property bool $is_visible Visibility flag to determine if the country is publicly visible.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CountryCoordinates|null $coordinates
 * @property-read CountryExtras|null $extras
 * @property-read CountryGeographical|null $geographical
 * @property-read mixed $iso_alpha2
 * @property-read mixed $iso_alpha3
 * @property-read CountryRegion $region
 * @property-read CountryTranslation|null $translation
 * @property-read Collection<int, CountryTranslation> $translations
 * @property-read int|null $translations_count
 *
 * @method static Builder<static>|Country listsTranslations(string $translationField)
 * @method static Builder<static>|Country newModelQuery()
 * @method static Builder<static>|Country newQuery()
 * @method static Builder<static>|Country notTranslatedIn(?string $locale = null)
 * @method static Builder<static>|Country orWhereGeoname($geonameId)
 * @method static Builder<static>|Country orWhereIso(string $iso)
 * @method static Builder<static>|Country orWhereIsoAlpha2(string $isoAlpha2)
 * @method static Builder<static>|Country orWhereIsoAlpha3(string $isoAlpha2)
 * @method static Builder<static>|Country orWhereIsoNumeric(string $isoNumeric)
 * @method static Builder<static>|Country orWhereName(string $name)
 * @method static Builder<static>|Country orWhereNameLike(string $name)
 * @method static Builder<static>|Country orWhereOficialName($officialName)
 * @method static Builder<static>|Country orWhereOficialNameLike($officialName)
 * @method static Builder<static>|Country orWherePhoneCode($internationalPhone)
 * @method static Builder<static>|Country orWhereSlug(string $slug)
 * @method static Builder<static>|Country orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder<static>|Country orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder<static>|Country orWhereUid($uid)
 * @method static Builder<static>|Country orderByName(string $sortMethod = 'asc')
 * @method static Builder<static>|Country orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder<static>|Country query()
 * @method static Builder<static>|Country translated()
 * @method static Builder<static>|Country translatedIn(?string $locale = null)
 * @method static Builder<static>|Country whereAlternativeTld($value)
 * @method static Builder<static>|Country whereArea($value)
 * @method static Builder<static>|Country whereAreaKm2(string $area)
 * @method static Builder<static>|Country whereBorder(string $board)
 * @method static Builder<static>|Country whereBorders($value)
 * @method static Builder<static>|Country whereCapital($value)
 * @method static Builder<static>|Country whereCreatedAt($value)
 * @method static Builder<static>|Country whereCurrencies(array $currencies)
 * @method static Builder<static>|Country whereCurrency($value)
 * @method static Builder<static>|Country whereCurrencyCode(string $currency)
 * @method static Builder<static>|Country whereCurrencyCodes(array $currencies)
 * @method static Builder<static>|Country whereCurrencyName(string $currency)
 * @method static Builder<static>|Country whereCurrencyNames(array $currencies)
 * @method static Builder<static>|Country whereDomain(string $domain)
 * @method static Builder<static>|Country whereDomainAlternative(string $domain)
 * @method static Builder<static>|Country whereDomains(array $domains)
 * @method static Builder<static>|Country whereDomainsAlternative(array $domains)
 * @method static Builder<static>|Country whereFlagByManyColors(array $names, string $tableName)
 * @method static Builder<static>|Country whereFlagByOneColor(string $name, string $tableName)
 * @method static Builder<static>|Country whereFlagColor(array|string $name)
 * @method static Builder<static>|Country whereFlagColorCMYK(array|string $cmyk)
 * @method static Builder<static>|Country whereFlagColorHSL(array|string $hsl)
 * @method static Builder<static>|Country whereFlagColorHSV(array|string $hsv)
 * @method static Builder<static>|Country whereFlagColorHex(array|string $hex)
 * @method static Builder<static>|Country whereFlagColorPantone(array|string $pantone)
 * @method static Builder<static>|Country whereFlagColorRGB(array|string $rgb)
 * @method static Builder<static>|Country whereFlagColorWeb(array|string $name)
 * @method static Builder<static>|Country whereFlagColors($value)
 * @method static Builder<static>|Country whereFlagColorsCmyk($value)
 * @method static Builder<static>|Country whereFlagColorsContrast($value)
 * @method static Builder<static>|Country whereFlagColorsHex($value)
 * @method static Builder<static>|Country whereFlagColorsHsl($value)
 * @method static Builder<static>|Country whereFlagColorsHsv($value)
 * @method static Builder<static>|Country whereFlagColorsPantone($value)
 * @method static Builder<static>|Country whereFlagColorsRgb($value)
 * @method static Builder<static>|Country whereFlagColorsWeb($value)
 * @method static Builder<static>|Country whereFlagContrast(array|string $contrast)
 * @method static Builder<static>|Country whereFlagEmoji($value)
 * @method static Builder<static>|Country whereGdp($value)
 * @method static Builder<static>|Country whereGeoname(int $geonameId)
 * @method static Builder<static>|Country whereGeonameId($value)
 * @method static Builder<static>|Country whereId($value)
 * @method static Builder<static>|Country whereIndependenceAfter(string $date)
 * @method static Builder<static>|Country whereIndependenceBefore(string $date)
 * @method static Builder<static>|Country whereIndependenceBetweenDates($startDate, $endDate)
 * @method static Builder<static>|Country whereIndependenceDay($value)
 * @method static Builder<static>|Country whereIndependenceMonth(int $month)
 * @method static Builder<static>|Country whereIndependenceYear(int $year)
 * @method static Builder<static>|Country whereInternationalPhone($value)
 * @method static Builder<static>|Country whereIsVisible($value)
 * @method static Builder<static>|Country whereIso(string $iso)
 * @method static Builder<static>|Country whereIsoAlpha2($value)
 * @method static Builder<static>|Country whereIsoAlpha3($value)
 * @method static Builder<static>|Country whereIsoNumeric($value)
 * @method static Builder<static>|Country whereLanguage(string $language)
 * @method static Builder<static>|Country whereLanguages($value)
 * @method static Builder<static>|Country whereLcRegionId($value)
 * @method static Builder<static>|Country whereName(string $name)
 * @method static Builder<static>|Country whereNameLike(string $name)
 * @method static Builder<static>|Country whereOfficialName($value)
 * @method static Builder<static>|Country whereOficialName(string $officialName)
 * @method static Builder<static>|Country whereOficialNameLike(string $officialName)
 * @method static Builder<static>|Country wherePhoneCode(string $internationalPhone)
 * @method static Builder<static>|Country wherePopulation($value)
 * @method static Builder<static>|Country whereSlug(string $slug)
 * @method static Builder<static>|Country whereTimezones($value)
 * @method static Builder<static>|Country whereTld($value)
 * @method static Builder<static>|Country whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder<static>|Country whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder<static>|Country whereUid($value)
 * @method static Builder<static>|Country whereUpdatedAt($value)
 * @method static Builder<static>|Country whereWmo($value)
 * @method static Builder<static>|Country whereWmoCode(string $wmo)
 * @method static Builder<static>|Country whereWorldMeteorologicalOrganizationCode(string $wmo)
 * @method static Builder<static>|Country withTranslation(?string $locale = null)
 *
 * @mixin \Eloquent
 */
class Country extends BaseCountry
{
    use CentralConnection;
}
