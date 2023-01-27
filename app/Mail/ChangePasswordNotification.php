<?php 
 
namespace App\Mail; 
 
use Illuminate\Bus\Queueable; 
use Illuminate\Mail\Mailable; 
use Illuminate\Queue\SerializesModels; 
use Illuminate\Contracts\Queue\ShouldQueue; 
 
/** 
 * パスワード変更 リマインダーメール 
 */ 
class ChangePasswordNotification extends Mailable 
{ 
    use Queueable, SerializesModels; 
 
    /** 
     * Create a new message instance. 
     * 
     * @return void 
     */ 
    public function __construct($name, $text) 
    { 
        // 
        // $this->title = sprintf('%sさん、ありがとうございます。', $name); 
        $title = '【' . (( session()->get('db_accesspoint_now', '0') == 2) ? config('const.TitleName2') : config('const.TitleName')) . '】パスワード設定完了のお知らせ'; 
        # グランデータではタイトルはこちら
        if (config('const.ViewThame')[0] == 'views_himawari') {
            $title = "【グランデータマイページ】パスワード設定完了のお知らせ";
        }
        $this->title = $title;
        $this->text = $text; 
    } 
 
    /** 
     * Build the message. 
     * 
     * @return $this 
     */ 
    public function build() 
    { 
        return $this->view('email.password_change') 
        ->text('email.password_change_plain') 
        ->subject($this->title) 
        ->with([ 
            "service_name" => config('const.TitleName'), 
            'text' => $this->text
          ]); 
        // return $this->view('view.name'); 
    } 
}
