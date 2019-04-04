<table border="1">
    <tr>
        <td>用户名</td>
        <td>身份证号</td>
        <td>身份证照片</td>
        <td>接口用途</td>
        <td>状态</td>
    </tr>
    @foreach($data as $k=>$v)
        <tr>
            <td>{{$v['u_name']}}</td>
            <td>{{$v['u_num']}}</td>
            <td><img src="{{$v['num_img']}}" style="width: 50px; height: 50px;"></td>
            <td>{{$v['u_content']}}</td>
            @if($v['status']=='0')
                <td><a href="/month/audit_do?id={{$v['id']}}">通过</a>/<a href="/month/audit_no?id={{$v['id']}}">不通过</a></td>
            @elseif($v['status']=='1')
                <td>审核通过</td>
            @else
                <td>{{$v['status']}}</td>
            @endif
        </tr>
    @endforeach

</table>