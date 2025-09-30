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
        $message = $this->generateMessage($statusCode);

        if ($statusCode == 200) {
            $message = $response->getMessage() ?? $message;
        }

        $res = array(
            'status'      => $status,
            'statusCode'  => $statusCode,
            'message'     => $message,
            'data'        => $data,
        );

        return $res;
    }
}
