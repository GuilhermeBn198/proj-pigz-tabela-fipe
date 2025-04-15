<?php
namespace App\Service;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Year;
use App\Enum\VehicleType;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Repository\YearRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FipeSyncService
{
    private array $tipos = [VehicleType::CARROS, VehicleType::MOTOS, VehicleType::CAMINHOES];
    private int $maxRetries = 1;
    private int $retryDelay = 5;

    public function __construct(
        private HttpClientInterface $client,
        private BrandRepository $brandRepo,
        private ModelRepository $modelRepo,
        private YearRepository $yearRepo,
        private EntityManagerInterface $em
    ) {}

    /**
     * @return array<string, array{brands_created:int, models_created:int, years_created:int}>
     */
    public function syncAll(): array
    {
        $summary = [];
        foreach ($this->tipos as $tipo) {
            $summary[$tipo->value] = $this->syncTipo($tipo);
        }
        return $summary;
    }

    private function syncTipo(VehicleType $tipo): array
    {
        $brandsCreated = $modelsCreated = $yearsCreated = 0;

        // BRANDS
        $data = $this->requestJson("https://parallelum.com.br/fipe/api/v1/{$tipo->value}/marcas");
        foreach ($data as $item) {
            $brand = $this->brandRepo->findOneBy(['fipeCode' => $item['codigo'], 'type' => $tipo]) ?: new Brand();
            $isNew = $brand->getId() === null;
            $brand->setFipeCode($item['codigo'])
                  ->setName($item['nome'])
                  ->setType($tipo);
            $this->em->persist($brand);
            $isNew && $brandsCreated++;
        }
        $this->em->flush();

        // MODELS
        foreach ($this->brandRepo->findBy(['type' => $tipo]) as $brand) {
            $modelsCreated += $this->syncModels($tipo, $brand);
        }

        // YEARS
        foreach ($this->modelRepo->findAll() as $model) {
            $yearsCreated += $this->syncYears($tipo, $model);
        }

        return [
            'brands_created' => $brandsCreated,
            'models_created' => $modelsCreated,
            'years_created'  => $yearsCreated,
        ];
    }

    private function syncModels(VehicleType $tipo, Brand $brand): int
    {
        $count = 0;
        $resp = $this->requestJson(
            "https://parallelum.com.br/fipe/api/v1/{$tipo->value}/marcas/{$brand->getFipeCode()}/modelos"
        );
        $models = $resp['modelos'] ?? [];
        foreach ($models as $m) {
            $model = $this->modelRepo->findOneBy(['fipeCode' => $m['codigo']]) ?: new Model();
            $isNew = $model->getId() === null;
            $model->setFipeCode($m['codigo'])
                  ->setName($m['nome'])
                  ->setBrand($brand);
            $this->em->persist($model);
            $isNew && $count++;
        }
        $this->em->flush();
        return $count;
    }

    private function syncYears(VehicleType $tipo, Model $model): int
    {
        $count = 0;
        $data = $this->requestJson(
            "https://parallelum.com.br/fipe/api/v1/{$tipo->value}/marcas/{$model->getBrand()->getFipeCode()}/modelos/{$model->getFipeCode()}/anos"
        );
        foreach ($data as $y) {
            $year = $this->yearRepo->findOneBy(['fipeCode' => $y['codigo']]) ?: new Year();
            $isNew = $year->getId() === null;
            $year->setFipeCode($y['codigo'])
                 ->setName($y['nome'])
                 ->setModel($model);
            $this->em->persist($year);
            $isNew && $count++;
        }
        $this->em->flush();
        return $count;
    }

    private function requestJson(string $url): array
    {
        $attempts = 0;
        while (true) {
            $response = $this->client->request('GET', $url);
            if ($response->getStatusCode() === 429) {
                if (++$attempts >= $this->maxRetries) {
                    throw new \RuntimeException("Max retries reached for {$url}");
                }
                sleep($this->retryDelay);
                continue;
            }
            return $response->toArray();
        }
    }
}