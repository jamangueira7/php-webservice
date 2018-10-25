<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->group([
    'prefix'=> 'api/clients',
], function () use ($router) {
    $router->get('','ClientsController@index');
    $router->get('{id}','ClientsController@show');
    $router->post('','ClientsController@store');
    $router->put('{id}','ClientsController@update');
    $router->delete('{id}','ClientsController@destory');
});

$router->group([
    'prefix'=> 'api/clients/{client}/addresses',
], function () use ($router) {
    $router->get('','AddressesController@index');
    $router->get('{id}','AddressesController@show');
    $router->post('','AddressesController@store');
    $router->put('{id}','AddressesController@update');
    $router->delete('{id}','AddressesController@destory');
});

$router->get('tcu', function (){
    $client = new \Zend\Soap\Client('http://contas.tcu.gov.br/debito/CalculoDebito?wsdl');
    echo "Informações do Servidor:";
    print_r($client->getOptions());
    echo "Funções:";
    print_r($client->getFunctions());
    echo "Tipos:";
    print_r($client->getTypes());
    echo "Resultado:";
    print_r($client->obterSaldoAtualizado([
        'parcelas' => [
            'parcela' => [
                'data' =>'1995-01-01',
                'tipo' =>'D',
                'valor' => 35000
            ]
        ],
        'aplicaJuros' => true,
        'dataAtualizacao' => '2016-01-01'
    ]));

});

$uri = 'http://son-soap.dev:8080';

$router->get('son-soap.wsdl', function () use ($uri){
    $autoDiscover = new \Zend\Soap\AutoDiscover();
    $autoDiscover->setUri("$uri/server");
    $autoDiscover->setServiceName('SONSOAP');
    $autoDiscover->addFunction('soma');
    $autoDiscover->handle();
});

$router->post('server', function () use ($uri){
  $server = new \Zend\Soap\Server("$uri/son-soap.wsdl",[
      'cache_wsdl' => WSDL_CACHE_NONE
  ]);
  $server->setUri("$uri/server");
  return $server->setReturnResponse(true)
      ->addFunction('soma')
      ->handle();
});

$router->get('soap-test', function () use ($uri){
    $client = new \Zend\Soap\Client("$uri/son-soap.wsdl",[
        'cache_wsdl' => WSDL_CACHE_NONE
    ]);
    print_r($client->soma(2,5));

});

/**
 * @param int $num1
 * @param int $num2
 * @return int
 */
function soma($num1, $num2)
{
    return $num1 + $num2;
}
