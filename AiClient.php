<?php

class AiClient
{
    private $apiKey;
    private $baseUrl;

    public function __construct($apiKey, $baseUrl = "https://api.openai.com/v1/chat/completions")
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public function generate($prompt, $model = "gpt-4o-mini")
    {
        $data = [
            "model" => $model,
            "messages" => [
                ["role" => "user", "content" => $prompt]
            ],
            "temperature" => 0.7
        ];

        $ch = curl_init($this->baseUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return "cURL Error: " . curl_error($ch);
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        if (isset($decoded['choices'][0]['message']['content'])) {
            return $decoded['choices'][0]['message']['content'];
        }

        return $response; // fallback raw output
    }
}
