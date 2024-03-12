<?php

namespace InsightMedia\StatamicPdfThumbnailer;

use Spatie\PdfToImage\Pdf;
use Statamic\Assets\Asset;
use Statamic\Assets\AssetContainer;
use Statamic\Facades\AssetContainer as AssetContainerFacade;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PdfToImage
{

    public static function convert(Asset $asset, int $pageNumber = 1)
    {

        if ($asset->isPdf()) {

            $imageFileName = $asset->filename() . '.jpg';
            $imageFilePath = storage_path($imageFileName);

            $pdf = self::createImageFromPdf($asset->resolvedPath(), $imageFilePath, $pageNumber);

            $container = config('statamic-pdf-thumbnailer.container');
            if (!is_null($container)) {
                $container = AssetContainerFacade::findByHandle($container);
            } else {
                $container = $asset->container();
            }

            $thumbnailAsset = self::createAssetFromImage($container, $imageFilePath, $imageFileName);

            $asset->data([
                'thumbnail' => $thumbnailAsset->id(),
                'pdf_page_count' => $pdf->getNumberOfPages(),
                'pdf_converted_page' => $pageNumber
            ])->save();
        }

    }

    protected static function createImageFromPdf(string $pdfFilePath, string $imageFilePath, int $pageNumber): Pdf
    {

        $pdf = new Pdf($pdfFilePath);

        $pdf->setPage($pageNumber)->saveImage($imageFilePath);

        return $pdf;

    }

    protected static function createAssetFromImage(AssetContainer $container, string $imageFilePath, string $imageFileName): Asset
    {

        $file = new UploadedFile($imageFilePath, $imageFileName);

        return $container->makeAsset($imageFileName)->upload($file);

    }

}
