<?php


namespace sales\Dataprovider\Assortment;


interface DataProviderInterface
{
    /**
     * @return Product[]
     */
    public function getProducts() : array;
}

class csvDataProvider implements DataProviderInterface{

    private array $result_array;

    /** 
     * description: the constructor
     */
    public function __construct( ?string $payload )
    {
        $lines = preg_split('/\s*\R\s*/', trim($payload), NULL, PREG_SPLIT_NO_EMPTY);
        $headers = str_getcsv( array_shift( $lines ), ';');
        $this->result_array = array();
        foreach ( $lines as $line ) {
            $row = array();
            foreach ( str_getcsv( $line, ';') as $key => $field )
                $row[ $headers[ $key ] ] = $field;

            $row = array_filter( $row );

            $this->result_array[] = $row;

        }
    }
 
    public function getProducts(): array {
        return $this->result_array;
    }
 }

 class jsonDataProvider implements DataProviderInterface{

    private array $result_array;

    /** 
     * description: the constructor
     */
    public function __construct( ?string $input )
    {
        try {
            $this->result_array = json_decode($input);
        } catch (Exception $e) {
            $this->result_array = array();
        }
    }
 
    public function getProducts(): array {
        return $this->result_array;
    }
 }