<?php


namespace App\Domain\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ApiService
{
    private $cliente;

    public function autorizeService($url) : array
    {

        $this->cliente = new Client();

        try {
            $response = $this->client->request('GET', $url);
            return json_decode($response->getBody(), true);
        } catch (GuzzleException $exception) {
            return ['message' => 'Não Autorizado'];
        }

        
    }

}