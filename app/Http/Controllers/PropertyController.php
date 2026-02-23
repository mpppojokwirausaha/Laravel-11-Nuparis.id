<?php

namespace App\Http\Controllers;

use App\Models\Info;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function property()
    {
        return view('front-end.property-more', [
            'title' => 'Property | ' . config('app.name'),
            'infos' => (new Info())->getInfo(),
        ]);
    }

    public function propertyData()
    {
        try {
            $properties = Property::latest()->get()->map(function ($property) {
                // Pastikan property_image adalah array
                $propertyArray = $property->toArray();
                
                // Proses property_image untuk mengambil gambar pertama
                if (isset($propertyArray['property_image'])) {
                    $mediaData = $propertyArray['property_image'];
                    
                    // Cek jika property_image adalah string JSON
                    if (is_string($mediaData)) {
                        try {
                            $decoded = json_decode($mediaData, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $mediaData = $decoded;
                            }
                        } catch (\Exception $e) {
                            // Tetap sebagai string
                        }
                    }
                    
                    // Jika array, ambil gambar pertama yang bukan video
                    if (is_array($mediaData)) {
                        $firstImage = '';
                        
                        foreach ($mediaData as $media) {
                            if (is_string($media)) {
                                $lowerMedia = strtolower($media);
                                
                                // Cek ekstensi gambar
                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|bmp)(\?.*)?$/i', $lowerMedia);
                                
                                if ($isImage) {
                                    $firstImage = $media;
                                    break;
                                }
                            }
                        }
                        
                        // Update property_image dengan gambar pertama
                        $propertyArray['property_image'] = $firstImage ? [$firstImage] : [];
                    }
                }
                
                return $propertyArray;
            });
            
            return response()->json([
                'success' => true,
                'propertyData' => $properties->toArray(),
                'total' => $properties->count()
            ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
            
        } catch (\Exception $e) {
            \Log::error('properties API error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage(),
                'propertyData' => []
            ], 500);
        }
    }

        public function propertyDetail($property_slug)
    {
        $property = (new Property())->getPropertyDetail($property_slug);

        // Decode gambar
        $propertyImages = $this->processImages($property->property_image);
        
        // Proses fasilitas
        $fasilities = $this->processStringArray($property->property_fasilities, true);
        
        // Proses sertifikat
        $certificate = $this->processStringArray($property->property_certificate);

        // Ambil properti terkait
        $relatedProperties = Property::where('property_slug', '!=', $property_slug)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('front-end.property-detail', [
            'title' => $property->property_name . ' | ' . config('app.name'),
            'infos' => (new Info)->getInfo(),
            'property' => $property,
            'fasilities' => $fasilities,
            'certificate' => $certificate,
            'propertyImages' => $propertyImages,
            'relatedProperties' => $relatedProperties,
        ]);
    }

    /**
     * Helper untuk memproses string array dengan koma
     */
    private function processStringArray($data, $skipFasilityKeyword = false)
    {
        $result = [];
        
        if (empty($data)) {
            return $result;
        }
        
        // Konversi ke array jika perlu
        $items = [];
        
        if (is_string($data)) {
            // Coba decode JSON
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = [$data];
            }
        } elseif (is_array($data)) {
            $items = $data;
        }
        
        // Proses setiap item
        foreach ($items as $item) {
            if (is_string($item)) {
                // Handle special cases with commas in specifications
                $cleanedItem = $this->cleanItem($item, $skipFasilityKeyword);
                
                // Split hanya jika item tidak mengandung pola khusus dengan koma
                if ($this->shouldSplitItem($cleanedItem)) {
                    $splitItems = explode(',', $cleanedItem);
                    foreach ($splitItems as $splitItem) {
                        $trimmedItem = trim($splitItem);
                        if (!empty($trimmedItem)) {
                            $result[] = $trimmedItem;
                        }
                    }
                } else {
                    // Item mengandung koma sebagai bagian dari spesifikasi, tidak di-split
                    $trimmedItem = trim($cleanedItem);
                    if (!empty($trimmedItem)) {
                        $result[] = $trimmedItem;
                    }
                }
            }
        }
        
        return array_values(array_unique($result));
    }

    private function cleanItem($item, $skipFasilityKeyword)
    {
        $item = trim($item);
        
        if ($skipFasilityKeyword) {
            $item = preg_replace('/^fasilitas\s*:/i', '', $item);
        }
        
        return $item;
    }

    private function shouldSplitItem($item)
    {
        // Items yang mengandung pola khusus dengan koma tidak boleh di-split
        $patterns = [
            '/\d,\d\s+Ton/',  // Pattern for "1,5 Ton" or "2,5 Ton"
            '/Ton,\s*\d+/',   // Pattern for "Ton, 14" or "Ton, 4"
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $item)) {
                return false; // Don't split this item
            }
        }
        
        return true; // Safe to split
    }

    /**
     * Helper untuk menambahkan item bersih ke array
     */
    private function addCleanItem(&$array, $item, $skipFasilityKeyword = false)
    {
        $cleanItem = trim($item);
        
        // Skip jika kosong
        if (empty($cleanItem)) {
            return;
        }
        
        // Skip jika "-"
        if ($cleanItem === '-') {
            return;
        }
        
        // Skip jika mengandung "fasilitas" (case insensitive)
        if ($skipFasilityKeyword && stripos($cleanItem, 'fasilitas') !== false) {
            return;
        }
        
        $array[] = $cleanItem;
    }

    /**
     * Helper untuk memproses gambar
     */
    private function processImages($imageData)
    {
        if (empty($imageData)) {
            return [];
        }
        
        if (is_string($imageData)) {
            $decoded = json_decode($imageData, true);
            return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) 
                ? $decoded 
                : [$imageData];
        }
        
        return is_array($imageData) ? $imageData : [];
    }
}
