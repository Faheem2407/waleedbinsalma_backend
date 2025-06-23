<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'blog_category_id',
        'blog_creator',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            $blog->slug = static::generateUniqueSlug($blog->title);
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title')) {
                $blog->slug = static::generateUniqueSlug($blog->title, $blog->id);
            }
        });
    }

    protected static function generateUniqueSlug($title, $ignoreId = null)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
