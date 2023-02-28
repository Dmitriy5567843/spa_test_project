<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Blog - Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</head>
<body>


<script>
    $(document).ready(function () {
        $('button[id^="addCommentBtn"]').click(function () {
            var parentId = $(this).data('id');
            $('#parent_id').val(parentId);
        });
    });
</script>
{{--cкрипт для загрузки файла--}}
<script>
    function loadImagePreview(event) {
        var imagePreviewContainer = document.getElementById('image-preview-container');
        imagePreviewContainer.innerHTML = '';
        for (var i = 0; i < event.target.files.length; i++) {
            var image = document.createElement('img');
            image.src = URL.createObjectURL(event.target.files[i]);
            image.onload = function () {
                URL.revokeObjectURL(image.src);
            };
            imagePreviewContainer.appendChild(image);
        }
    }
</script>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container my-5">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1>Сreate new comments</h1>

    {{--форма создания--}}
    <form action="{{route('comment.create')}}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mb-3">
            <div class="col">
                <label for="name" class="form-label">Name:</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="col">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comment:</label>
                <div class="input-group">
                    <input type="file" name="files[]" class="form-control" id="inputGroupFile" multiple
                           onchange="loadImagePreview(event)">
                </div>
                <textarea id="comment" name="content" class="form-control mt-3" required></textarea>
                <div id="image-preview-container"></div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>

    </form>


</main>

{{--форма карточки комментария--}}
<div class="container">
    @foreach ($comments as $comment)
        <div class="card mb-5">
            <div class="card-body">
                <p class="card-title">Name: {{ $comment->name }}</p>
                <p class="card-title">Email: {{ $comment->email }}</p>
                <p class="card-text">{{ $comment->content }}
                </p>
                <button id="addCommentBtn{{$comment->id}}" data-id="{{$comment->id}}" type="button"
                        class="btn btn-primary" data-toggle="modal"
                        data-target="#addCommentModal">
                    Добавить комментарий
                </button>
            </div>
            @if (count($comment->children) > 0)
                <div class="container children">
                    @include('comment', ['comments' => $comment->children])
                </div>
            @endif
        </div>
    @endforeach
</div>

<div>
    {{--модальное окно для добавления комментария--}}
    <div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog"
         aria-labelledby="addCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCommentModalLabel">Добавить комментарий</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- форма для добавления комментария -->
                    <form method="POST" action="{{ route('comments.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Комментарий</label>
                            <textarea class="form-control" id="content" name="content" rows="3"
                                      required></textarea>
                            <div class="input-group">
                                <input type="file" name="files[]" class="form-control" id="inputGroupFile" multiple
                                       onchange="loadImagePreview(event)">
                            </div>
                        </div>

                        <input type="hidden" name="parent_id" id="parent_id" value="">
                        <button type="submit" class="btn btn-primary">Добавить комментарий</button>
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


{{--        пагинация--}}
<div class="d-flex justify-content-center">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <li class="page-item {{ ($comments->currentPage() == 1) ? ' disabled' : '' }}">
                <a class="page-link" href="{{ $comments->url(1) }}">Previous</a>
            </li>
            @for ($i = 1; $i <= $comments->lastPage(); $i++)
                <li class="page-item {{ ($comments->currentPage() == $i) ? ' active' : '' }}">
                    <a class="page-link" href="{{ $comments->url($i) }}">{{ $i }}</a>
                </li>
            @endfor
            <li class="page-item {{ ($comments->currentPage() == $comments->lastPage()) ? ' disabled' : '' }}">
                <a class="page-link" href="{{ $comments->url($comments->currentPage()+1) }}">Next</a>
            </li>
        </ul>
    </nav>
</div>
<footer class="bg-light py-3">
    <div class="container">
        <p>&copy; 2023 My Blog</p>
    </div>
</footer>

</body>
</html>
