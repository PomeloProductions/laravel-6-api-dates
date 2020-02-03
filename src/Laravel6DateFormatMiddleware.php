<?php
declare(strict_types=1);

namespace PomeloProductions\Laravel6Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Laravel6DateFormatMiddleware
 * @package PomeloProductions\Laravel6Middleware
 */
class Laravel6DateFormatMiddleware
{
    /**
     * Checks all date formats before the response is sent to make sure that
     * any dates following the laravel 7+ ISO 8601 date format are output in the legacy format.
     *
     * @param $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $response->setData($this->formatData($data));
        }

        return $response;
    }

    /**
     * Formats all data in an array to the new date format
     *
     * @param array $data
     * @return array
     */
    public function formatData(array $data): array
    {
        $formattedData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $formattedData[$key] = $this->formatData($value);
            } elseif (is_string($value)) {
                $formattedData[$key] = $this->formatString($value);
            } else {
                $formattedData[$key] = $value;
            }
        }

        return $formattedData;
    }

    /**
     * Checks a string to see if it is in ISO date format, and then returns it in the pre Laravel 7 format
     *
     * @param string $value
     * @return string
     */
    public function formatString(string $value): string
    {
        // regex source https://stackoverflow.com/a/3143231
        if (preg_match("/\d{4}-[01]\d-[0-3]\dT[0-2]\d:[0-5]\d:[0-5]\d\.\d+([+-][0-2]\d:[0-5]\d|Z)/", $value)) {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        }
        return $value;
    }
}