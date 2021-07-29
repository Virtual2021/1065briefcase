@extends('layouts.staff')

@section('content')

    <div class="bottom-bar">
        <div class="breadcrumb-bar">
            <ul class="breadcrumbs">
                <li>
                    <a href="javascript:void(0)" class="btns  btns-default">Clients</a>
                </li>
            </ul>
        </div>
    </div>
     <!-- Header of content area end -->

    <div class="gray-box">
        <div class="gray-inner">
            <h2 class="section-title">Add New Client</h2>

            <form class="client-form" method="post" action="{{ route('backend.add.client') }}" id="client-form">
                 {{ csrf_field() }}
                 @include('includes.backend.form-both')
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <input type="text" class="form-control" name="clientName" placeholder="Enter Client's Name">

                    </div>

                    <div class="col-sm-6 col-12">
                        <button type="submit" id="add_client" class="btns w-auto btns-default">Activate Account</button>
                    </div>
                </div>
            </form>

            <div class="table-area">
                
                <table class="table table-striped table-responsive">
                    <tbody>
                        @foreach ($clientDatas as $clientData) 
                        <tr>
                            <td>{{$clientData->name}}</td>
                            <td><a href="{{ route('backend.client.files', $clientData->id) }}" class="btns btns-default">View Files</a>
                            </td>
                            <td><a href="{{ route('backend.client.profile', $clientData->id) }}" class="btns btns-default">Client Profile</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
@endsection
