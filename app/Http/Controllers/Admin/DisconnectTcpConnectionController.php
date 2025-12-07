<?php

namespace Expose\Server\Http\Controllers\Admin;

use Expose\Server\Configuration;
use Expose\Server\Connections\TcpControlConnection;
use Expose\Server\Contracts\ConnectionManager;
use Illuminate\Http\Request;
use Ratchet\ConnectionInterface;

class DisconnectTcpConnectionController extends AdminController
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
        $connection = $this->connectionManager->findControlConnectionForClientId($request->input('id'));

        if (! is_null($connection)) {
            $connection->close();

            $this->connectionManager->removeControlConnection($connection);
        }

        $httpConnection->send(respond_json([
            'tcp_connections' => collect($this->connectionManager->getConnections())
                ->filter(function ($connection) {
                    return get_class($connection) === TcpControlConnection::class;
                })
                ->values(),
        ]));
    }
}
