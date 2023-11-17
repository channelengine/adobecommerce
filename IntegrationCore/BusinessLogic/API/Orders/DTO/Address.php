<?php


namespace ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Data\DataTransferObject;

/**
 * Class Address
 *
 * @package ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO
 */
class Address extends DataTransferObject
{
    /**
     * The first address line.
     *
     * @var string | null
     */
    protected $line1;
    /**
     * The second address line.
     *
     * @var string | null
     */
    protected $line2;
    /**
     * The third address line.
     *
     * @var string | null
     */
    protected $line3;
    /**
     * Gender.
     *
     * @var string
     */
    protected $gender;
    /**
     * Company name.
     *
     * @var string | null
     */
    protected $companyName;
    /**
     * First name of the customer.
     *
     * @var string | null
     */
    protected $firstName;
    /**
     * Last name of the customer.
     *
     * @var string | null
     */
    protected $lastName;
    /**
     * The name of the street.
     *
     * @var string | null
     */
    protected $streetName;
    /**
     * The house number.
     *
     * @var string | null
     */
    protected $houseNumber;
    /**
     * Addition to the house number.
     *
     * @var string | null
     */
    protected $houseNumberAddition;
    /**
     * Zip code.
     *
     * @var string | null
     */
    protected $zipCode;
    /**
     * City.
     *
     * @var string | null
     */
    protected $city;
    /**
     * Region.
     *
     * @var string | null
     */
    protected $region;
    /**
     * Country iso code.
     *
     * @var string
     */
    protected $countryIso;
    /**
     * The address as a single string: use in case the address lines
     * are entered as single lines and later parsed into street,
     * house number and house number addition.
     *
     * @var string | null
     */
    protected $original;

    /**
     * @return string|null
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param string|null $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return string|null
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param string|null $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return string|null
     */
    public function getLine3()
    {
        return $this->line3;
    }

    /**
     * @param string|null $line3
     */
    public function setLine3($line3)
    {
        $this->line3 = $line3;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string|null $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string|null $streetName
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string|null
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @param string|null $houseNumber
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string|null
     */
    public function getHouseNumberAddition()
    {
        return $this->houseNumberAddition;
    }

    /**
     * @param string|null $houseNumberAddition
     */
    public function setHouseNumberAddition($houseNumberAddition)
    {
        $this->houseNumberAddition = $houseNumberAddition;
    }

    /**
     * @return string|null
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getCountryIso()
    {
        return $this->countryIso;
    }

    /**
     * @param string $countryIso
     */
    public function setCountryIso($countryIso)
    {
        $this->countryIso = $countryIso;
    }

    /**
     * @return string|null
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @param string|null $original
     */
    public function setOriginal($original)
    {
        $this->original = $original;
    }

    /**
     * Creates instance of the data transfer object from an array.
     *
     * @param array $data
     *
     * @return Address
     */
    public static function fromArray(array $data)
    {
        $address = new static();

        $address->setLine1(static::getDataValue($data, 'Line1', null));
        $address->setLine2(static::getDataValue($data, 'Line2', null));
        $address->setLine3(static::getDataValue($data, 'Line3', null));
        $address->setGender(static::getDataValue($data, 'Gender', null));
        $address->setCompanyName(static::getDataValue($data, 'CompanyName', null));
        $address->setFirstName(static::getDataValue($data, 'FirstName', null));
        $address->setLastName(static::getDataValue($data, 'LastName', null));
        $address->setStreetName(static::getDataValue($data, 'StreetName', null));
        $address->setHouseNumber(static::getDataValue($data, 'HouseNr', null));
        $address->setHouseNumberAddition(static::getDataValue($data, 'HouseNrAddition', null));
        $address->setZipCode(static::getDataValue($data, 'ZipCode', null));
        $address->setCity(static::getDataValue($data, 'City', null));
        $address->setRegion(static::getDataValue($data, 'Region', null));
        $address->setCountryIso(static::getDataValue($data, 'CountryIso', null));
        $address->setOriginal(static::getDataValue($data, 'Original', null));

        return $address;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'Line1' => $this->getLine1(),
            'Line2' => $this->getLine2(),
            'Line3' => $this->getLine3(),
            'Gender' => $this->getGender(),
            'CompanyName' => $this->getCompanyName(),
            'FirstName' => $this->getFirstName(),
            'LastName' => $this->getLastName(),
            'StreetName' => $this->getStreetName(),
            'HouseNr' => $this->getHouseNumber(),
            'HouseNrAddition' => $this->getHouseNumberAddition(),
            'ZipCode' => $this->getZipCode(),
            'City' => $this->getCity(),
            'Region' => $this->getRegion(),
            'CountryIso' => $this->getCountryIso(),
            'Original' => $this->getOriginal(),
        ];
    }
}