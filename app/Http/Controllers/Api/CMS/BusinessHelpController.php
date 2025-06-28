<?php

namespace App\Http\Controllers\Api\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use App\Models\CMS;
use App\Enum\Page;
use App\Enum\Section;

class BusinessHelpController extends Controller
{
    use ApiResponse;
    public function businessHelp(Request $request)
    {
        $perPage = 9;
        $page = Page::BUSINESS_HELP;

        $banner = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP_BANNER)->first();
        $popularArticleBanner = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLE_BANNER)->first();
        $popularArticles = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)->get();
        $knowledgeBaseBanner = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE_BANNER)->first();
        $knowledgeBaseItems = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)->paginate($perPage);
        $helpSection = CMS::where('page', $page)->where('section', Section::BUSINESS_HELP)->first();

        $data = [
            'banner' => $banner ? [
                'title' => $banner->title,
            ] : null,

            'popular_article_banner' => $popularArticleBanner ? [
                'title' => $popularArticleBanner->title,
                'sub_title' => $popularArticleBanner->sub_title,
            ] : null,

            'popular_articles' => $popularArticles->isNotEmpty() ? $popularArticles->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->background_image ? $item->background_image : null,
                ];
            }) : null,

            'knowledge_base_banner' => $knowledgeBaseBanner ? [
                'title' => $knowledgeBaseBanner->title,
                'sub_title' => $knowledgeBaseBanner->sub_title,
            ] : null,

            'knowledge_base_items' => $knowledgeBaseItems->isNotEmpty() ? [
                'data' => $knowledgeBaseItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'sub_title' => $item->sub_title,
                        'description' => $item->description,
                        'icon' => $item->icon ? $item->icon : null,
                    ];
                }),
                'current_page' => $knowledgeBaseItems->currentPage(),
                'last_page' => $knowledgeBaseItems->lastPage(),
                'current_page_url' => $knowledgeBaseItems->url($knowledgeBaseItems->currentPage()),
                'previous_page_url' => $knowledgeBaseItems->previousPageUrl(),
                'next_page_url' => $knowledgeBaseItems->nextPageUrl(),
                'per_page' => $knowledgeBaseItems->perPage(),
                'total' => $knowledgeBaseItems->total(),
            ] : null,


            'help_section' => $helpSection ? [
                'title' => $helpSection->title,
                'sub_title' => $helpSection->sub_title,
                'button_text' => $helpSection->button_text,
                'button_link' => $helpSection->button_link
            ] : null,
        ];

        if (empty(array_filter($data, fn($value) => !is_null($value) && (!is_countable($value) || count($value) > 0)))) {
            return $this->error([],'No data found', 404);
        }

        return $this->success((object) $data, 'Business help page data retrieved successfully', 200);
    }

    public function knowledgeBaseDetails($id)
    {
        $knowledgeBase = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
            ->where('id', $id)
            ->first();


        if (!$knowledgeBase) {
            return $this->error([],'Knowledge base item not found', 404);
        }

        $relatedKnowledgeBaseItems = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
            ->where('id', '!=', $knowledgeBase->id)
            ->latest()
            ->take(5)
            ->get();

        $data = (object) [
            'knowledge_base_item' => [
                'title' => $knowledgeBase->title,
                'sub_title' => $knowledgeBase->sub_title,
                'description' => $knowledgeBase->description,
                'icon' => $knowledgeBase->icon ? $knowledgeBase->icon : null,
            ],
            'related_items' => $relatedKnowledgeBaseItems->map(function ($item) {
                return [
                    'title' => $item->title,
                    'sub_title' => $item->sub_title,
                ];
            }),
        ];

        return $this->success($data, 'Knowledge base details retrieved successfully', 200);
    }

    public function searchKnowledgeBase(Request $request)
    {
        $searchTerm = trim($request->query('search'));

        if (!$searchTerm) {
            return $this->error([],'Search term is required', 422);
        }

        $results = CMS::query()
            ->where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_KNOWLEDGE_BASE)
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('sub_title', 'like', '%' . $searchTerm . '%');
            })
            ->latest()
            ->get();

        $data = $results->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'sub_title' => $item->sub_title,
                'icon' => $item->icon ? $item->icon : null,
            ];
        });

        return $this->success($data, 'Knowledge base search results retrieved successfully', 200);
    }


    public function popularArticleDetails($id)
    {
        $article = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)
            ->where('id', $id)
            ->first();

        if (!$article) {
            return $this->error([],'Popular article not found', 404);
        }

        $relatedArticles = CMS::where('page', Page::BUSINESS_HELP)
            ->where('section', Section::BUSINESS_HELP_POPULAR_ARTICLES)
            ->where('id', '!=', $article->id)
            ->latest()
            ->take(5)
            ->get();

        $data = (object) [
            'article' => [
                'title' => $article->title,
                'description' => $article->description,
                'image' => $article->background_image ?? null,
            ],
            'related_articles' => $relatedArticles->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->background_image ?? null,
                ];
            }),
        ];

        return $this->success($data, 'Popular article details retrieved successfully', 200);
    }


}
