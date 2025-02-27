<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Imports\ScoreImport;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;


new class extends Component {
    use WithFileUploads;

    public $file_excel;
    public string $html_table;

    public function importExcelFile(): void
    {
        $validated = $this->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);
        Excel::import(new ScoreImport,$validated['file_excel']);
    }

}; ?>


<section class="w-full">
    <style>
        /* Table Container */
        .table-container {
            width: 80%;
            margin: 20px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Table Styles */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        /* Table Header */
        .styled-table thead {
            background-color: #007bff;
            color: #ffffff;
            text-align: left;
        }

        .styled-table th, .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        /* Zebra Striping */
        .styled-table tbody tr:nth-child(even) {
            background-color: rgba(46, 204, 113,0.1);
        }

        /* Hover Effect */
        .styled-table tbody tr:hover {
            background-color: rgba(44, 62, 80,1.0);
            color: rgba(236, 240, 241,1.0);
            cursor: pointer;
        }
    </style>

    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">Import File</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Test upload') }}</flux:subheading>
        <flux:separator variant="subtle"/>
    </div>
    <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <form wire:submit="importExcelFile" class="my-6 w-full space-y-6" enctype="multipart/form-data">
            <flux:input wire:model="file_excel" label="{{ __('Choose Excel File') }}" type="file" name="file_excel"/>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('Save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>

            </div>
        </form>
    </div>
    <div class="relative mb-6 w-full">
        @if(session('success'))
            <flux:heading size="xl" level="1">Imported Result</flux:heading>
        <div class="table-container">
            {!! session('success') !!}
        </div>
        @endif
        @if(session('error'))
                {!! session('error') !!}
        @endif
    </div>

</section>
