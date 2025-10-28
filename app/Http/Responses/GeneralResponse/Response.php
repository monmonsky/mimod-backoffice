<?php

namespace App\Http\Responses\GeneralResponse;

class Response
{
    private function generateMessage(string $code): string
    {
        $message = [
            "204" => "Data tidak ditemukan"
        ];

        return $message[$code] ?? 'false';
    }

    public function generateResponse(ResultBuilder $response)
    {
        $data = $response->getData();
        $statusCode = $response->getStatusCode();
        $status = $response->getStatus();
        $defaultMessage = $this->generateMessage($statusCode);

        // Always use message from ResultBuilder if provided, otherwise use default
        $message = $response->getMessage() ?? $defaultMessage;

        $res = array(
            'status'      => $status,
            'statusCode'  => $statusCode,
            'message'     => $message,
            'data'        => $data,
        );

        return $res;
    }
}
