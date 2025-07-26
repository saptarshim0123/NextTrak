<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{   
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Application::class);
        
        $applications = auth()->user()->applications()->latest()->get();
        
        return view('applications.index', [
            'applications' => $applications,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Application::class);
        
        return view('applications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApplicationRequest $request): RedirectResponse
    {
        $this->authorize('create', Application::class);
        
        $application = auth()->user()->applications()->create($request->validated());
        
        return redirect()->route('applications.index')
            ->with('success', 'Application created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Application $application): View
    {
        $this->authorize('view', $application);
        
        return view('applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Application $application): View
    {
        $this->authorize('update', $application);
        
        return view('applications.edit', compact('application'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateApplicationRequest $request, Application $application): RedirectResponse
    {
        $this->authorize('update', $application);
        
        $application->update($request->validated());
        
        return redirect()->route('applications.index')
            ->with('success', 'Application updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Application $application): RedirectResponse
    {
        $this->authorize('delete', $application);
        
        $application->delete();
        
        return redirect()->route('applications.index')
            ->with('success', 'Application deleted successfully.');
    }

    /**
     * Legacy method for backward compatibility.
     */
    public function list(): View
    {
        return $this->index();
    }
}
