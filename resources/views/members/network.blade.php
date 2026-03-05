@extends('layout')

@section('content')
    <div class="row" style="justify-content: space-between; align-items: center;">
        <h1>成員上下線關係</h1>
        <a href="{{ route('dashboard') }}">返回儀表板</a>
    </div>

    <table>
        <thead>
        <tr>
            <th>成員</th>
            <th>上線</th>
            <th>下線</th>
            <th>下下線</th>
        </tr>
        </thead>
        <tbody>
        @foreach($members as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->sponsor?->name ?? '無' }}</td>
                <td>
                    @forelse($member->recruits as $downline)
                        {{ $downline->name }}<br>
                    @empty
                        無
                    @endforelse
                </td>
                <td>
                    @php($second = $member->recruits->flatMap->recruits)
                    @forelse($second as $downline2)
                        {{ $downline2->name }}<br>
                    @empty
                        無
                    @endforelse
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
