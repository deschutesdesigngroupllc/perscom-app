<?php

namespace App\Models\Enums;

enum FeatureIdentifier: string
{
    case FEATURE_SOCIAL_LOGIN = 'social-login';
    case FEATURE_API_ACCESS = 'api-access';
    case FEATURE_CUSTOM_SUBDOMAIN = 'custom-subdomain';
    case FEATURE_CUSTOM_DOMAIN = 'custom-domain';
    case FEATURE_SUPPORT_TICKET = 'support-ticket';
    case FEATURE_EXPORT_DATA = 'export-data';
    case FEATURE_SINGLE_SIGN_ON = 'single-sign-on';
}
