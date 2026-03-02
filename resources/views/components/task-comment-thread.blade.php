{{--
    resources/views/components/task-comment-thread.blade.php

    Props:
        $comments   – collection of top-level PerformanceTaskComment (with replies)
        $task       – PerformanceTask model
        $storeRoute – named route to POST a comment
        $role       – 'student' | 'instructor'  (the current viewer)
        $step       – int|null
        $stepTitles – array
--}}

<div class="flex flex-col h-full" id="commentThread">

    {{-- ── Step filter pills ──────────────────────────────────────────── --}}
    <div class="flex items-center gap-2 flex-wrap mb-6 pb-4 border-b border-gray-100">
        <a href="{{ request()->url() }}"
           class="px-3 py-1 rounded-full text-xs font-medium transition
                  {{ is_null($step) ? 'bg-[#FF92C2] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            All
        </a>
        @foreach($stepTitles as $num => $title)
            <a href="{{ request()->url() }}?step={{ $num }}"
               class="px-3 py-1 rounded-full text-xs font-medium transition whitespace-nowrap
                      {{ (int)$step === $num ? 'bg-[#FF92C2] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Step {{ $num }}
            </a>
        @endforeach
    </div>

    {{-- ── Messages ────────────────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto space-y-5 mb-6 pr-1" id="messagesContainer"
         style="max-height: 480px;">

        @forelse($comments as $comment)
            @php
                $isMine = $comment->sender_role === $role;
            @endphp

            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} group">
                <div class="max-w-[75%]">

                    {{-- Sender label --}}
                    <div class="flex items-center gap-2 mb-1 {{ $isMine ? 'flex-row-reverse' : '' }}">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shadow-sm
                                    {{ $isMine ? 'bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] text-white'
                                               : 'bg-gradient-to-br from-blue-400 to-indigo-500 text-white' }}">
                            {{ strtoupper(substr($comment->sender->name ?? '?', 0, 1)) }}
                        </div>
                        <span class="text-xs text-gray-500 font-medium">
                            {{ $isMine ? 'You' : ($comment->sender->name ?? 'Unknown') }}
                        </span>
                        <span class="text-[10px] text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @if($comment->step)
                            <span class="text-[10px] px-2 py-0.5 bg-purple-50 text-purple-600 rounded-full font-medium">
                                Step {{ $comment->step }}
                            </span>
                        @endif
                    </div>

                    {{-- Bubble --}}
                    <div class="relative px-4 py-3 rounded-2xl text-sm shadow-sm
                                {{ $isMine
                                    ? 'bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] text-white rounded-tr-sm'
                                    : 'bg-white border border-gray-100 text-gray-800 rounded-tl-sm' }}">
                        <p class="leading-relaxed whitespace-pre-wrap">{{ $comment->body }}</p>

                        {{-- Delete button (own comments) --}}
                        @if($isMine)
                            <form method="POST"
                                  action="{{ route(str_replace('comments.show', 'comments.destroy', request()->route()->getName()), ['task' => $task->id, 'comment' => $comment->id]) }}"
                                  class="absolute -top-2 -right-2 hidden group-hover:block"
                                  onsubmit="return confirm('Delete this comment?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center shadow-sm hover:bg-red-600 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Replies --}}
                    @if($comment->replies->count())
                        <div class="mt-3 ml-4 space-y-2 border-l-2 border-gray-100 pl-4">
                            @foreach($comment->replies as $reply)
                                @php $replyIsMine = $reply->sender_role === $role; @endphp
                                <div class="flex {{ $replyIsMine ? 'justify-end' : 'justify-start' }} group/reply">
                                    <div class="max-w-full">
                                        <div class="flex items-center gap-1.5 mb-0.5 {{ $replyIsMine ? 'flex-row-reverse' : '' }}">
                                            <span class="text-xs text-gray-500">{{ $replyIsMine ? 'You' : ($reply->sender->name ?? 'Unknown') }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="px-3 py-2 rounded-xl text-xs shadow-sm relative
                                                    {{ $replyIsMine
                                                        ? 'bg-gradient-to-br from-[#FF92C2]/80 to-[#FFC8FB]/80 text-white rounded-tr-sm'
                                                        : 'bg-gray-50 border border-gray-100 text-gray-700 rounded-tl-sm' }}">
                                            <p class="whitespace-pre-wrap">{{ $reply->body }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Reply toggle --}}
                    <div class="mt-1 {{ $isMine ? 'text-right' : 'text-left' }}">
                        <button onclick="toggleReply('reply-{{ $comment->id }}')"
                                class="text-[11px] text-gray-400 hover:text-[#FF92C2] transition font-medium">
                            <i class="fas fa-reply text-[10px] mr-1"></i>Reply
                        </button>
                    </div>

                    {{-- Reply form --}}
                    <div id="reply-{{ $comment->id }}" class="hidden mt-2">
                        <form method="POST"
                              action="{{ route($storeRoute, ['task' => $task->id]) }}{{ $step ? '?step='.$step : '' }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <div class="flex gap-2">
                                <input type="text" name="body"
                                       placeholder="Write a reply…"
                                       maxlength="2000"
                                       class="flex-1 text-xs border border-gray-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#FF92C2]/30"
                                       required>
                                <button type="submit"
                                        class="px-3 py-2 bg-[#FF92C2] text-white rounded-xl text-xs font-medium hover:bg-[#ff79b5] transition">
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                    <i class="fas fa-comment-dots text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium text-sm">No messages yet</p>
                <p class="text-gray-400 text-xs mt-1">
                    {{ $role === 'student' ? 'Ask your instructor a question below.' : 'Start the conversation with students.' }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- ── Compose ─────────────────────────────────────────────────────── --}}
    <div class="border-t border-gray-100 pt-4">
        @if(session('success'))
            <div class="mb-3 px-3 py-2 bg-green-50 border border-green-200 text-green-700 text-xs rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route($storeRoute, ['task' => $task->id]) }}{{ $step ? '?step='.$step : '' }}"
              id="composeForm">
            @csrf

            {{-- Step selector --}}
            <div class="flex items-center gap-2 mb-3">
                <label class="text-xs text-gray-500 font-medium shrink-0">Tag step (optional):</label>
                <select name="step"
                        class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-[#FF92C2]/30">
                    <option value="">— General —</option>
                    @foreach($stepTitles as $num => $title)
                        <option value="{{ $num }}" {{ (int)$step === $num ? 'selected' : '' }}>
                            Step {{ $num }}: {{ $title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3">
                <textarea name="body"
                          rows="2"
                          maxlength="2000"
                          placeholder="Type your message…"
                          required
                          class="flex-1 text-sm border border-gray-200 rounded-xl px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-[#FF92C2]/30 transition">{{ old('body') }}</textarea>
                <button type="submit"
                        class="self-end px-5 py-3 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] text-white rounded-xl font-semibold text-sm shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-paper-plane mr-1"></i>Send
                </button>
            </div>
            @error('body')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </form>
    </div>
</div>

<script>
function toggleReply(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.querySelector('input').focus();
    }
}

// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('messagesContainer');
    if (container) container.scrollTop = container.scrollHeight;
});
</script>