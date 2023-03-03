<!-- comment.blade.php -->
@foreach ($comments as $comment)

    <div class="card w-75 mb-3" style="margin-left: 30px">
        <div class="card-body" style="margin-left: 30px">
            <p class="card-title">Name: {{ $comment->name }}</p>
            <p class="card-title">Email: {{ $comment->email }}</p>
            <p class="card-text">{{ $comment->content }}

            @if(!empty($comment->files))
                @foreach($comment->files as $file)
                    <div class="card-image">
                        <img src="data:image/png;base64,{{ base64_encode($file->base_64) }}" alt="Image">
                    </div>
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
