@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Dusk Spider Results</div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">URL</th>
                                <th scope="col">Title</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pages as $page)
                                <tr>
                                    <td scope="row">{{($pages->currentpage()-1) * $pages->perpage() + $loop->index + 1}}</td>
                                    <td>{{$page->url}}</td>
                                    <td>{{$page->title}}</td>
                                    @if($page->status == 200)
                                        <td style="color:green">{{$page->status}}</td>
                                    @elseif($page->status == 302)
                                        <td style="color:#848407">{{$page->status}}</td>
                                    @elseif($page->status == 404)
                                        <td style="color:red">{{$page->status}}</td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $pages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
