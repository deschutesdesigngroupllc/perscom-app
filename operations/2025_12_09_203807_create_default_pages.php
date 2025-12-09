<?php

declare(strict_types=1);

use App\Models\Page;
use App\Models\Tenant;
use TimoKoerber\LaravelOneTimeOperations\OneTimeOperation;

return new class extends OneTimeOperation
{
    protected bool $async = true;

    public function process(): void
    {
        tenancy()->runForMultiple(Tenant::all(), function ($tenant): void {
            $pages = [
                [
                    'name' => 'Awards',
                    'description' => 'An overview of your current awards. Manage this page in your System Settings or visit Widgets to use on your own website.',
                    'slug' => 'awards',
                    'hidden' => false,
                    'icon' => 'heroicon-o-trophy',
                    'order' => 1,
                    'content' => <<<'HTML'
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{{ ssoJwt() }}"
        data-widget="awards"
        src="{{ widgetUrl() }}"
        type="text/javascript"
    ></script>
</div>
HTML
                ],
                [
                    'name' => 'Positions',
                    'description' => 'An overview of your current positions. Manage this page in your System Settings or visit Widgets to use on your own website.',
                    'slug' => 'positions',
                    'hidden' => false,
                    'icon' => 'heroicon-o-identification',
                    'order' => 2,
                    'content' => <<<'HTML'
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{{ ssoJwt() }}"
        data-widget="positions"
        src="{{ widgetUrl() }}"
        type="text/javascript"
    ></script>
</div>
HTML
                ],
                [
                    'name' => 'Qualifications',
                    'description' => 'An overview of your current qualifications. Manage this page in your System Settings or visit Widgets to use on your own website.',
                    'slug' => 'qualifications',
                    'hidden' => false,
                    'icon' => 'heroicon-o-star',
                    'order' => 3,
                    'content' => <<<'HTML'
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{{ ssoJwt() }}"
        data-widget="qualifications"
        src="{{ widgetUrl() }}"
        type="text/javascript"
    ></script>
</div>
HTML
                ],
                [
                    'name' => 'Ranks',
                    'description' => 'An overview of your current ranks. Manage this page in your System Settings or visit Widgets to use on your own website.',
                    'slug' => 'ranks',
                    'hidden' => false,
                    'icon' => 'heroicon-o-chevron-double-up',
                    'order' => 4,
                    'content' => <<<'HTML'
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{{ ssoJwt() }}"
        data-widget="ranks"
        src="{{ widgetUrl() }}"
        type="text/javascript"
    ></script>
</div>
HTML
                ],
                [
                    'name' => 'Specialties',
                    'description' => 'An overview of your current specialties. Manage this page in your System Settings or visit Widgets to use on your own website.',
                    'slug' => 'specialties',
                    'hidden' => false,
                    'icon' => 'heroicon-o-briefcase',
                    'order' => 5,
                    'content' => <<<'HTML'
<div id="perscom_widget_wrapper">
    <script
        id="perscom_widget"
        data-apikey="{{ ssoJwt() }}"
        data-widget="specialties"
        src="{{ widgetUrl() }}"
        type="text/javascript"
    ></script>
</div>
HTML
                ],
            ];

            foreach ($pages as $page) {
                Page::create($page);
            }
        });
    }
};
