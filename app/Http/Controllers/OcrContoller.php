<?php

namespace App\Http\Controllers;

use App\Services\OcrService;
use App\Services\PropertyService;
use App\Services\SourceService;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OcrContoller extends Controller
{
    public function __construct(
        private OcrService $ocrService,
        private VenueService $venueService,
        private SourceService $sourceService,
        private PropertyService $propertyService
    ) {
        $this->ocrService = $ocrService;
        $this->venueService = $venueService;
        $this->sourceService = $sourceService;
        $this->propertyService = $propertyService;
    }

    public function scan()
    {
        return Inertia::render('Ocr/Scan');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'uploaded_image' => 'required|mimes:png,jpg'
        ]);

        try {
            $result = $this->ocrService->scan($request->toArray());
            $result_string = $this->ocrService->formatScanned($result);
        } catch (Exception $e) {
            return redirect()->to('scan');
        }

        // dd($result);
        // dd($result_string);

        return Inertia::render('Ocr/OcrLeadForm', [
            'lead' => $result_string,
            'is_disabled' => false,
            'form_type' => 'OCR',
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
            'properties' => $this->propertyService->indexProperty()
        ]);
    }

    public function lead(Request $request)
    {
        dd($request);
        return Inertia::render('Ocr/OcrLeadForm');
    }
}
