@section('title')积分提取 @endsection

@extends('pcview::layouts.default')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/pc/css/account.css')}}"/>
@endsection

@section('content')

<div class="pay-box">
    <div class="pay-title">
        <h1 class="title">积分提取</h1>
        <span id="open">
            <svg class="icon" aria-hidden="true"><use xlink:href="#icon-doubt"></use></svg>
            积分提取规则
        </span>
    </div>
    <div class="pay-form">

        <p class="tcolor">积分兑换余额比例</p>
        <p><font color="#FF9400">{{$currency['recharge-ratio'] * 100}}积分 = 1元</font></p>
        <p class="rules">输入需提取的积分，提取积分需官方审核，审核反馈请注意系统消息！</p>
        <p><input class="custom-sum" type="text" name="sum" placeholder="请至少提取{{$currency['cash-min']}}积分"></p>

        <button class="pay-btn" id="J-pay-btn">确认</button>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    // 积分提取规则
    $('#open').on('click', function () {
        var html = '<div class="out">';
        html += '<div class="agreement">';
        html += '  <div class="title">';
        html += '  <h3>积分提取规则</h3>';
        html += '  </div>';
        html += '    <div class="agreement-info">';
        html += '    <p class="info">{{$currency['cash-rule']}}</p>';
        html += '</div>';
        html += ' </div>';
        html += '</div>';
        ly.loadHtml(html,'','520','440');
    });
$('#J-pay-btn').on('click', function(){

    var amount = $('[name="sum"]').val();
    if (amount == '') {
        $('[name="sum"]').focus();
        return false;
    }

    var params = {
        amount: amount
    };

    axios.post('/api/v2/currency/cash', params)
      .then(function (response) {
        noticebox('提取成功，请等待管理员审核', 1, "/account/currency/draw");
      })
      .catch(function (error) {
        showError(error.response.data);
      });
});


</script>
@endsection