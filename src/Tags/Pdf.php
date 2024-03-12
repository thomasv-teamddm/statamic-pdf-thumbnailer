<?php

namespace InsightMedia\StatamicPdfThumbnailer\Tags;

use Statamic\Facades\Asset;
use Statamic\Tags\Tags;

class Pdf extends Tags
{
    protected static $aliases = ['pdf'];

    public function index()
    {
        $asset = Asset::find($this->params->get('to'));
        return $asset?->url();
    }
}
