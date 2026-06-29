<?php

namespace App\Services;

class GeoLocation
{

   public function lookup(string $ip): mixed
   {
      return json_decode(file_get_contents("https://api.ipapi.is?q={$ip}"));
   }
}