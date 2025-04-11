<?php

namespace SubscriptionBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SubscribeStatus: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ACTIVE = 'active';
    case EXPIRED = 'expiry';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => '活跃',
            self::EXPIRED => '过期',
        };
    }
}
