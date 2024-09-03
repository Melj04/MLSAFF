<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Posts</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($posts as $post)
            <div class="p-4 border rounded shadow">
                <h2 class="text-xl font-bold">{{ $post->created_at }}</h2>
                <p>{{ $post->value }}</p>
            </div>
        @endforeach
    </div>
</div>
