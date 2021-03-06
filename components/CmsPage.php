<?php namespace JumpLink\SeoExtension\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Request;
use JumpLink\SeoExtension\models\Settings;
use JumpLink\SeoExtension\classes\Helper;
use URL;

class CmsPage extends ComponentBase
{
    public $page;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $canonical_url;
    public $redirect_url;
    public $robot_index;
    public $robot_follow;
    public $hasBlog;

    public $ogTitle;
    public $ogUrl;
    public $ogDescription;
    public $ogSiteName;
    public $ogFbAppId;
    public $ogLocale;
    public $ogImage;

    public $is_https;


    public function componentDetails()
    {
        return [
            'name'        => 'jumplink.seoextension::lang.component.cms.name',
            'description' => 'jumplink.seoextension::lang.component.cms.description'
        ];
    }

    public function defineProperties()
    {
        return [];
    }

    public function onRun()
    {
        $theme = Theme::getActiveTheme();
        $page = Page::load($theme,$this->page->baseFileName);
        $this->page["hasBlog"] = false;

        $this->is_https = $this->page['is_https'] = Helper::isSSL();

        if(!$page->hasComponent("blogPost"))
        {
            $this->seo_title = $this->page["seo_title"] = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
            $this->seo_description = $this->page["seo_description"] = $this->page->meta_description;
            $this->seo_keywords = $this->page["seo_keywords"] = $this->page->seo_keywords;
            $this->canonical_url = $this->page["canonical_url"] = $this->page->canonical_url;
            $this->redirect_url = $this->page["redirect_url"] = $this->page->redirect_url;
            $this->robot_follow = $this->page["robot_follow"] = $this->page->robot_follow;
            $this->robot_index = $this->page["robot_index"] = $this->page->robot_index;

            $settings = Settings::instance();

            if($settings->enable_og_tags)
            {
                $this->ogTitle = empty($this->page->meta_title) ? $this->page->title : $this->page->meta_title;
                $this->ogDescription = $this->page->meta_description;
                $this->ogUrl = empty($this->page->canonical_url) ? Request::url() : $this->page->canonical_url ;
                $this->ogSiteName = $settings->og_sitename;
                $this->ogFbAppId = $settings->og_fb_appid;
                $this->ogImage = $this->page['og_image'] = $this->page->og_image;
                // if no image is specified for the page, use the global default
                if (empty($this->ogImage))
                    $this->ogImage = $this->page['og_image'] = $settings->og_image;
            }

        }
        else{
            $this->hasBlog = $this->page["hasBlog"] = true;
        }
    }

}
