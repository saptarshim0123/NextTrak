<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Using a Set to prevent duplicates if any are accidentally added
        $companies = [
            // Major Tech (FAANG & similar)
            ['name' => 'Google', 'website' => 'google.com'],
            ['name' => 'Meta', 'website' => 'meta.com'],
            ['name' => 'Apple', 'website' => 'apple.com'],
            ['name' => 'Amazon', 'website' => 'amazon.com'],
            ['name' => 'Microsoft', 'website' => 'microsoft.com'],
            ['name' => 'Netflix', 'website' => 'netflix.com'],
            ['name' => 'Nvidia', 'website' => 'nvidia.com'],
            ['name' => 'Tesla', 'website' => 'tesla.com'],

            // Software & SaaS
            ['name' => 'Adobe', 'website' => 'adobe.com'],
            ['name' => 'Salesforce', 'website' => 'salesforce.com'],
            ['name' => 'Oracle', 'website' => 'oracle.com'],
            ['name' => 'IBM', 'website' => 'ibm.com'],
            ['name' => 'SAP', 'website' => 'sap.com'],
            ['name' => 'Intel', 'website' => 'intel.com'],
            ['name' => 'AMD', 'website' => 'amd.com'],
            ['name' => 'Atlassian', 'website' => 'atlassian.com'],
            ['name' => 'ServiceNow', 'website' => 'servicenow.com'],
            ['name' => 'Workday', 'website' => 'workday.com'],
            ['name' => 'Splunk', 'website' => 'splunk.com'],
            ['name' => 'HubSpot', 'website' => 'hubspot.com'],
            ['name' => 'Intuit', 'website' => 'intuit.com'],
            ['name' => 'VMware', 'website' => 'vmware.com'],
            ['name' => 'Cisco', 'website' => 'cisco.com'],

            // Cloud & Data
            ['name' => 'Snowflake', 'website' => 'snowflake.com'],
            ['name' => 'Datadog', 'website' => 'datadoghq.com'],
            ['name' => 'MongoDB', 'website' => 'mongodb.com'],
            ['name' => 'Palantir', 'website' => 'palantir.com'],
            ['name' => 'Cloudflare', 'website' => 'cloudflare.com'],
            ['name' => 'DigitalOcean', 'website' => 'digitalocean.com'],
            ['name' => 'Databricks', 'website' => 'databricks.com'],

            // Security
            ['name' => 'CrowdStrike', 'website' => 'crowdstrike.com'],
            ['name' => 'Okta', 'website' => 'okta.com'],
            ['name' => 'Palo Alto Networks', 'website' => 'paloaltonetworks.com'],
            ['name' => 'Zscaler', 'website' => 'zscaler.com'],

            // Consumer Tech & Social
            ['name' => 'Spotify', 'website' => 'spotify.com'],
            ['name' => 'Uber', 'website' => 'uber.com'],
            ['name' => 'Airbnb', 'website' => 'airbnb.com'],
            ['name' => 'LinkedIn', 'website' => 'linkedin.com'],
            ['name' => 'X (Twitter)', 'website' => 'x.com'],
            ['name' => 'GitHub', 'website' => 'github.com'],
            ['name' => 'Slack', 'website' => 'slack.com'],
            ['name' => 'Zoom', 'website' => 'zoom.us'],
            ['name' => 'Shopify', 'website' => 'shopify.com'],
            ['name' => 'Stripe', 'website' => 'stripe.com'],
            ['name' => 'Square', 'website' => 'squareup.com'],
            ['name' => 'DoorDash', 'website' => 'doordash.com'],
            ['name' => 'Lyft', 'website' => 'lyft.com'],
            ['name' => 'Pinterest', 'website' => 'pinterest.com'],
            ['name' => 'Snap Inc.', 'website' => 'snap.com'],
            ['name' => 'Dropbox', 'website' => 'dropbox.com'],
            ['name' => 'DocuSign', 'website' => 'docusign.com'],
            ['name' => 'Miro', 'website' => 'miro.com'],
            ['name' => 'Notion', 'website' => 'notion.so'],
            ['name' => 'Figma', 'website' => 'figma.com'],
            ['name' => 'Canva', 'website' => 'canva.com'],
            ['name' => 'Discord', 'website' => 'discord.com'],
            ['name' => 'Reddit', 'website' => 'reddit.com'],
            ['name' => 'TikTok', 'website' => 'tiktok.com'],
            ['name' => 'Twilio', 'website' => 'twilio.com'],

            // Consulting & Professional Services
            ['name' => 'Accenture', 'website' => 'accenture.com'],
            ['name' => 'Deloitte', 'website' => 'deloitte.com'],
            ['name' => 'PwC', 'website' => 'pwc.com'],
            ['name' => 'EY', 'website' => 'ey.com'],
            ['name' => 'KPMG', 'website' => 'kpmg.com'],
            ['name' => 'McKinsey & Company', 'website' => 'mckinsey.com'],
            ['name' => 'Boston Consulting Group', 'website' => 'bcg.com'],

            // Finance & Banking
            ['name' => 'Goldman Sachs', 'website' => 'goldmansachs.com'],
            ['name' => 'J.P. Morgan Chase', 'website' => 'jpmorganchase.com'],
            ['name' => 'Morgan Stanley', 'website' => 'morganstanley.com'],
            ['name' => 'American Express', 'website' => 'americanexpress.com'],
            ['name' => 'Visa', 'website' => 'visa.com'],
            ['name' => 'Mastercard', 'website' => 'mastercard.com'],
            ['name' => 'PayPal', 'website' => 'paypal.com'],
            ['name' => 'Bloomberg', 'website' => 'bloomberg.com'],

            // Major Indian Companies & Startups
            ['name' => 'Tata Consultancy Services', 'website' => 'tcs.com'],
            ['name' => 'Infosys', 'website' => 'infosys.com'],
            ['name' => 'Wipro', 'website' => 'wipro.com'],
            ['name' => 'HCL Technologies', 'website' => 'hcltech.com'],
            ['name' => 'Tech Mahindra', 'website' => 'techmahindra.com'],
            ['name' => 'Reliance Jio', 'website' => 'jio.com'],
            ['name' => 'Flipkart', 'website' => 'flipkart.com'],
            ['name' => 'Paytm', 'website' => 'paytm.com'],
            ['name' => 'Zomato', 'website' => 'zomato.com'],
            ['name' => 'Swiggy', 'website' => 'swiggy.com'],
            ['name' => 'Ola Cabs', 'website' => 'olacabs.com'],
            ['name' => 'Oyo Rooms', 'website' => 'oyorooms.com'],
            ['name' => 'Byju\'s', 'website' => 'byjus.com'],
            ['name' => 'PhonePe', 'website' => 'phonepe.com'],
            ['name' => 'Zerodha', 'website' => 'zerodha.com'],
            ['name' => 'Razorpay', 'website' => 'razorpay.com'],
            ['name' => 'CRED', 'website' => 'cred.club'],
            ['name' => 'Myntra', 'website' => 'myntra.com'],
            ['name' => 'Mindtree', 'website' => 'mindtree.com'],

            // Other Major International Companies
            ['name' => 'Walmart', 'website' => 'walmart.com'],
            ['name' => 'Samsung', 'website' => 'samsung.com'],
            ['name' => 'Sony', 'website' => 'sony.com'],
            ['name' => 'Disney', 'website' => 'disney.com'],
            ['name' => 'Ford', 'website' => 'ford.com'],
            ['name' => 'General Motors', 'website' => 'gm.com'],
            ['name' => 'AT&T', 'website' => 'att.com'],
            ['name' => 'Verizon', 'website' => 'verizon.com'],
        ];

        // Using firstOrCreate to avoid errors if you run the seeder multiple times.
        // It will only create a company if a company with that name doesn't already exist.
        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['name' => $company['name']],
                [
                    'website' => $company['website'],
                    'logo_url' => 'https://logo.clearbit.com/' . $company['website']
                ]
            );
        }
    }
}
