<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($staticPages as $page)
        <url>
            <loc>{{ $page['url'] }}</loc>
            <changefreq>{{ $page['changefreq'] }}</changefreq>
            <priority>{{ $page['priority'] }}</priority>
        </url>
    @endforeach
    @foreach ($articles as $article)
        <url>
            <loc>{{ $article['url'] }}</loc>
            @if ($article['lastmod'])
                <lastmod>{{ $article['lastmod'] }}</lastmod>
            @endif
            <changefreq>{{ $article['changefreq'] }}</changefreq>
            <priority>{{ $article['priority'] }}</priority>
        </url>
    @endforeach
</urlset>
