<?php

namespace Kavish\APIResponse;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

trait HasApiResponse
{
    /**
     * Prepare API response payload
     *
     * @param bool $status
     * @param array $data
     * @param array $errors
     * @param int $code
     * @param int $current_page
     * @param int $total_page
     *
     * @return JsonResponse
     */
    public function result($status = false, $data = [], $errors = [], $code = 200, $current_page = 1, $total_page = 1): JsonResponse
    {
        $errors = $this->parse_errors($errors);
        $meta   = [
            'status' => ($status) ? 'SUCCESS' : 'FAILED',
            'status_code' => $code,
            'current_page' => $current_page,
            'total_page' => $total_page
        ];

        if (empty($data)) {
            if (empty($errors)) {
                return Response::json(['meta' => $meta, 'data' => []], $code);
            }

            return Response::json(['meta' => $meta, 'error' => $errors], $code);
        }

        if (empty($errors)) {
            return Response::json(['meta' => $meta, 'data' => $data], $code);
        }

        return Response::json(['meta' => $meta, 'data' => $data, 'errors' => $errors], $code);
    }

    /**
     * Prepare errors for response payload
     *
     * @param $errors
     * @return array
     */
    public function parse_errors($errors): array
    {
        if ($errors instanceof \Exception) return ["key" => $errors->getCode(), "message" => $errors->getMessage()];

        foreach ($errors as $key => $messages) {
            $result[] = [
                'key' => (string)$key,
                'message' => is_array($messages) ? $messages[0] : $messages,
            ];
        }

        return $result ?? [];
    }
}
