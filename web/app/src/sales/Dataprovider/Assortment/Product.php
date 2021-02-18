<?php


namespace sales\Dataprovider\Assortment;


interface ProductInterface
{
    /**
     * To be implemented
     */
    public function getProduct();
}

class Utils {

    public static function converToPacking( ?string $packaging )
    {
        $packaging = strtolower($packaging);
        $associationValues = array(
            'case' => 'CA',
            'box' => 'BX',
            'bottle' => 'BO',
        );

        return isset($associationValues[$packaging]) ? $associationValues[$packaging] : null;

    }

    public static function converToBaseProductPackaging( ?string $baseProductPackaging )
    {
        $baseProductPackaging = strtolower($baseProductPackaging);
        $associationValues = array(
            'bottle' => 'BO',
            'can' => 'CN',
        );

        return isset($associationValues[$baseProductPackaging]) ? $associationValues[$baseProductPackaging] : null;

    }

    public static function converToBaseProductUnit( ?string $baseProductUnit )
    {
        $baseProductUnit = strtolower($baseProductUnit);
        $associationValues = array(
            'liters' => 'LT',
            'grams' => 'GR',
        );

        return isset($associationValues[$baseProductUnit]) ? $associationValues[$baseProductUnit] : null;

    }

    function normalizeDecimal($val, int $precision = 2): float
    {
        $input = str_replace(' ', '', $val);
        $number = str_replace(',', '.', $input);
        if (strpos($number, '.')) {
            $groups = explode('.', str_replace(',', '.', $number));
            $lastGroup = array_pop($groups);
            $number = implode('', $groups) . '.' . $lastGroup;
        }
        return bcadd($number, 0, $precision);
    }
}

class Product implements ProductInterface{
    /**
     * type: "string"
     * description: "The wholesaler's unique product identifier"
     * example: "ABC12345678"
     */
    private ?string $id;
    
    /**
     * type: "string"
     * description: The "Global Trade Identification Number" (GTIN/EAN), a public identifier for trade items, developed by GS1
     * example: "05449000061704"
     */
    private ?string $gtin;
    
    /**
     * type: "string"
     * description: "Manufacturer name"
     * example: "Beverages Ltd."
     */
    private ?string $manufacturer;
    
    /**
     * type: "string"
     * description: "Product name"
     * example: "Beverage 23, 6x 0.75 L"
     */
    private ?string $name;
    
    /**
     * type: "string"
     * description: Packaging of the product (standardized units, see external docs), for example a case (CA)
     * Avaiable options:
     *   - CA = case
     *   - BX = box
     *   - BO = bottle
     * example: "CA"
     */
    private ?string $packaging;

    /**
     * type: "string"
     * description: Packaging of the base product (standardized units, see external docs), for example a bottle (BO)
     * Avaiable options:
     *   - BO = bottle
     *   - CN = can
     * example: "BO"
     */
    private ?string $baseProductPackaging;

    /**
     * type: "string"
     * description: Unit of measurement of the base product (standardized units, see external docs), for example liters (LT)
     * Avaiable options:
     *   - LT = liters
     *   - GR = grams
     * example: "LT"
     */
    private ?string $baseProductUnit;

    /**
     * type: "number"
     * format: "float"
     * description: "Amount of contents in the given unit of measurement of the base product, for example 0.75 liters"
     * example: 0.75
     */
    private $baseProductAmount;

    /** 
     * type: "integer"
     * description: "Number of base products within the package, for example 6 bottles"
     * example: 6
     */
    private $baseProductQuantity;

    /** 
     * description: the constructor
     */
    public function __construct(
        ?string $id, 
        ?string $gtin,
        ?string $manufacturer,
        ?string $name,
        ?string $packaging,
        ?string $baseProductPackaging,
        ?string $baseProductUnit,
        $baseProductAmount,
        $baseProductQuantity
        )
    {
        $this->id = $id;
        $this->gtin = $gtin;
        $this->manufacturer = $manufacturer;
        $this->name = $name;

        // forcing the input to have only the values accepted
        $this->packaging = Utils::converToPacking($packaging);
        $this->baseProductPackaging = Utils::converToBaseProductPackaging($baseProductPackaging);
        $this->baseProductUnit = Utils::converToBaseProductUnit($baseProductUnit);

        // here convert any comma or dot type of numbers to float
        preg_match('/^.*?([\d]+(?:\.[\d]+)?).*?$/', $baseProductAmount, $output_array);
        $this->baseProductAmount = floatval(str_replace(',', '.', $output_array[0]));

        // here convert string to integer with sanityze
        $this->baseProductQuantity = intval($baseProductQuantity);
    }

    /**
     * Getter for id
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * Setter for id
     */
    public function setId(?string $id): void {
        $this->id = $id;
    }

    /**
     * Getter for gtin
     */
    public function getGtin(): string {
        return $this->gtin;
    }

    /**
     * Setter for gtin
     */
    public function setGtin(?string $gtin): void {
        $this->gtin = $gtin;
    }

    /**
     * Getter for manufacturer
     */
    public function getManufacturer(): string {
        return $this->manufacturer;
    }

    /**
     * Setter for manufacturer
     */
    public function setManufacturer(?string $manufacturer): void {
        $this->manufacturer = $manufacturer;
    }

    /**
     * Getter for name
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Setter for name
     */
    public function setName(?string $name): void {
        $this->name = $name;
    }

    /**
     * Getter for packaging
     */
    public function getPackaging(): Packaging {
        return $this->packaging;
    }

    /**
     * Setter for packaging
     */
    public function setPackaging(?string $packaging): void {
        $this->packaging = Utils::converToPacking($packaging);
    }

    /**
     * Getter for baseProductPackaging
     */
    public function getBaseProductPackaging(): string {
        return $this->baseProductPackaging;
    }

    /**
     * Setter for baseProductPackaging
     */
    public function setBaseProductPackaging(?string $baseProductPackaging): void {
        $this->baseProductPackaging = Utils::converToBaseProductPackaging($baseProductPackaging);
    }

    /**
     * Getter for baseProductUnit
     */
    public function getBaseProductUnit(): string {
        return $this->baseProductUnit;
    }

    /**
     * Setter for baseProductUnit
     */
    public function setBaseProductUnit(?string $baseProductUnit): void {
        $this->baseProductUnit = Utils::converToBaseProductUnit($baseProductUnit);
    }

    /**
     * Getter for baseProductAmount
     */
    public function getBaseProductAmount(): float {
        return $this->baseProductAmount;
    }

    /**
     * Setter for baseProductAmount
     */
    public function setBaseProductAmount(?float $baseProductAmount): void {
        $this->baseProductAmount = $baseProductAmount;
    }

    /**
     * Getter for baseProductQuantity
     */
    public function getBaseProductQuantity(): int {
        return $this->baseProductQuantity;
    }

    /**
     * Setter for baseProductQuantity
     */
    public function setBaseProductQuantity(?int $baseProductQuantity): void {
        $this->baseProductQuantity = $baseProductQuantity;
    }

    /**
     * Setter for the entire Product object
     */
    public function getProduct(): object {
        $product = (object) array();
        $product->id = $this->id;
        $product->gtin = $this->gtin;
        $product->manufacturer = $this->manufacturer;
        $product->name = $this->name;
        $product->packaging = $this->packaging;
        $product->baseProductPackaging = $this->baseProductPackaging;
        $product->baseProductUnit = $this->baseProductUnit;
        $product->baseProductAmount = $this->baseProductAmount;
        $product->baseProductQuantity = $this->baseProductQuantity;

        return $product;
    }
}