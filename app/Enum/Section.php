<?php

namespace App\Enum;

enum Section : string
{
    case BUSINESS_HOME_BANNER = 'business_home_banner';
    case BUSINESS_HOME_STATS = 'business_home_stats';

    case BUSINESS_HOME_GROW_YOUR_BUSINESS_SECTION_TITLE = 'business_home_grow_your_business_section_title';
    case BUSINESS_HOME_GROW_YOUR_BUSINESS = 'business_home_grow_your_business';
    case BUSINESS_HOME_STAY_CONNECTED = 'business_home_stay_connected';

    case BUSINESS_HOME_GET_STARTED = 'business_home_get_started';
    case BUSINESS_HOME_INTERESTED = 'business_home_interested';

    case BUSINESS_HOME_WHAT_OUR_CLIENTS_SAY = 'business_home_what_our_clients_say';

    case BUSINESS_PRICING_BANNER = 'business_pricing_banner';
    case BUSINESS_PRICING_SECTION_TITLE = 'business_pricing_section_title';
    case BUSINESS_PRICING_SECTION_DESCRIPTION = 'business_pricing_section_description';

    case BUSINESS_PRICING_DESCRIPITON = 'business_pricing_description';

    case BUSINESS_PRICING_FAQ = 'business_pricing_faq';


    case BUSINESS_HELP_BANNER = 'business_help_banner';
    case BUSINESS_HELP_POPULAR_ARTICLE_BANNER = 'business_help_popular_article_banner';
    case BUSINESS_HELP_POPULAR_ARTICLES = 'business_help_popular_articles';

    case BUSINESS_HELP_KNOWLEDGE_BASE_BANNER = 'business_help_knowledge_base_banner';
    case BUSINESS_HELP_KNOWLEDGE_BASE = 'business_help_knowledge_base';
    case BUSINESS_HELP = 'business_help';

    case BLOG_BANNER = 'blog_banner';
    case BLOG_FOOTER = 'blog_footer';   
}
