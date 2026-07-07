<?php

declare(strict_types=1);

namespace App\Domain\Tenancy\Http\Middleware;

use App\Domain\Tenancy\CurrentTenant;
use App\Domain\Tenancy\Support\HostNormalizer;
use App\Domain\Tenancy\Support\TenantDatabaseContext;
use App\Domain\Tenancy\Support\TenantResolver;
use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenantFromHost
{
    public function __construct(
        private readonly HostNormalizer $hostNormalizer,
        private readonly TenantResolver $tenantResolver,
        private readonly TenantDatabaseContext $tenantDatabaseContext,
        private readonly ConfigRepository $config,
    ) {}

    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $rawHost = $request->server->get('HTTP_HOST', $request->headers->get('host', ''));

        if (! is_string($rawHost)) {
            $rawHost = '';
        }

        try {
            $normalizedHost = $this->hostNormalizer->normalize($rawHost);
        } catch (InvalidArgumentException) {
            return response()->json(['message' => 'Bad Request'], 400);
        }

        /** @var array<int, string> $platformHosts */
        $platformHosts = $this->config->get('tenancy.platform_hosts', []);

        if (in_array($normalizedHost, $platformHosts, true)) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $currentTenant = $this->tenantResolver->resolve($normalizedHost);

        if ($currentTenant === null) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        app()->instance(CurrentTenant::class, $currentTenant);

        try {
            $response = $this->tenantDatabaseContext->run(
                $currentTenant,
                static function () use ($next, $request): Response|JsonResponse {
                    $response = $next($request);

                    /** @var Response|JsonResponse $response */
                    return $response;
                },
            );

            /** @var Response|JsonResponse $response */
            return $response;
        } finally {
            app()->forgetInstance(CurrentTenant::class);
        }
    }
}
