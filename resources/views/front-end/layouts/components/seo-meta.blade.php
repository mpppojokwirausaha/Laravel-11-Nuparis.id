{{--
    Partial SEO meta tag, reusable untuk article-detail, property-detail, event-detail, activity-detail.

    Variabel yang bisa dikirim dari controller (semua opsional):
    - seo_title        (string)  fallback ke $title lama, lalu config('app.name')
    - seo_description  (string)  fallback ke config('app.description')
    - seo_image        (string)  URL absolut; fallback ke $infos->meta_image kalau ada
    - seo_image_alt    (string)  teks alternatif gambar OG
    - seo_type         (string)  default 'website', isi 'article' untuk Article
    - canonical_url    (string)  default url()->current()
    - published_time   (Carbon)  hanya untuk article
    - modified_time    (Carbon)  hanya untuk article
    - seo_author       (string)
    - seo_section      (string)
    - seo_robots       (string)  default 'index, follow'
    - jsonld           (array|array[])  satu schema atau array berisi beberapa schema (mis. Article + BreadcrumbList)
    - feed_url         (string)  satu URL feed, mis. url('/article/feed')
    - feed_title       (string)  judul feed untuk feed_url tunggal, default $seoTitle
    - feed_urls        (array)   beberapa feed sekaligus, format: [['url' => ..., 'title' => ...], ...]
--}}
@php
    $seoTitle = $seo_title ?? ($title ?? config('app.name'));
    $seoDescription = $seo_description ?? config('app.description', config('app.name'));
    $fallbackImage = isset($infos) && !empty($infos->meta_image) ? asset('storage/' . $infos->meta_image) : null;
    $seoImage = $seo_image ?? $fallbackImage;
    $canonicalUrl = $canonical_url ?? url()->current();
    $seoType = $seo_type ?? 'website';
@endphp

<title>{{ $seoTitle }}</title>
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seo_robots ?? 'index, follow' }}">
<meta name="author" content="{{ $seo_author ?? config('app.name') }}">

{{-- Autodiscovery RSS/Atom feed --}}
@if (!empty($feed_url))
    <link rel="alternate" type="application/atom+xml" title="{{ $feed_title ?? $seoTitle }}" href="{{ $feed_url }}">
@endif
@if (!empty($feed_urls) && is_array($feed_urls))
    @foreach ($feed_urls as $feed)
        <link rel="alternate" type="application/atom+xml" title="{{ $feed['title'] ?? config('app.name') }}"
            href="{{ $feed['url'] }}">
    @endforeach
@endif

<!-- Open Graph -->
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="id_ID">
@if ($seoImage)
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:secure_url" content="{{ $seoImage }}">
    <meta property="og:image:width" content="{{ $seo_image_width ?? 1200 }}">
    <meta property="og:image:height" content="{{ $seo_image_height ?? 630 }}">
    <meta property="og:image:alt" content="{{ $seo_image_alt ?? $seoTitle }}">
@endif

@if ($seoType === 'article')
    @if (!empty($published_time))
        <meta property="article:published_time" content="{{ $published_time->toIso8601String() }}">
    @endif
    @if (!empty($modified_time))
        <meta property="article:modified_time" content="{{ $modified_time->toIso8601String() }}">
        <meta property="og:updated_time" content="{{ $modified_time->toIso8601String() }}">
    @endif
    <meta property="article:author" content="{{ $seo_author ?? 'Tim Redaksi ' . config('app.name') }}">
    <meta property="article:section" content="{{ $seo_section ?? 'Berita' }}">
@endif

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
@if ($seoImage)
    <meta name="twitter:image" content="{{ $seoImage }}">
    <meta name="twitter:image:alt" content="{{ $seo_image_alt ?? $seoTitle }}">
@endif
<meta name="twitter:site" content="{{ config('app.twitter_handle', '@nuparis_id') }}">
<meta name="twitter:creator" content="{{ config('app.twitter_handle', '@nuparis_id') }}">

@if (!empty($jsonld))
    @php
        // Terima satu schema (array asosiatif dengan '@type') atau beberapa schema (array of arrays)
        $schemas = array_is_list($jsonld) ? $jsonld : [$jsonld];
    @endphp
    @foreach ($schemas as $schema)
        <script type="application/ld+json">
            {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endforeach
@endif
