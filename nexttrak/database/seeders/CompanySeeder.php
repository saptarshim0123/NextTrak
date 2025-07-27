<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            ['name' => 'Google', 'website' => 'google.com'],
            ['name' => 'Meta', 'website' => 'meta.com'],
            ['name' => 'Apple', 'website' => 'apple.com'],
            ['name' => 'Amazon', 'website' => 'amazon.com'],
            ['name' => 'Microsoft', 'website' => 'microsoft.com'],
            ['name' => 'Netflix', 'website' => 'netflix.com'],
            ['name' => 'Nvidia', 'website' => 'nvidia.com'],
            ['name' => 'Tesla', 'website' => 'tesla.com'],
            ['name' => 'Adobe', 'website' => 'adobe.com'],
            ['name' => 'Salesforce', 'website' => 'salesforce.com'],
            ['name' => 'Oracle', 'website' => 'oracle.com'],
            ['name' => 'IBM', 'website' => 'ibm.com'],
            ['name' => 'Intel', 'website' => 'intel.com'],
            ['name' => 'AMD', 'website' => 'amd.com'],
            ['name' => 'Spotify', 'website' => 'spotify.com'],
            ['name' => 'Uber', 'website' => 'uber.com'],
            ['name' => 'Airbnb', 'website' => 'airbnb.com'],
            ['name' => 'LinkedIn', 'website' => 'linkedin.com'],
            ['name' => 'Twitter', 'website' => 'twitter.com'],
            ['name' => 'GitHub', 'website' => 'github.com'],
            ['name' => 'Slack', 'website' => 'slack.com'],
            ['name' => 'Zoom', 'website' => 'zoom.us'],
            ['name' => 'Shopify', 'website' => 'shopify.com'],
            ['name' => 'Stripe', 'website' => 'stripe.com'],
            ['name' => 'Palantir', 'website' => 'palantir.com'],
            ['name' => 'Snowflake', 'website' => 'snowflake.com'],
            ['name' => 'Datadog', 'website' => 'datadoghq.com'],
            ['name' => 'MongoDB', 'website' => 'mongodb.com'],
            ['name' => 'Atlassian', 'website' => 'atlassian.com'],
            ['name' => 'ServiceNow', 'website' => 'servicenow.com'],
            ['name' => 'Workday', 'website' => 'workday.com'],
            ['name' => 'Splunk', 'website' => 'splunk.com'],
            ['name' => 'CrowdStrike', 'website' => 'crowdstrike.com'],
            ['name' => 'Okta', 'website' => 'okta.com'],
            ['name' => 'Twilio', 'website' => 'twilio.com'],
            ['name' => 'Square', 'website' => 'squareup.com'],
            ['name' => 'DoorDash', 'website' => 'doordash.com'],
            ['name' => 'Lyft', 'website' => 'lyft.com'],
            ['name' => 'Pinterest', 'website' => 'pinterest.com'],
            ['name' => 'Snap Inc.', 'website' => 'snap.com'],
            ['name' => 'Dropbox', 'website' => 'dropbox.com'],
            ['name' => 'Box', 'website' => 'box.com'],
            ['name' => 'DocuSign', 'website' => 'docusign.com'],
            ['name' => 'ZoomInfo', 'website' => 'zoominfo.com'],
            ['name' => 'HubSpot', 'website' => 'hubspot.com'],
            ['name' => 'Miro', 'website' => 'miro.com'],
            ['name' => 'Notion', 'website' => 'notion.so'],
            ['name' => 'Figma', 'website' => 'figma.com'],
            ['name' => 'Canva', 'website' => 'canva.com'],
            ['name' => 'Discord', 'website' => 'discord.com'],
            ['name' => 'Reddit', 'website' => 'reddit.com'],
            ['name' => 'TikTok', 'website' => 'tiktok.com'],
            ['name' => 'ByteDance', 'website' => 'bytedance.com'],
            ['name' => 'Alibaba', 'website' => 'alibaba.com'],
            ['name' => 'Tencent', 'website' => 'tencent.com'],
            ['name' => 'Baidu', 'website' => 'baidu.com'],
        ];

        foreach ($companies as $company) {
            Company::create([
                'name' => $company['name'],
                'website' => $company['website'],
                'logo_url' => 'https://logo.clearbit.com/' . $company['website'] // Automatically create the logo URL!
            ]);
        }
    }
}
