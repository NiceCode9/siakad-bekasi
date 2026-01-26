<?php

// ============================================
// TRAIT - HYBRID RESPONSE HELPER
// ============================================

// app/Traits/HybridResponse.php
namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

trait HybridResponse
{
    /**
     * Return view atau JSON tergantung request type
     */
    protected function hybridResponse($data, string $view, array $compact = [])
    {
        if (request()->wantsJson() || request()->ajax()) {
            return $this->jsonSuccess($data);
        }

        return view($view, array_merge(compact('data'), $compact));
    }

    /**
     * Success response (redirect atau JSON)
     */
    protected function successResponse(string $message, string $route = null, $data = null)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return $this->jsonSuccess($data, $message);
        }

        $redirect = $route ? redirect()->route($route) : redirect()->back();
        return $redirect->with('success', $message);
    }

    /**
     * Error response (redirect atau JSON)
     */
    protected function errorResponse(string $message, int $code = 400, $errors = null)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return $this->jsonError($message, $code, $errors);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $message)
            ->withErrors($errors ?? []);
    }

    /**
     * JSON Success Response
     */
    protected function jsonSuccess($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * JSON Error Response
     */
    protected function jsonError(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Validation Error Response (untuk form AJAX)
     */
    protected function validationErrorResponse($validator)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($validator);
    }
}
