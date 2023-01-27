$(function(){
    // 処理を中断する
    $('#execute-usage').on('click', function(){
      
      alert('取込を開始しました。※必ずOKボタンを押してください。');
      location.href = '/admin/upload/usagedata';
      // setTimeout(function(){
      //   console.log('処理を中断');
      // },5000);
    });

    // 処理を中断する
    $('#execute-billing').on('click', function(){
      
      alert('取込を開始しました。※必ずOKボタンを押してください。');
      location.href = '/admin/upload/billingdata';
      // setTimeout(function(){
      //   console.log('処理を中断');
      // },5000);
    });
    
    // 処理を中断する
    $('#execute-meisai').on('click', function(){
      
      alert('取込を開始しました。※必ずOKボタンを押してください。');
      location.href = '/admin/upload/meisaidata';
      // setTimeout(function(){
      //   console.log('処理を中断');
      // },5000);
    });
});
