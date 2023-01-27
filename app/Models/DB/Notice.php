<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Models\DB\BaseModel;
use App\Models\DB\NoticeRelation;

/**
 * お知らせ モデル
 */
class Notice extends BaseModel
{
    use SoftDeletes;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'notice';
    protected $guarded = array('id');

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * お知らせ一覧を取得
     *
     * @param string $customerCode
     * @return array
     */
    public static function getList($customerCode, $limit = null) {
        $results = self::leftJoin('notice_relation', function ($join) {
                $join->on('notice_relation.notice_id', 'notice.id')
                    ->whereNull('notice_relation.deleted_at');
            })
            ->where('notice_date', '<=' , DB::raw('now()'))
            ->where(function($query) use($customerCode){
                $query->whereNull('notice_relation.customer_code')
                    ->orWhere('notice_relation.customer_code', $customerCode);
            });
        $totalCount = $results->count();
        $notices = [];
        if ($totalCount > 0) {
            if ($limit) {
                $results = $results->take($limit);
            }
            $notices = $results->orderBy('notice_date', 'desc')->get();
        }

        return $notices;
    }

}
