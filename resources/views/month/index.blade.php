<form action="/month/user_info" method="post" enctype="multipart/form-data">
    <h2>接口申请</h2>
    <table border="1">
        <tr>
            <td>姓名</td>
            <td><input type="text" name="user_name"></td>
        </tr>
        <tr>
            <td>身份证号</td>
            <td><input type="text" name="user_num"></td>
        </tr>
        {{--<tr>--}}
            {{--<td>上传照片</td>--}}
            {{--<td><input type="file" name="user_img" id=""></td>--}}
        {{--</tr>--}}
        <tr>
            <td>接口用途</td>
            <td><input type="text" name="user_content"></td>
        </tr>
    </table>
    <input type="submit" value="提交申请">
</form>