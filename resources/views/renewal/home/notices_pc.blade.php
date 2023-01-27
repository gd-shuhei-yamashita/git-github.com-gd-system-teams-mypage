{{-- ホーム画面 - 各種お知らせ PC版 --}}
<div class="news-pc">
    <div class="news-area">
        <p>各種お知らせ</p>
        <ul id="result1_list">
        @if (isset($notices))
        @forelse ($notices as $notice)
            <li>
                <span class="date">{{ str_replace('-', '/', $notice->notice_date) }}</span>
                @if (isset($notice->url) && $notice->url)<a href="{{ $notice->url }}">
                    <a href="{{ $notice->url }}">{!! str_replace('\n', ' ', $notice->notice_comment) !!}</a>
                @else
                    {!! str_replace('\n', ' ', $notice->notice_comment) !!}
                @endif
            </li>
        @empty
            <li><span class="date">----/--/--</span><a href="#">お知らせはありません</a></li>
        @endforelse
        @else
            <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
            <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
            <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
        @endif
        </ul>
        <div class="news-link">
            <a href="https://grandata-service.jp/news/" target="_blank" rel="noopener noreferrer" style="position: relative;">
                全て表示
                <i class="fa-solid fa-up-right-from-square"></i>
            </a>
        </div>
    </div>
</div>
