@extends('layouts.result')


@section('content')

<div class="row">
    <div class="invoice p-3 mb-3">
        <div class="row" >
        <div class="col-12">
            <h4>
                <img src="{{ asset('img/logomain.png') }}" class="brand-image img-circle elevation-3" width="50px" height="70" alt="">
            GTI Health Care.

            </h4>
        </div>
        </div>

        <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">

            <address>
            <strong>GTI HC Lab,</strong><br />
            HP Nyametei Street F809/1<br />
            OSU, ACCRA - GHANA<br />
            GPS: GA-032-1300<br />
            Toll Free: 0800790555<br />
            Email: info@gtihc.com
            </address>
        </div>

        <div class="col-sm-4 invoice-col" >
            <address>
            <strong><b>Client Name:</b></strong>{{ $data->patient->first_name.' '.$data->patient->middle_name.' '.$data->patient->last_name}}<br />
            <strong><b>Client Code:</b></strong>{{ $data->patient->customerId }}<br />
            <b>Number:</b>{{ $data->patient->mobile }}<br />
            <b>Address:</b>{{ $data->patient->address }}<br />
           <b>Gender:</b>{{ $data->patient->gender = 'M' ? 'Male': 'Female'}}<br />
           <b>Email:</b>{{ $data->patient->email }}<br />
            </address>
        </div>

        <div class="col-sm-4 invoice-col" >
            <b></b><br />
            <br />
            <b>Batch:</b>{{ $data->batch }}<br />
        </div>
        </div>

        <div class="row" >
        <div class="col-12 table-responsive">
            <table class="table table-striped">
            <thead>
                <tr>
                <th>#</th>
                <th>Test</th>
                <th>Comments</th>
                <th>Results</th>
                <th>Authorised By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                <tr>
                    <td>1</td>
                     <td>{{ $result->price->test }}</td>
                    <td>{{ $result->comments }}</td>
                    <td>{{ $result->results }}</td>
                    <td>{{ $result->controlled_by }}</td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        </div>

        <div class="row">
        <div class="col-6">
            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px">
            Methods used are included in the MOH, Hefra, FDA, AHPC schedule of Accreditation for this Laboratory.
            This document shall not be reproduced except in full, without the written approval from GTI Healthcare Laboratory Management
            </p>
        </div>

        <div class="col-6">
            <div class="table-responsive">
            <table class="table" >
                <tbody>
                <tr>
                    <th style="width: 50%">Date Collected:</th>
                    <td>{{ $data->created_at }}</td>
                </tr>
                <tr>
                    <th>Date Received:</th>
                    <td>{{ $data->created_at }}</td>
                <tr>
                    <th>Date Verified:</th>
                    <td>{{ $data->updated_at}}</td>
                </tr>
                <tr>
                    <th>Date Published:</th>
                    <td>{{ $data->updated_at }}</td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>
        </div>


    </div>
</div>

@endsection
