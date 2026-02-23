<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class DocumentService
{
    public function saveDocument(array $data): Document
    {
        try {
            Log::info('Creating new document', $data);
            
            // Validate ticket exists
            $ticket = Ticket::where('uuid', $data['ticket_id'])->firstOrFail();
            
            // Create document record
            $document = Document::create([
                'ticket_id' => $ticket->id,
                'nama_document' => $data['nama_document'],
                'description_document' => $data['description_document'] ?? null,
                'penandatangan_document' => $data['penandatangan_document'] ?? null,
                'penerima_document' => $data['penerima_document'] ?? null,
                'tindakan_document' => $data['tindakan_document'] ?? null,
                'no_document' => $data['no_document'] ?? null,
                'catatan' => $data['catatan'] ?? null,
                'pdf_path' => $data['pdf_path'] ?? null,
                'qr_position_x' => $data['qr_position_x'] ?? null,
                'qr_position_y' => $data['qr_position_y'] ?? null,
                'qr_scale' => $data['qr_scale'] ?? null,
            ]);

            // Generate QR code if PDF path provided
            if (!empty($data['pdf_path'])) {
                $this->generateQrCode($document);
            }

            Log::info('Document created successfully', ['document_id' => $document->id]);
            
            return $document;
            
        } catch (\Exception $e) {
            Log::error('Error creating document', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateDocument(Document $document, array $data): Document
    {
        try {
            Log::info('Updating document', ['document_id' => $document->id, 'data' => $data]);
            
            $document->update($data);
            
            // Regenerate QR if PDF path changed
            if (isset($data['pdf_path']) && $data['pdf_path'] !== $document->getOriginal('pdf_path')) {
                $this->generateQrCode($document);
            }
            
            Log::info('Document updated successfully', ['document_id' => $document->id]);
            
            return $document->fresh();
            
        } catch (\Exception $e) {
            Log::error('Error updating document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function saveQrPosition(Document $document, float $x, float $y, float $scale): Document
    {
        try {
            Log::info('Saving QR position', [
                'document_id' => $document->id,
                'x' => $x,
                'y' => $y,
                'scale' => $scale
            ]);
            
            $document->update([
                'qr_position_x' => $x,
                'qr_position_y' => $y,
                'qr_scale' => $scale
            ]);
            
            Log::info('QR position saved successfully', ['document_id' => $document->id]);
            
            return $document->fresh();
            
        } catch (\Exception $e) {
            Log::error('Error saving QR position', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function generateQrCode(Document $document): void
    {
        try {
            if (empty($document->pdf_path)) {
                throw new \Exception('PDF path is required to generate QR code');
            }

            // Create QR directory if doesn't exist
            Storage::disk('public')->makeDirectory('qrcodes');
            
            // Generate QR filename
            $fileName = pathinfo($document->pdf_path, PATHINFO_FILENAME);
            $qrFileName = "document_{$document->uuid}_{$fileName}.png";
            $qrPath = "qrcodes/{$qrFileName}";
            
            // Generate QR content (URL to PDF)
            $qrContent = url('storage/' . ltrim($document->pdf_path, '/'));
            
            // Generate QR image
            $qrImage = QrCode::format('png')->size(300)->generate($qrContent);
            
            // Save QR image
            Storage::disk('public')->put($qrPath, $qrImage);
            
            // Update document with QR path
            $document->update(['qr_path' => $qrPath]);
            
            Log::info('QR code generated successfully', [
                'document_id' => $document->id,
                'qr_path' => $qrPath,
                'qr_content' => $qrContent
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating QR code', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function deleteDocument(Document $document): bool
    {
        try {
            Log::info('Deleting document', ['document_id' => $document->id]);
            
            // Delete QR file if exists
            if ($document->qr_path && Storage::disk('public')->exists($document->qr_path)) {
                Storage::disk('public')->delete($document->qr_path);
            }
            
            // Delete document record
            $deleted = $document->delete();
            
            Log::info('Document deleted successfully', ['document_id' => $document->id]);
            
            return $deleted;
            
        } catch (\Exception $e) {
            Log::error('Error deleting document', [
                'document_id' => $document->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getAllDocuments(?string $ticketUuid = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Document::with('ticket')->where('is_active', true);
        
        if ($ticketUuid) {
            $query->whereHas('ticket', function ($q) use ($ticketUuid) {
                $q->where('uuid', $ticketUuid);
            });
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getDocumentByUuid(string $uuid): ?Document
    {
        return Document::with('ticket')->where('uuid', $uuid)->first();
    }
}