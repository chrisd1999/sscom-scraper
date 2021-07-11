<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertisementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       return [
           'data' => [
               'id' => $this->id,
               'ss_id' => $this->ss_id,
               'ss_href' => $this->ss_href,
               'ss_img' => $this->ss_img,
               'short_description' => $this->short_description,
               'brand' => $this->brand,
               'model' => $this->model,
               'year' => $this->year,
               'engine_size' => $this->engine_size,
               'price' => $this->price,
               'location' => $this->location,
               'created_at' => $this->created_at,
           ],
       ];
    }
}
