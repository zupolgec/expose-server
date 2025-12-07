<?php

namespace Expose\Server\Http\Controllers\Admin;

use Expose\Server\Configuration;
use Expose\Server\Contracts\LoggerRepository;
use Illuminate\Http\Request;
use Ratchet\ConnectionInterface;

class GetLogsController extends AdminController
{
    protected $keepConnectionOpen = true;

    /** @var Configuration */
    protected $configuration;

    /** @var LoggerRepository */
    protected $logger;

    public function __construct(LoggerRepository $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request, ConnectionInterface $httpConnection)
    {
        $subdomain = $request->input('subdomain');
        $this->logger->getLogs()
            ->then(function ($logs) use ($httpConnection) {
                $httpConnection->send(
                    respond_json(['logs' => $logs])
                );

                $httpConnection->close();
            });
    }
}
