<?php

namespace App\Http\Responses\GeneralResponse;

class ResultBuilder
{
    private $status = true;
    private $message = null;
    private $statusCode = '200';
    private $data = [];

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status) : ResultBuilder {
        $this->status = $status;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message) : ResultBuilder {
        $this->message = $message;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($status_code) : ResultBuilder {
        $this->statusCode = $status_code;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data) : ResultBuilder {
        $this->data = $data;
        return $this;
    }
}
