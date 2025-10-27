<x-app-layout>
    <div class="max-w-4xl mx-auto py-6">
        <h2 class="text-2xl font-bold mb-4">{{ $quiz->title }}</h2>
        <p class="mb-6">{{ $quiz->description }}</p>

        <form action="{{ route('quizzes.submit', $quiz->id) }}" method="POST">
            @csrf
            @foreach($quiz->questions as $question)
                <div class="mb-6">
                    <p class="font-medium">{{ $loop->iteration }}. {{ $question->question_text }}</p>

                    @if($question->type === 'multiple_choice')
                        @foreach($question->options as $option)
                            <label class="block">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}">
                                {{ $option }}
                            </label>
                        @endforeach

                    @elseif($question->type === 'true_false')
                        <label class="block">
                            <input type="radio" name="answers[{{ $question->id }}]" value="true"> True
                        </label>
                        <label class="block">
                            <input type="radio" name="answers[{{ $question->id }}]" value="false"> False
                        </label>

                    @elseif($question->type === 'identification')
                        <input type="text" name="answers[{{ $question->id }}]" class="border rounded p-2 w-full">
                    @endif
                </div>
            @endforeach

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Submit Answers
            </button>
        </form>
    </div>
</x-app-layout>
