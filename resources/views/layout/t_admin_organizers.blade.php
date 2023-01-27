    <div class="card">
      <div class="row card-content">
        @if ($users['user_type'] == 1 or $users['user_type'] == 9)
        <div class="col s12 m4">
          <select id="organizers" class="select2" style="width: 100%">
            <option value=""></option>
            @foreach ($organizers as $organizer)
            <option value="{{ $organizer->organizer_code }}">{{ $organizer->organizer_name }}</option>
            @endforeach
          </select>
        </div>
        @endif
      </div>
    </div>