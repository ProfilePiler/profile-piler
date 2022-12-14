<?php

namespace App\Core;

use App\Models\CuratedList;
use App\Models\Keyword;

class SiteMapGenerator
{
    private $urlset;
    private $frontendAppUrl;

    const TopProfileByPlatformSlug = "top-{platform-name}-{profile-type}-for-";
    const TopProfileSlug = "top-social-media-profiles-for-";

    public function __construct()
    {
        $this->urlset = new \SimpleXMLElement('<urlset/>');
        $this->urlset->addAttribute('xml', "http://www.sitemaps.org/schemas/sitemap/0.9");
        $this->frontendAppUrl = env('FE_URL');
    }

    public function build($platform = null)
    {
        $keywords = Keyword::select('keyword')->get()->pluck('keyword');
        $this->addKeywords($keywords, $platform);
        return $this->urlset->asXML();
    }

    public function buildCuratedList(): string
    {
        $curaltedLigsUrls = CuratedList::where('is_active', 1)->get()->pluck('seo_url');
        $this->addUrl("{$this->frontendAppUrl}/lists");
        foreach ($curaltedLigsUrls as $url) {
            $absUrl = "{$this->frontendAppUrl}/lists/{$url}";
            $this->addUrl($absUrl);
        }
        return $this->urlset->asXML();
    }

    private function addUrl($absUrl)
    {
        $url = $this->urlset->addChild('url');
        $url->addChild('loc', htmlspecialchars($absUrl));
    }

    private function addKeywords($keywords, $platform = null)
    {
        foreach ($keywords as $keyword) {
            $absUrl = $this->buildUrl($keyword, $platform);
            $this->addUrl($absUrl);
        }
    }

    private function buildUrl($keyword, $platform = null): string
    {
        $topProfileByPlatformSlug = "top-{platform-name}-{profile-type}-for-";
        $topProfileSlug = "top-social-media-profiles-for-";

        $urlFriendlyKeyword = urlencode(str_replace(' ', '-', strtolower($keyword)));
        if ($platform === null) {
            return "{$this->frontendAppUrl}{$topProfileSlug}{$urlFriendlyKeyword}";
        }
        $slug = str_replace("{platform-name}", $platform, $topProfileByPlatformSlug);
        $slug = str_replace("{profile-type}", $this->getProfileTypeByPlatform($platform), $slug);
        return "{$this->frontendAppUrl}{$slug}{$urlFriendlyKeyword}";
    }

    private function getProfileTypeByPlatform($platformName): string
    {
        switch ($platformName) {
            case "facebook":
                return "pages";
            case "youtube":
                return "channels";
            default:
                return "profiles";
        }
    }
}
