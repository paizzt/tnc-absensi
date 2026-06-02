<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubjectService;
use App\Http\Requests\SaveSubjectRequest;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    public function index()
    {
        $subjects = $this->subjectService->getSubjectsByCurrentSchool();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(SaveSubjectRequest $request)
    {
        $this->subjectService->createSubject($request->validated());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function update(SaveSubjectRequest $request, $id)
    {
        $this->subjectService->updateSubject($id, $request->validated());
        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $this->subjectService->deleteSubject($id);
        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}