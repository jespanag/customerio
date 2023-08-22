<?php

declare(strict_types=1);

namespace Customerio;

use Customerio\Region\InvalidRegionException;
use Customerio\Region\RegionInterface;
use Customerio\Region\RegionEu;
use Customerio\Region\RegionEuV2;
use Customerio\Region\RegionUs;
use Customerio\Region\RegionUsV2;

class Region
{
    public static function factory(string $region = 'us', int $version = 1): RegionInterface
    {
        switch ($region) {
            case 'us':
                return $version == 2 ? new RegionUsV2() : new RegionUs();
            case 'eu':
                return $version == 2 ? new RegionEuV2 : new RegionEu();
            default:
                throw new InvalidRegionException("Unknown region: {$region}");
        }
    }
}
