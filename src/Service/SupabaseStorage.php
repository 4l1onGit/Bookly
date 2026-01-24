<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SupabaseStorage 
{
    public function __construct(private HttpClientInterface $client, private string $url, private string $key, private string $bucket)
    {}
     
    
    public function upload(string $path, string $content): string {
        $endpoint = sprintf('%s/storage/v1/object/%s/%s', $this->url, $this->bucket, $path);

        $this->client->request('POST', $endpoint, [
            'headers' => [
                'apikey' => $this->key,
                'Authorization' => 'Bearer ' . $this->key,
                'Content-Type' => 'application/octet-stream',
                'x-upsert' => 'true',
            ],
            'body' => $content,
        ]);

        return $endpoint;
    }
}