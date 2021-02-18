<?php
require '../app/vendor/autoload.php';
require '../app/src/sales/Dataprovider/Assortment/DataProvider.php';
require '../app/src/sales/Dataprovider/Assortment/Product.php';

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true,
]];
$app = new \Slim\App($config);

// Ping route
$app->get('/ping', function ($request, $response, $args) {
    return $response->write('pong');
});

// The route where we receive the payload csv or json format
$app->post('/data', function ($request, $response, $args) {

    $authorization_header = $request->getHeader('Authorization');
    if (!$authorization_header) return $response->write("You have NO access!")->withStatus(401);
    
    $content_type_header = $request->getHeader('Content_type');
    
    $result_array = array();

    switch ($content_type_header[0]) {
        case 'application/json':
            $parsedBody = $request->getParsedBody();
            $payload = $parsedBody['data'];
            $payload = json_encode($payload);
          
            try {
                $input = new sales\Dataprovider\Assortment\jsonDataProvider($payload);
                
                foreach($input->getProducts() as $element){
                    $x = new sales\Dataprovider\Assortment\Product(
                        $element->PRODUCT_IDENTIFIER,
                        $element->EAN_CODE_GTIN,
                        $element->BRAND,
                        $element->NAME,
                        $element->PACKAGE,
                        $element->VESSEL,
                        $element->ADDITIONAL_INFO,
                        $element->LITERS_PER_BOTTLE,
                        $element->BOTTLE_AMOUNT,
                    );
                    $the_response[] = $x->getProduct();
                    //echo json_encode($x->getProduct())."\n\n";
                }
            } catch (Exception $e) {
                $the_response = array(
                    'error' => $e->getMessage(),
                );
                return $response->withJson($the_response)->withStatus(404);
            }

            
            break;
        case 'text/csv':
            $payload = file_get_contents('php://input');
        
            try {
                $input = new sales\Dataprovider\Assortment\csvDataProvider($payload);
                
                foreach($input->getProducts() as $element){
                    var_dump($element);
                    $x = new sales\Dataprovider\Assortment\Product(
                        $element['id'],
                        $element['ean'],
                        $element['manufacturer'],
                        $element['product'],
                        $element['packaging product'],
                        $element['packaging unit'],
                        $element['description'],
                        $element['amount per unit'],
                        $element['items on stock (availability)'],
                    );
                    $the_response[] = $x->getProduct();
                    //echo json_encode($x->getProduct())."\n\n";
                }
            } catch (Exception $e) {
                $the_response = array(
                    'error' => $e->getMessage(),
                );
                return $response->withJson($the_response)->withStatus(404);
            }

            break;
        default:
        return $response->withJson('Current content type has no DataProvider defined')->withStatus(404);
    }

    return $response->withJson($the_response)->withStatus(201);
});

// Run app
$app->run();