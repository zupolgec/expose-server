<?php

namespace Expose\Server\Http\Controllers\Admin;

use Expose\Server\Contracts\UserRepository;
use Illuminate\Http\Request;
use Ratchet\ConnectionInterface;

class GetUsersController extends AdminController
{
    protected $keepConnectionOpen = true;

    /** @var UserRepository */
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle(Request $request, ConnectionInterface $httpConnection)
    {
        $this->userRepository
            ->paginateUsers($request->input('search', ''), (int) $request->input('perPage', 20), (int) $request->input('page', 1))
            ->then(function ($paginated) use ($httpConnection) {
                $httpConnection->send(
                    respond_json(['paginated' => $paginated])
                );

                $httpConnection->close();
            });
    }
}
