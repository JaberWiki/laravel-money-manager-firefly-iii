<?php


/*
 * ShowController.php
 * Copyright (c) 2023 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Api\V2\Controllers\Model\PiggyBank;

use FireflyIII\Api\V2\Controllers\Controller;
use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Repositories\UserGroups\PiggyBank\PiggyBankRepositoryInterface;
use FireflyIII\Support\Http\Api\ValidatesUserGroupTrait;
use FireflyIII\Transformers\V2\PiggyBankTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class ShowController
 */
class ShowController extends Controller
{
    use ValidatesUserGroupTrait;

    private PiggyBankRepositoryInterface $repository;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(
            function ($request, $next) {
                $this->repository = app(PiggyBankRepositoryInterface::class);

                $userGroup = $this->validateUserGroup($request);
                if (null !== $userGroup) {
                    $this->repository->setUserGroup($userGroup);
                }

                return $next($request);
            }
        );
    }

    /**
     * @param Request $request
     *
     * TODO see autocomplete/accountcontroller for list.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $piggies     = $this->repository->getPiggyBanks();
        $pageSize    = (int)app('preferences')->getForUser(auth()->user(), 'listPageSize', 50)->data;
        $count       = $piggies->count();
        $piggies     = $piggies->slice(($this->parameters->get('page') - 1) * $pageSize, $pageSize);
        $paginator   = new LengthAwarePaginator($piggies, $count, $pageSize, $this->parameters->get('page'));
        $transformer = new PiggyBankTransformer();
        $transformer->setParameters($this->parameters); // give params to transformer

        return response()
            ->json($this->jsonApiList('piggy-banks', $paginator, $transformer))
            ->header('Content-Type', self::CONTENT_TYPE);
    }
}
