{{-- サンキューレター改ページ --}}
@php
    if ($rows == 33 || ($rows - 33) % 70 == 0) {
        echo '</table><div class="page_break"></div><table  border="1">';
    }
@endphp
