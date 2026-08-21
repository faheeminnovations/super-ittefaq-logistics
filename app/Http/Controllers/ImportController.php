<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Billing;

class ImportController extends Controller
{
    public function process(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,txt|max:10240', // Max 10MB
            'billing_month' => 'required|date_format:Y-m',
        ]);

        try {
            // Get the uploaded file
            $file = $request->file('import_file');
            $filePath = $file->getPathname();
            
            \Log::info('CSV file uploaded: ' . $file->getClientOriginalName());
            
            // Parse CSV file
            $extractedData = $this->parseCSV($filePath);
            
            \Log::info('Extracted data: ' . json_encode($extractedData));
            
            // Check if parsing failed
            if (empty($extractedData['records'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid records found in CSV file. Please check the format.',
                    'details' => $extractedData
                ], 400);
            }
            
            // Process the extracted data and save to database
            $importResults = $this->processExtractedData($extractedData, $request->billing_month);
            
            return response()->json([
                'success' => true,
                'message' => "Imported {$importResults['records_imported']} records successfully",
                'details' => $importResults
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing import: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Parse CSV file
     */
    private function parseCSV($filePath)
    {
        $records = [];
        $header = [];
        $rowNumber = 0;
        
        if (($handle = fopen($filePath, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
                $rowNumber++;
                
                // Skip header row
                if ($rowNumber === 1) {
                    $header = $data;
                    \Log::info('CSV Header: ' . json_encode($header));
                    continue;
                }
                
                // Skip empty rows or TOTAL row
                if (empty($data) || (isset($data[0]) && strtoupper(trim($data[0])) === 'TOTAL')) {
                    continue;
                }
                
                \Log::info("Processing row {$rowNumber}: " . json_encode($data));
                
                // Map CSV columns to our format (more flexible)
                $record = [
                    'sr' => $data[0] ?? '',
                    'date' => $this->formatDate($data[1] ?? ''),
                    'vehicle_no' => trim($data[2] ?? ''),
                    'customer_name' => trim($data[3] ?? ''),
                    'contact_number' => trim($data[4] ?? ''),
                    'bags' => $this->cleanNumber($data[5] ?? 0),
                    'delivery_point' => trim($data[6] ?? ''),
                    'km_covered' => $this->cleanNumber($data[7] ?? 0),
                    'rent' => $this->cleanNumber($data[8] ?? 0),
                    'advance' => $this->cleanNumber($data[9] ?? 0),
                    'advance_date' => $this->formatDate($data[10] ?? ''),
                    'guarantor' => trim($data[11] ?? ''),
                    'dues' => $this->cleanNumber($data[12] ?? 0),
                    'status' => $this->cleanStatus($data[13] ?? 'Pending')
                ];
                
                \Log::info("Parsed record: " . json_encode($record));
                
                // Only add if has essential data (more lenient check - only customer_name required)
                if (!empty($record['customer_name'])) {
                    $records[] = $record;
                    \Log::info("Record added: {$record['vehicle_no']} - {$record['customer_name']}");
                } else {
                    \Log::warning("Record skipped due to missing customer name: " . json_encode($record));
                }
            }
            fclose($handle);
        }
        
        \Log::info("Total records parsed: " . count($records));
        
        return [
            'company_name' => 'CSV Import',
            'billing_month' => 'From CSV',
            'records' => $records
        ];
    }
    
    /**
     * Format date from DD-Mon-YY to YYYY-MM-DD
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return now()->format('Y-m-d');
        }
        
        try {
            // Try parsing DD-Mon-YY format (e.g., 05-Jan-26)
            $dateObj = \DateTime::createFromFormat('d-M-y', $date);
            if ($dateObj) {
                return $dateObj->format('Y-m-d');
            }
            
            // Try other formats
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
    
    /**
     * Clean number by removing commas and other non-numeric characters
     */
    private function cleanNumber($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Remove commas, spaces, and other non-numeric characters except decimal point
        $cleaned = preg_replace('/[^0-9.]/', '', $value);
        return floatval($cleaned);
    }
    
    /**
     * Clean status value
     */
    private function cleanStatus($status)
    {
        if (empty($status)) {
            return 'Pending';
        }
        
        $status = strtoupper(trim($status));
        
        // Check for various status variations
        if (strpos($status, 'OK') !== false || strpos($status, 'PAID') !== false) {
            return 'Paid';
        }
        
        if (strpos($status, 'PEND') !== false) {
            return 'Pending';
        }
        
        return 'Pending';
    }
    

    
    public function export(Request $request)
    {
        try {
            $billingMonth = $request->get('month', date('Y-m'));
            
            // Get data for the specified month from billings table
            $billings = Billing::where('billing_month', $billingMonth)
                ->orderBy('sr')
                ->get();
            
            // Create CSV content
            $csvContent = "Sr,Date,Vhl No,Name,Number,Bag,Drop/Delivery Point,Km Cover,Rent,Advance,Advance Date,Guarantor,Dues,Status\n";
            
            foreach ($billings as $billing) {
                $sr = $billing->sr ?? '';
                $date = $billing->date ? $billing->date->format('d-M-y') : '';
                $vehicleNo = $billing->vehicle_no ?? '';
                $customerName = $billing->customer_name ?? '';
                $contactNumber = $billing->contact_number ?? '';
                $bags = $billing->bags ?? '';
                $deliveryPoint = $billing->delivery_point ?? '';
                $kmCovered = $billing->km_covered ?? '';
                $rent = $billing->rent ?? '';
                $advance = $billing->advance ?? '';
                $advanceDate = $billing->advance_date ? $billing->advance_date->format('d-M-y') : '';
                $guarantor = $billing->guarantor ?? '';
                $dues = $billing->dues ?? '';
                $status = $billing->status ?? '';
                
                $csvContent .= "{$sr},{$date},{$vehicleNo},{$customerName},{$contactNumber},{$bags},{$deliveryPoint},{$kmCovered},{$rent},{$advance},{$advanceDate},{$guarantor},{$dues},{$status}\n";
            }
            
            // Return CSV file download
            $fileName = "billing_export_{$billingMonth}.csv";
            
            return response($csvContent)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
                
        } catch (\Exception $e) {
            \Log::error('Export error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Extract data from image using OCR
     * This is a simplified version - in production use Tesseract OCR or Google Cloud Vision
     */
    private function extractDataFromImage($imagePath)
    {
        try {
            // Try to use Tesseract OCR if available
            if (extension_loaded('imagick') || function_exists('exec')) {
                try {
                    $ocr = new \thiagoalessio\TesseractOCR\TesseractOCR($imagePath);
                    $text = $ocr->run();
                    
                    // Parse the extracted text to extract structured data
                    return $this->parseExtractedText($text);
                } catch (\Exception $e) {
                    // Log the OCR error and fall back to manual parsing
                    \Log::error('Tesseract OCR failed: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('OCR processing failed: ' . $e->getMessage());
        }
        
        // Fallback: Return empty data structure if OCR fails
        return [
            'company_name' => '',
            'billing_month' => '',
            'records' => [],
            'error' => 'OCR not available - please install Tesseract OCR'
        ];
    }
    
    /**
     * Parse extracted text to structured data
     */
    private function parseExtractedText($text)
    {
        $records = [];
        $lines = explode("\n", $text);
        
        // This is a simplified parser - you would need to customize this based on your actual document format
        // Looking for patterns like: Sr | Date | Vhl No | Name | Number | Bag | Drop/Delivery Point | Km Cover | Rent | Advance | Advance Date | Guarantor | Dues | Status
        
        foreach ($lines as $line) {
            // Skip header lines and empty lines
            if (empty(trim($line)) || strpos($line, 'Sr') !== false || strpos($line, 'Date') !== false) {
                continue;
            }
            
            // Try to extract data using regex patterns
            // This is a basic pattern - you'll need to adjust based on your actual document format
            if (preg_match('/(\d+)\s+(\d{4}-\d{2}-\d{2}|\d{2}-\d{2}-\d{4})\s+([A-Z0-9-]+)\s+(.+?)\s+(\d+)\s+(\d+)\s+(.+?)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d{4}-\d{2}-\d{2}|\d{2}-\d{2}-\d{4})\s+(.+?)\s+(\d+)\s+(Paid|Pending)/', $line, $matches)) {
                $records[] = [
                    'sr' => $matches[1],
                    'date' => $matches[2],
                    'vehicle_no' => $matches[3],
                    'customer_name' => trim($matches[4]),
                    'contact_number' => $matches[5],
                    'bags' => $matches[6],
                    'delivery_point' => trim($matches[7]),
                    'km_covered' => $matches[8],
                    'rent' => $matches[9],
                    'advance' => $matches[10],
                    'advance_date' => $matches[11],
                    'guarantor' => trim($matches[12]),
                    'dues' => $matches[13],
                    'status' => $matches[14]
                ];
            }
        }
        
        return [
            'company_name' => 'Extracted from Image',
            'billing_month' => 'From Document',
            'records' => $records,
            'raw_text' => $text
        ];
    }
    
    /**
     * Process extracted data and save to database
     */
    private function processExtractedData($data, $billingMonth)
    {
        $results = [
            'records_imported' => 0,
            'total' => 0,
            'errors' => []
        ];
        
        if (empty($data['records'])) {
            $results['errors'][] = 'No records found in extracted data';
            \Log::error('No records in extracted data');
            return $results;
        }
        
        \Log::info('Processing ' . count($data['records']) . ' records');
        
        foreach ($data['records'] as $record) {
            try {
                \Log::info('Processing record: ' . json_encode($record));
                
                // Create billing record directly - simple Excel-like structure
                $billingData = [
                    'sr' => $record['sr'] ?? null,
                    'date' => $record['date'] ?? now(),
                    'vehicle_no' => $record['vehicle_no'] ?? '',
                    'customer_name' => $record['customer_name'] ?? '',
                    'contact_number' => $record['contact_number'] ?? '',
                    'bags' => $record['bags'] ?? 0,
                    'delivery_point' => $record['delivery_point'] ?? '',
                    'km_covered' => $record['km_covered'] ?? 0,
                    'rent' => $record['rent'] ?? 0,
                    'advance' => $record['advance'] ?? 0,
                    'advance_date' => $record['advance_date'] ?? null,
                    'guarantor' => $record['guarantor'] ?? '',
                    'dues' => $record['dues'] ?? 0,
                    'status' => $record['status'] ?? 'Pending',
                    'billing_month' => $billingMonth,
                ];
                
                \Log::info('Creating billing record with data: ' . json_encode($billingData));
                
                $billing = Billing::create($billingData);
                
                $results['records_imported']++;
                $results['total']++;
                \Log::info('Billing record created successfully (ID: ' . $billing->id . ')');
                
            } catch (\Exception $e) {
                // Log error but continue processing other records
                $errorMsg = 'Error processing record: ' . $e->getMessage();
                $results['errors'][] = $errorMsg;
                \Log::error($errorMsg);
                \Log::error('Exception trace: ' . $e->getTraceAsString());
            }
        }
        
        \Log::info('Import results: ' . json_encode($results));
        
        return $results;
    }
}