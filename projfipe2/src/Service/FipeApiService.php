<?php
// src/Service/FipeApiService.php
namespace App\Service;

use App\Enum\VehicleType;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FipeApiService
{
    public function __construct(private HttpClientInterface $client) {}

    /**
     * Busca detalhes de um veículo na API FIPE e retorna o payload.
     *
     * @return array{Valor:string, Marca:string, Modelo:string, AnoModelo:int, Combustivel:string, CodigoFipe:string, MesReferencia:string, SiglaCombustivel:string}
     */
    public function getVehicleDetails(VehicleType $tipo, string $brandCode, string $modelCode, string $yearCode): array
    {
        $url = sprintf(
            'https://parallelum.com.br/fipe/api/v1/%s/marcas/%s/modelos/%s/anos/%s',
            $tipo->value,
            $brandCode,
            $modelCode,
            $yearCode
        );

        $response = $this->client->request('GET', $url);
        return $response->toArray();
    }
}