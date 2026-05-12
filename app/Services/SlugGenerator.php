<?php
namespace App\Services;

use Hidehalo\Nanoid\Client;

class SlugGenerator {
     public function __construct(private Client $client){}

     public function generate(): string {
          return $this->client->generateId(6);
     }
}