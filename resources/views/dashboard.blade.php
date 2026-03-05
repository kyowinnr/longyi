@extends('layout')

@section('content')
    <div class="row" style="justify-content: space-between; align-items: center;">
        <h1>傳銷帳務系統</h1>
        <div class="row" style="align-items: center;">
            <a href="{{ route('members.network') }}">成員上下線關係</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">登出</button></form>
        </div>
    </div>

    @if(session('status'))
        <p style="color: green">{{ session('status') }}</p>
    @endif

    <div class="card">
        <p><strong>規則：</strong>預設招募人為「公司」；公司招收第一代不分獎金。若有上線則上線拿 5,000；若沒有上線（或上線為公司），5,000 回補給招募人。</p>
    </div>

    <div class="row">
        <div class="card"><strong>公司收入總額：</strong>{{ number_format($totalIncome) }}</div>
        <div class="card"><strong>獎金支出總額：</strong>{{ number_format($totalPayout) }}</div>
        <div class="card"><strong>公司淨額：</strong>{{ number_format($totalIncome - $totalPayout) }}</div>
    </div>

    <div class="card">
        <h3>新增招募（建立新成員）</h3>
        <form method="POST" action="{{ route('members.recruit') }}" class="row" style="align-items: end;">
            @csrf
            <div><label>招募人</label><br>
                <select name="recruiter_id" required>
                    <option value="">選擇招募人</option>
                    @foreach($members as $member)
                        <option value="{{ $member->id }}" @selected($member->id === $company->id)>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>姓名</label><br><input type="text" name="new_member_name" required></div>
            <div><label>身分證字號</label><br><input type="text" name="id_number" required></div>
            <div><label>生日</label><br><input type="date" name="birthday" required></div>
            <div><label>電話</label><br><input type="text" name="phone" required></div>
            <div><label>加入時間</label><br><input type="date" name="joined_at" required></div>
            <div><label>到期時間</label><br><input type="date" name="expires_at" required></div>
            <div style="min-width: 220px; flex: 1;"><label>帳務備註</label><br><textarea name="note" placeholder="可填寫該筆招募帳務備註"></textarea></div>
            <button type="submit">送出</button>
        </form>
    </div>

    <div class="card">
        <h3>成員查詢</h3>
        <form method="GET" action="{{ route('dashboard') }}" class="row">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="輸入成員名稱">
            <button type="submit">查詢</button>
        </form>
        <div class="row">
            @forelse($members as $member)
                <div class="card" style="min-width: 260px; margin: .3rem 0; flex: 1;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div style="font-size: 1.05rem; font-weight: 600;">{{ $member->name }}</div>
                        <div style="color:#555;">到期：{{ optional($member->expires_at)?->format('Y-m-d') ?? '未設定' }}</div>
                    </div>
                    @if($member->name !== '公司')
                        <details style="margin-top:.5rem;">
                            <summary>修改成員基本資料</summary>
                            <form method="POST" action="{{ route('members.update', $member) }}" class="row">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $member->name }}" required>
                                <input type="text" name="id_number" value="{{ $member->id_number }}" required>
                                <input type="date" name="birthday" value="{{ optional($member->birthday)?->format('Y-m-d') }}" required>
                                <input type="text" name="phone" value="{{ $member->phone }}" required>
                                <input type="date" name="joined_at" value="{{ optional($member->joined_at)?->format('Y-m-d') }}" required>
                                <input type="date" name="expires_at" value="{{ optional($member->expires_at)?->format('Y-m-d') }}" required>
                                <button type="submit">更新</button>
                            </form>
                        </details>
                    @endif
                </div>
            @empty
                <p>查無成員資料</p>
            @endforelse
        </div>
    </div>

    <h3>帳務明細</h3>
    <div class="table-wrap">
        <table>
            <thead>
            <tr><th>時間</th><th>招募</th><th>公司收入</th><th>獎金支出</th><th>分配</th><th>備註</th></tr>
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
                    <td style="min-width: 220px;">
                        <form method="POST" action="{{ route('events.note.update', $event) }}">
                            @csrf
                            @method('PUT')
                            <textarea name="note" placeholder="輸入備註...">{{ $event->note }}</textarea>
                            <button type="submit">儲存備註</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
