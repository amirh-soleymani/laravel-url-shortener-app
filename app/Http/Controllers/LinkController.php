<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Predis\Client;

class LinkController extends BaseController
{
    protected $redisClient;

    public function __construct()
    {
        $this->redisClient = Client();
    }

    public function create(LinkRequest $linkRequest)
    {
        $originalLink = $linkRequest->get('link');
        $user = $linkRequest->get('userId');

        $linkStringCreationFlag = true;
        while ($linkStringCreationFlag) {
            $shortenerLink = Str::random(5);

            $checkShortenerLinkExist = Link::where('shortener_url', $shortenerLink)
                ->exists();
            if (!$checkShortenerLinkExist) {
                $linkStringCreationFlag = false;
            }
        }
        $link = Link::create([
            'shortener_url' => $shortenerLink,
            'original_url' => $originalLink,
            'user_id' => $user
        ]);

        $redisClient = new Client();
        $redisClient->set($shortenerLink, $originalLink);

        return $this->responseSuccess(array($link), 'Link Created Successfully');
    }

    public function mostClickedLinks($size)
    {
        $links = Link::orderBy('count', 'DESC')
            ->take($size)
            ->get();

        return $this->responseSuccess(array($links), 'Done');
    }

    public function linksByUser($user)
    {
        $links = Link::where('user_id', $user)
            ->get();

        return $this->responseSuccess(array($links), 'Done');
    }

    public function load($query)
    {
        $redisClient = new Client();
        $originalLink = $redisClient->get($query);
        if ($originalLink == '') {
            return $this->responseError("Url Not Found!");
        }

        ////// This Section Should Move to Background (Event)
            $link = Link::where('shortener_url', $query)
                ->first();
            if ($link) {
                $link->increment('count');
                $link->save();
            }
        /////////
        return Redirect::to('https://www.' . $originalLink);
    }

    public function search($query)
    {
        $link = Link::where('original_url', 'like', '%' . $query . '%')
            ->first();

        return $this->responseSuccess(array($link), 'Done');
    }
}
