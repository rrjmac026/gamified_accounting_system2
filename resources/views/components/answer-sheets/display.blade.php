@props(['data' => null, 'step' => 1])

<div class="answer-sheet-display-{{ $step }}"></div>

<script>
    (function() {
        const container = document.querySelector('.answer-sheet-display-{{ $step }}');
        const displayData = @json($data ? (is_string($data) ? json_decode($data, true) : $data) : []);
        
        new Handsontable(container, {
            data: displayData,
            readOnly: true,
            rowHeaders: true,
            colHeaders: true,
            height: 400,
            licenseKey: 'non-commercial-and-evaluation',
            stretchH: 'all'
        });
    })();
</script>