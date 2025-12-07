<?php

namespace Expose\Server\Http\Controllers\Admin;

use Expose\Server\Configuration;
use Expose\Server\Contracts\ConnectionManager;
use Illuminate\Http\Request;
use Ratchet\ConnectionInterface;

class DisconnectSiteController extends AdminController
{
    /** @var ConnectionManager */
    protected $connectionManager;

    /** @var Configuration */
    protected $configuration;

    public function __construct(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;
    }

    public function handle(Request $request, ConnectionInterface $httpConnection)
    {
        if ($request->has('server_host')) {
            $connection = $this->connectionManager->findControlConnectionForSubdomainAndServerHost($request->input('id'), $request->input('server_host'));
        } else {
            $connection = $this->connectionManager->findControlConnectionForClientId($request->input('id'));
        }

        if (! is_null($connection)) {
            $connection->closeWithoutReconnect();

            $this->connectionManager->removeControlConnection($connection);
        }

        $httpConnection->send(respond_json([
            'sites' => $this->connectionManager->getConnections(),
        ]));
    }
}
