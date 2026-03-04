@extends('layout')

@section('content')
    <div class="row" style="justify-content: space-between; align-items: center;">
        <h1>傳銷帳務系統（含 RWS API）</h1>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">登出</button></form>
    </div>

    @if(session('status'))
        <p style="color: green">{{ session('status') }}</p>
    @endif

    <div class="card">
        <p><strong>規則：</strong>預設招募人為「公司」；公司招收第一代不分獎金。若有上線則上線拿 5,000；若沒有上線（或上線為公司），5,000 回補給招募人。</p>
    </div>

    <div class="row">
        <div class="card">
            <strong>公司收入總額：</strong>{{ number_format($totalIncome) }}
        </div>
        <div class="card">
            <strong>獎金支出總額：</strong>{{ number_format($totalPayout) }}
        </div>
        <div class="card">
            <strong>公司淨額：</strong>{{ number_format($totalIncome - $totalPayout) }}
        </div>
    </div>

    <div class="card">
        <h3>新增招募</h3>
        <form method="POST" action="{{ route('members.recruit') }}">
            @csrf
            <select name="recruiter_id" required>
                <option value="">選擇招募人</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" @selected($member->id === $company->id)>{{ $member->name }}</option>
                @endforeach
            </select>
            <input type="text" name="new_member_name" placeholder="新成員名稱" required>
            <button type="submit">送出</button>
        </form>
    </div>

    <div class="card">
        <h3>RWS（Read/Write/Search）成員查詢</h3>
        <form method="GET" action="{{ route('dashboard') }}">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="輸入成員名稱">
            <button type="submit">查詢</button>
        </form>
        <ul>
            @foreach($members as $member)
                <li>{{ $member->name }}（推薦人：{{ $member->sponsor?->name ?? '無' }}）</li>
            @endforeach
        </ul>
    </div>

    <h3>帳務明細</h3>
    <table>
        <thead>
        <tr><th>時間</th><th>招募</th><th>公司收入</th><th>獎金支出</th><th>分配</th></tr>
        </thead>
        <tbody>
        @foreach($events as $event)
            <tr>
                <td>{{ $event->created_at }}</td>
                <td>{{ $event->recruiter->name }} 招收 {{ $event->newMember->name }}</td>
                <td>{{ number_format($event->company_income) }}</td>
                <td>{{ number_format($event->bonus_payout_total) }}</td>
                <td>
                    @forelse($event->bonusPayouts as $bonus)
                        {{ $bonus->member->name }}：{{ number_format($bonus->amount) }}<br>
                    @empty
                        無
                    @endforelse
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
