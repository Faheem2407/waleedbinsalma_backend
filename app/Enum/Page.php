<?php

namespace App\Enum;

enum Page : string
{
    case BUSINESS_HOME = 'business_home';
    case BUSINESS_PRICING = 'business_pricing';
    case BUSINESS_HELP = 'business_help';

    case BLOG = 'blog';
}
