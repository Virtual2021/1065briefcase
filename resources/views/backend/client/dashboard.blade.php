@extends('layouts.client')

@section('content')

                <div class="bottom-bar">
                    <div class="breadcrumb-bar">
                        <ul class="breadcrumbs">
                            <li>
                                <a href="javascript:void(0)" class="btns btns-default">
                                    {{$userClientData->name}}</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Header of content area end -->

                <div class="project-grid">
                    @if($projectDatas != NULL)
                    @foreach ($projectDatas as $projectData)    
                    <div class="project-tiles">
                        <div class="project-inner">
                        <a href="{{ route('client.project.detail', $projectData->id) }}" class="btns btns-default">{{$projectData->name}}</a>
                        </div>
                    </div>
                   @endforeach
                   @else
                        <div class="project-tiles">
                        <div class="project-inner">
                        <a href="javascript:void(0)" class="btns btns-default">No Project Assigned</a>
                        </div>
                    </div>
                   @endif

                </div>
            </div>



@endsection