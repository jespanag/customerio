<?php

declare(strict_types=1);

namespace Customerio\Region;

class RegionUsV2 implements RegionInterface
{
    public function trackUri(): string
    {
        return 'https://track.customer.io/api/v2/';
    }

    public function apiUri(): string
    {
        return 'https://api.customer.io/v2/';
    }
}
