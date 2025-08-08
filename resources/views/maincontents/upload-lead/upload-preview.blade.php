<?php
use App\Helpers\Helper;
$controllerRoute = $module['controller_route'];
?>
@extends('layouts.main')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="row g-6">
            <h4><?= $page_header ?></h4>
            <h6 class="breadcrumb-wrapper">
                <span class="text-muted fw-light"><a href="<?= url('dashboard') ?>">Dashboard</a> /</span>
                <?= $page_header ?>
            </h6>
        </div>

        @if((!empty($counts['duplicateWRTCampaign'])) || (!empty($counts['duplicateInFile'])))
            <div class="alert alert-secondary alert-dismissible autohide" role="alert">
                <h6 class="alert-heading mb-1"><i class="bx bx-xs bx-store align-top me-2"></i>Warning!</h6>
                @if(!empty($counts['duplicateWRTCampaign']))
                    <span>{{$counts['duplicateWRTCampaign']}} Lead(s) Duplicate In The Same Campaign !!!</span>
                    <br>
                @endif
                
                @if(!empty($counts['duplicateInFile']))
                    <span>{{$counts['duplicateInFile']}} Lead(s) Duplicate In The Same File !!!</span>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif


        <div class="row mb-2 d-flex justify-content-center flex-wrap">
            <div class="mb-2" style="min-width: 150px; flex: 1 1 20%;">
                <div class="card text-center">
                    <div class="card-body">
                        <h6 style="margin-bottom: 3px;">Total Leads</h6>
                        <h6 style="margin-bottom: 0;">{{ $counts['total'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="mb-2" style="min-width: 150px; flex: 1 1 20%;">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h6 style="margin-bottom: 3px;">Invalid Leads</h6>
                        <h6 style="margin-bottom: 0;">{{ $counts['invalid'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="mb-2" style="min-width: 150px; flex: 1 1 20%;">
                <div class="card text-center bg-warning">
                    <div class="card-body">
                        <h6 style="margin-bottom: 3px;">Existing Leads</h6>
                        <h6 style="margin-bottom: 0;">{{ $counts['duplicate'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="mb-2" style="min-width: 150px; flex: 1 1 20%;">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h6 style="margin-bottom: 3px;">New Leads</h6>
                        <h6 style="margin-bottom: 0;">{{ $counts['new'] }}</h6>
                    </div>
                </div>
            </div>
            <div class="mb-2" style="min-width: 150px; flex: 1 1 20%;">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h6 style="margin-bottom: 3px;">Assignable Leads</h6>
                        <h6 style="margin-bottom: 0;">{{ $counts['assinableLead'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    

        <h5 class="mb-2">Lead Title: {{ $lead_title }} | Branch Name: {{ $branch_name }} | Lead Date:
            {{ $lead_date }}</h5>

        <form method="POST" action="<?= url($controllerRoute . '/store') ?>" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="temp_file" value="{{ $tempFile }}">
            <input type="hidden" name="branch_id" value="{{ $branch_id }}">
            <input type="hidden" name="lead_title" value="{{ $lead_title }}">
            <input type="hidden" name="lead_date" value="{{ $lead_date }}">
            <input type="hidden" name="campaign_type_id" value="{{ $campaign_type_id }}">
            <input type="hidden" name="campaign_id" value="{{ $campaign_id }}">
            @foreach ($telecaller_id as $id)
                <input type="hidden" name="telecaller_id[]" value="{{ $id }}">
            @endforeach

            <div class="card p-2">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sl No.</th>
                                <th>Status</th>
                                {{-- <th>Comment</th> --}}
                                <th>Telecaller</th>
                                @foreach ($headers as $header)
                                    <th>{{ $header }}</th>
                                @endforeach                              
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $index => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                      <span>{!! $row['status'] !!} <strong>{!! $row['comment'] !!}</strong></span>
                                    </td>
                                    <td>{{ $row['telecaller'] }}</td>
                                    @foreach ($row['original'] as $value)
                                        <td>{!! $value !!}</td>
                                    @endforeach                                   
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-outline-dark btn-sm me-2">Confirm</button>
                {{-- <a href="<?= url($controllerRoute) ?>" class="btn btn-outline-danger btn-sm">Cancel</a> --}}
                <a href="{{ url($controllerRoute .'/cancel-upload/' . basename($tempFile)) }}" class="btn btn-outline-danger btn-sm">
                Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
