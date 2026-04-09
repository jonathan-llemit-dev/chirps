@props(['chirp'])

<div class="card bg-base-100 shadow">
    <div class="card-body space-y-3">
        <div class="flex space-x-3">
            @if ($chirp->user)
                <div class="avatar">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/{{ urlencode($chirp->user->email) }}"
                            alt="{{ $chirp->user->name }}'s avatar" class="rounded-full" />
                    </div>
                </div>
            @else
                <div class="avatar placeholder">
                    <div class="size-10 rounded-full">
                        <img src="https://avatars.laravel.cloud/f61123d5-0b27-434c-a4ae-c653c7fc9ed6?vibe=stealth"
                            alt="Anonymous User" class="rounded-full" />
                    </div>
                </div>
            @endif

            <div class="min-w-0 flex-1 space-y-2">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-1">
                            <span class="text-sm font-semibold">
                                {{ $chirp->user ? $chirp->user->name : 'Anonymous' }}
                            </span>
                            <span class="text-base-content/60">·</span>

                            @if ($chirp->updated_at->gt($chirp->created_at->addSeconds(5)))
                                <span class="text-sm text-base-content/60">
                                    {{ $chirp->updated_at->diffForHumans() }}
                                </span>
                                <span class="text-base-content/60">·</span>
                                <span class="text-sm italic text-base-content/60">edited</span>
                            @else
                                <span class="text-sm text-base-content/60">
                                    {{ $chirp->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        @can('update', $chirp)
                            <a href="{{ route('chirps.edit', $chirp) }}" class="btn btn-ghost btn-xs">
                                Edit
                            </a>
                        @endcan

                        @can('delete', $chirp)
                            <form method="POST" action="{{ route('chirps.destroy', $chirp) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this chirp?')"
                                    class="btn btn-ghost btn-xs text-error">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>

                <div class="rounded-box bg-base-200/70 px-4 py-3">
                    <p class="whitespace-pre-line text-base leading-7 text-base-content md:text-lg">{{ $chirp->message }}</p>
                </div>
                <div class="space-y-3">
                    <input type="checkbox" id="comments-toggle-{{ $chirp->id }}" class="peer hidden">

                    <div class="flex flex-wrap items-center gap-4 text-sm text-base-content/70">
                        <div class="flex flex-wrap items-center gap-2">
                            <span>{{ $chirp->likes_count }} {{ \Illuminate\Support\Str::plural('like', $chirp->likes_count) }}</span>
                            @auth
                                @if ($chirp->liked_by_current_user)
                                    <form method="POST" action="{{ route('chirps.likes.destroy', $chirp) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-outline btn-sm">
                                            Unlike
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('chirps.likes.store', $chirp) }}">
                                        @csrf

                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Like
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>

                        <label for="comments-toggle-{{ $chirp->id }}"
                            class="cursor-pointer hover:text-base-content">
                            {{ $chirp->comments_count }} {{ \Illuminate\Support\Str::plural('comment', $chirp->comments_count) }}
                        </label>
                    </div>

                    <div class="ml-4 hidden border-l-2 border-base-300 pl-4 pt-3 peer-checked:block">
                        <div class="flex flex-col space-y-3">
                            <h3 class="text-sm font-semibold text-base-content/80">Comments</h3>

                            @forelse ($chirp->comments as $comment)
                                <div class="w-full rounded-box bg-base-200/80 px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2 text-sm">
                                        <span class="font-medium">{{ $comment->user->name }}</span>
                                        <span class="text-base-content/50">·</span>
                                        <span class="text-base-content/60">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>

                                    <p class="mt-2 whitespace-pre-line text-sm leading-6">{{ $comment->message }}</p>

                                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-base-content/70">
                                        <span>{{ $comment->likes_count ?? 0 }} {{ \Illuminate\Support\Str::plural('like', $comment->likes_count ?? 0) }}</span>

                                        @auth
                                            @if ($comment->liked_by_current_user ?? false)
                                                <form method="POST" action="{{ route('comments.likes.destroy', $comment) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-ghost btn-xs">
                                                        Unlike
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('comments.likes.store', $comment) }}">
                                                    @csrf

                                                    <button type="submit" class="btn btn-ghost btn-xs">
                                                        Like
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-base-content/60">No comments yet. Start the conversation.</p>
                            @endforelse

                            @auth
                                <form method="POST" action="{{ route('chirps.comments.store', $chirp) }}" class="w-full space-y-2">
                                    @csrf

                                    <div class="form-control">
                                        <textarea name="message" rows="3" maxlength="255" required
                                            placeholder="Write a comment..."
                                            class="textarea textarea-bordered w-full resize-none @error('message') textarea-error @enderror">{{ old('message') }}</textarea>

                                        @error('message')
                                            <div class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            Comment
                                        </button>
                                    </div>
                                </form>
                            @endauth
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
