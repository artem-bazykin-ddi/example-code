<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\ClientFactory;
use App\Models\Client;
use Carbon\Carbon;
use Exception;

class ClientManager
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * ClientManager constructor.
     *
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string $name
     * @param int $contentModel
     * @param int $primerNoticeTiming
     * @param int $contentNoticeTiming
     * @param int $reflectionNoticeTiming
     *
     * @return Client
     */
    public function create(
        string $name,
        int $contentModel,
        int $primerNoticeTiming,
        int $contentNoticeTiming,
        int $reflectionNoticeTiming
    ): Client {
        $client = $this->clientFactory->create();
        $client->name = $name;
        $client->content_model = $contentModel;
        $client->primer_notice_timing = $primerNoticeTiming;
        $client->content_notice_timing = $contentNoticeTiming;
        $client->reflection_notice_timing = $reflectionNoticeTiming;

        $client->save();

        return $client;
    }

    /**
     * @param Client $client
     * @param array $data
     * @return Client
     */
    public function update(Client $client, array $data): Client
    {
        $client->fill($data);

        $client->update();

        return $client;
    }

    /**
     * @param Client $client
     * @return bool|null
     * @throws Exception
     */
    public function delete(Client $client): ?bool
    {

        return $client->delete() ?? true;
    }

    /**
     * @param Client $client
     */
    public function deactivate(Client $client): void
    {
        $client->deactivated_at = Carbon::now();
        $client->save();
    }

    /**
     * @param Client $client
     */
    public function activate(Client $client): void
    {
        $client->deactivated_at = null;
        $client->save();
    }
}
