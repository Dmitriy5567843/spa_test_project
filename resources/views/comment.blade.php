<!-- comment.blade.php -->
@foreach ($comments as $comment)

    <div class="card w-75 mb-3" style="margin-left: 30px">
        <div class="card-body" style="margin-left: 30px">
            <p class="card-title">Name: {{ $comment->name }}</p>
            <p class="card-title">Email: {{ $comment->email }}</p>
            <p class="card-text">{{ $comment->content }}

            @if(!empty($comment->files))
                @foreach($comment->files as $file)
                    {{--                        {{dd($file)}}--}}
                    @if($file->type === 'txt')
                        <div class="card mb-2">
                            <div class="card-body">
                                <h6 class="card-title">{{ $file->name }}</h6>
                                <a href="{{route('download', $file->name)}}" class="btn btn-primary" target="_blank">Скачать</a>
                            </div>
                        </div>
                    @else
                        <div class="card-image" style="margin-bottom:10px">
                            <img src="data:image/png;base64,{{ base64_encode($file->base_64) }}" alt="Image">
                        </div>
                    @endif
                @endforeach
            @endif


            <button id="addCommentBtn{{$comment->id}}" data-id="{{$comment->id}}" type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#addCommentModal">
                Добавить комментарий
            </button>
        </div>
    </div>

    @if (count($comment->children) > 0)
        <div class="container children">
            @include('comment', ['comments' => $comment->children])
        </div>
    @endif
@endforeach
