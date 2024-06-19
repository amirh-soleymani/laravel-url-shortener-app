<?php

namespace App\Repositories;

use App\Models\Link;

class LinkRepository
{
    public function getById($id)
    {
        return Link::find($id);
    }

    public function getByOriginalUrl($originalUrl)
    {
        return Link::where('original_url', 'like', '%' . $originalUrl . '%')
            ->first();
    }

    public function getByShortenerUrl($shortenerUrl)
    {
        return Link::where('shortener_url', $shortenerUrl)
            ->first();
    }

    public function getByUser($userId)
    {
        return Link::where('user_id', $userId)
            ->get();
    }

    public function getMostClickedLinks($size)
    {
        return Link::orderBy('count', 'DESC')
            ->take($size)
            ->get();
    }

    public function checkExistsByShortenerUrl($shortenerUrl)
    {
        return Link::where('shortener_url', $shortenerUrl)
            ->exists();
    }

    public function createLink(array $data = [])
    {
        $link = new Link();
        $link->original_url = $data['original_url'];
        $link->shortener_url =$data['shortener_url'];
        $link->user_id = $data['user_id'];

        if (! $link->save()) {
            return null;
        }

        return $link;
    }

}
