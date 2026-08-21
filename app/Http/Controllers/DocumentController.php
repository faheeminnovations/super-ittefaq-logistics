<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::paginate(15);
        $allDocuments = Document::all();

        return view('pages.documents', [
            'documents' => $documents,
            'totalDocuments' => $allDocuments->count(),
            'verifiedDocuments' => $allDocuments->where('status', 'verified')->count(),
            'expiringSoonDocuments' => $allDocuments->where('status', 'expiring_soon')->count(),
            'expiredDocuments' => $allDocuments->where('status', 'expired')->count(),
        ]);
    }

    public function create()
    {
        return view('pages.documents-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
            'related_entity_type' => 'nullable|string|max:50',
            'related_entity_id' => 'nullable|integer',
            'file_path' => 'nullable|file|max:10240', // Max 10MB
            'status' => 'required|in:verified,expiring_soon,expired,pending',
            'description' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            $validated['file_path'] = $filePath;
        }

        Document::create($validated);
        return redirect()->route('documents.index')->with('success', 'Document created successfully.');
    }

    public function show(string $id)
    {
        $document = Document::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['document' => $document]);
        }
        
        return view('pages.documents-show', compact('document'));
    }

    public function edit(string $id)
    {
        $document = Document::findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($document);
        }
        
        return view('pages.documents-edit', compact('document'));
    }

    public function update(Request $request, string $id)
    {
        $document = Document::findOrFail($id);
        $validated = $request->validate([
            'document_name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'expiry_date' => 'nullable|date',
            'related_entity_type' => 'nullable|string|max:50',
            'related_entity_id' => 'nullable|integer',
            'file_path' => 'nullable|file|max:10240', // Max 10MB
            'status' => 'required|in:verified,expiring_soon,expired,pending',
            'description' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');
            $validated['file_path'] = $filePath;
        }

        $document->update($validated);
        return redirect()->route('documents.index')->with('success', 'Document updated successfully.');
    }

    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    public function download(string $id)
    {
        $document = Document::findOrFail($id);
        
        if (!$document->file_path) {
            return redirect()->back()->with('error', 'No file associated with this document.');
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $document->document_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }
}
