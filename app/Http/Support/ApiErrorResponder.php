<?php

namespace App\Http\Support;

use App\Domain\Whois\Exceptions\UserFacingException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ApiErrorResponder
{
    public static function fromException(Throwable $exception, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        if ($exception instanceof UserFacingException) {
            return response()->json([
                'message' => $exception->userMessage(),
                'code' => $exception->errorCode(),
            ], self::statusCode($exception));
        }

        if ($exception instanceof ValidationException) {
            return null;
        }

        if ($exception instanceof HttpException) {
            return null;
        }

        Log::error($exception->getMessage(), [
            'exception' => $exception,
            'url' => $request->fullUrl(),
        ]);

        return response()->json([
            'message' => 'Something went wrong on our end. Please try again in a moment.',
            'code' => 'server_error',
        ], 500);
    }

    private static function statusCode(Throwable $exception): int
    {
        $code = $exception->getCode();

        if ($code >= 400 && $code < 600) {
            return $code;
        }

        return 500;
    }
}
